<?php

namespace CodeHuiter\Service;

interface Network
{
    public const METHOD_GET = 1;
    public const METHOD_POST = 2;
    public const METHOD_HEAD = 3;

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
    ): ?string;

    /**
     * Use this method only for debug, get info, logging. Structure may be change
     * @return array
     */
    public function getLastResponseInfo(): array;

    /**
     * Build headers from array('Accept' => 'application/json') to array('Accept: application/json')
     * @param array $keyValueArray
     * @return array
     */
    public function buildHeaders(array $keyValueArray): array;
}
