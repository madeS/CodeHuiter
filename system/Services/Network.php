<?php

namespace CodeHuiter\Services;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Log\AbstractLog;

class Network
{
    public const METHOD_GET = 1;
    public const METHOD_POST = 2;
    public const METHOD_HEAD = 3;

    private const FAKE_AGENT = [
        'headers' => [

        ],
        'options' => [
            'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        ],
    ];

    /**
     * @var AbstractLog
     */
    protected $log;

    public function __construct(Application $application)
    {
        $this->log = $application->get(Config::SERVICE_KEY_LOG);
    }

    /**
     * Информация о заголовках последнего запрсоа curlRequest
     * header, cookie
     * @var array
     */
    public $lastResponseInfo = array('header' => '','cookie' => []);

    /**
     * Выполняет запрос к URL через curl.
     * Позволяет сохранять cookie между запросами.
     * Заголовок и куки сохраняются в $this->lastResponseInfo.
     * @param string $url URL
     * @param string $method Method
     * @param array $post_data Для POST array(key => value)
     * @param array $additionalHeaders Дополнительные заголовки array('Accept:application/json');
     * @param array $options
     * <br/><b>fake</b> - true - Fake browser emulating
     * <br/><b>saveCookie</b> - true - Сохраняет полностью заголовоки
     * <br/><b>cookie</b> - будут установлены для запроса (обычно берётся из $this->lastResponseInfo['cookie'])
     * <br/><b>useragent</b> - UserAgent
     * <br/><b>no_redirects</b> - No redirect
     * <br/><b>referer</b> - Referer
     * <br/><b>timeout</b> - Timeout
     * @return string|null Тело ответа
     */
    public function httpRequest(
        string $url,
        string $method = self::METHOD_GET,
        array $post_data = [],
        array $additionalHeaders = [],
        array $options = []
    ): ?string {
        if (isset($options['fake'])) {
            $options = array_merge(self::FAKE_AGENT['options'], $options);
            $additionalHeaders = array_merge(self::FAKE_AGENT['headers'], $additionalHeaders);
        }

        $saveHeader = (isset($options['saveCookie'])) ? true : false;
        $cookie = '';
        if (isset($options['cookie']) && is_array($options['cookie'])){
            foreach ($options['cookie'] as $key => $value) {
                $cookie .= $key.'='.$value.'; ';
            }
        }
        if ($method === self::METHOD_HEAD) {
            $saveHeader = true;
        }
        $secure = (strpos($url, 'https:') === 0) ? true : false;
        try {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $options['timeout'] ?? 10);
            if ($saveHeader) {
                curl_setopt($ch, CURLOPT_HEADER,true);
            } else {
                curl_setopt($ch, CURLOPT_HEADER,0);
            }
            if ($cookie){
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            }
            if (!empty($options['referer'])){
                curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
            }
            if (isset($options['encoding']) && $options['encoding'] == 'gzip'){
                curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            }
            if ($method == self::METHOD_POST) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            if ($method == self::METHOD_HEAD) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'HEAD');
                curl_setopt($ch, CURLOPT_NOBODY, true);
            }

            if ($secure) {
                if (!empty($options['ssl'])){
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
                    curl_setopt($ch, CURLOPT_CAINFO, $options['ssl']);
                } else {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                }
            }


            $useragent = $options['useragent'] ?? 'Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.17';
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
            if (!empty($options['no_redirects'])){
                @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            } else {
                @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            }

            if ($additionalHeaders) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $additionalHeaders);
            }
            $verbose = null;
            if (!empty($options['debug'])){
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $verbose = fopen('php://temp', 'w+');
                curl_setopt($ch, CURLOPT_STDERR, $verbose);
                //$type = curl_multi_getcontent($ch)
            }
            $responseCURL = curl_exec($ch);
            $this->lastResponseInfo['info'] = curl_getinfo($ch);
            if (!empty($options['debug'])){
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                $this->lastResponseInfo['debug'] = $verboseLog;
            }
            if ($saveHeader){
                $header = substr($responseCURL,0,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
                $responseCURL = substr($responseCURL,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
                preg_match_all("/Set-Cookie: (.*?)=(.*?);/i",$header,$res);
                $receiveCookies = $res[1] ?? [];
                foreach ($receiveCookies as $key => $value) {
                    $this->lastResponseInfo['cookie'][$value] = $res[2][$key];
                }
                $this->lastResponseInfo['header'] = $header;
            }
            curl_close($ch);
            return $responseCURL;
        } catch (\Throwable $ex) {
            $this->log->warning('Curl Connection Error: ' . $ex->getMessage(), $ex);
            return null;
        }
    }

    /**
     * Build headers from array('Accept' => 'application/json') to array('Accept: application/json')
     * @param array $keyValueArray
     * @return array
     */
    public function buildHeaders(array $keyValueArray): array
    {
        $result = [];
        foreach ($keyValueArray as $key => $value) {
            $result[] = $key . ': ' . $value;
        }
        return $result;
    }
}
