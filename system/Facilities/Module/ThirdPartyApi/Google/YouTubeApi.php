<?php

namespace CodeHuiter\Facilities\Module\ThirdPartyApi\Google;

use CodeHuiter\Config\GoogleApiConfig;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Service\Language;
use CodeHuiter\Service\Logger;
use CodeHuiter\Service\Network;
use DateInterval;
use DateTime;
use Exception;

class YouTubeApi
{
    /**
     * @var GoogleApiConfig
     */
    private $config;
    /**
     * @var Network
     */
    private $network;
    /**
     * @var Language
     */
    private $lang;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var string
     */
    private $errorMessage = '';

    public function __construct(GoogleApiConfig $config, Network $network, Language $language, Logger $logger)
    {
        $this->config = $config;
        $this->network = $network;
        $this->lang = $language;
        $this->logger = $logger;
    }

    public function getVideoDataByUrl(string $url): ?YoutubeVideoData
    {
        $matches = [];
        if (!preg_match('/[.]*v=([^&]+)[.]*/i', $url, $matches)) {
            if (!preg_match('/[.]*youtu\.be\/([^&]+)[.]*/i', $url, $matches)) {
                preg_match('/[.]*youtube\.com\/embed\/([^&]+)[.]*/i', $url, $matches);
            }
        }
        if (!$matches || !isset($matches[1])) {
            $this->setErrorMessage($this->lang->get('google_api:incorrect_link'));
            return null;
        }
        return $this->getVideoData($matches[1]);
    }

    public function getVideoData(string $videoCode): ?YoutubeVideoData
    {
        if (!$videoCode) {
            $this->setErrorMessage($this->lang->get('google_api:no_video_code_got'));
            return null;
        }
        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoCode
            . '&key=' . $this->config->googleApiKey
            . '&part=snippet,contentDetails,statistics';
        $responseJson = $this->network->httpRequest($url, Network::METHOD_GET);
        if ($responseJson === 'Invalid id') {
            $this->setErrorMessage($this->lang->get('google_api:invalid_id'));
            return null;
        }
        $videoInfo = StringModifier::jsonDecode($responseJson);
        if (!$videoInfo || isset($videoInfo['error']) || isset($videoInfo['errors']) || !isset($videoInfo['items'])) {
            $this->setErrorMessage($this->lang->get('google_api:json_error'));
            $this->logger->withTag('YOUTUBE_API')->warning("Incorrect response json: $responseJson; Url: $url");
            return null;
        }

        $item = $videoInfo['items'][0] ?? [];
        if (!$item) {
            $this->setErrorMessage($this->lang->get('google_api:json_error'));
            $this->logger->withTag('YOUTUBE_API')->warning("Incorrect response json: $responseJson; Url: $url");
            return null;
        }

        return $this->parseData($item);
    }

    public function getVideosData(array $videoCodes): ?array
    {
        $result = [];
        $requestVideoCodes = [];
        foreach ($videoCodes as $index => $videoCode) {
            $requestVideoCodes[] = $videoCode;
            if (count($requestVideoCodes) > 45 || $index === count($videoCodes) - 1) {
                $requestResult = $this->getVideosDataInner($requestVideoCodes);
                if ($requestResult !== null) {
                    foreach ($requestResult as $requestResultItem) {
                        $result[] = $requestResultItem;
                    }
                }
                $requestVideoCodes = [];
            }
        }
        return $result;
    }

    /**
     * @param string[] $videoCodes
     * @return YoutubeVideoData[]|null
     */
    public function getVideosDataInner(array $videoCodes): ?array
    {
        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . implode(',', $videoCodes)
            . '&key=' . $this->config->googleApiKey
            . '&part=snippet,contentDetails,statistics';
        $responseJson = $this->network->httpRequest($url, Network::METHOD_GET);
        if ($responseJson === 'Invalid id') {
            $this->setErrorMessage($this->lang->get('google_api:invalid_id'));
            $this->logger->withTag('YOUTUBE_API')->warning('Incorrect response json: ' . $responseJson);
            return null;
        }
        $videoInfo = StringModifier::jsonDecode($responseJson);
        if (!$videoInfo || isset($videoInfo['error']) || isset($videoInfo['errors']) || !isset($videoInfo['items'])) {
            $this->setErrorMessage($this->lang->get('google_api:json_error'));
            $this->logger->withTag('YOUTUBE_API')->warning("Incorrect response json: $responseJson; Url: $url");
            return null;
        }

        $items = $videoInfo['items'] ?? [];
        if (!is_array($items)) {
            $this->setErrorMessage($this->lang->get('google_api:json_error'));
            $this->logger->withTag('YOUTUBE_API')->warning("Incorrect response json: $responseJson; Url: $url");
            return null;
        }
        $result = [];
        foreach ($items as $item) {
            $data = $this->parseData($item);
            if ($data === null) {
                $this->logger->withTag('YOUTUBE_API')->warning('Cant parse response : ' . StringModifier::jsonEncode($item));
            } else {
                $result[] = $data;
            }
        }
        return $result;
    }

    private function parseData(array $data): ?YoutubeVideoData
    {
        $videoId = $data['id'] ?? '';
        if (!$videoId) {
            $this->setErrorMessage($this->lang->get('google_api:json_error'));
            $this->logger->withTag('YOUTUBE_API')->warning('Cant get video info. response json: ' . StringModifier::jsonEncode($data));
            return null;
        }

        $picture = $data['snippet']['thumbnails']['standard']['url'] ?? '';
        if (!$picture){
            $picture = $data['snippet']['thumbnails']['high']['url'] ?? '';
        }
        if (!$picture){
            $picture = $data['snippet']['thumbnails']['medium']['url'] ?? '';
        }
        if (!$picture){
            $picture = $data['snippet']['thumbnails']['default']['url'] ?? '';
        }

        return new YoutubeVideoData(
            $videoId,
            $data['snippet']['channelTitle'] ?? '',
            $data['snippet']['channelId'] ?? '',
            $data['snippet']['title'] ?? '',
            $data['snippet']['description'] ?? '',
            $this->dateIntervalStringToSeconds($data['contentDetails']['duration'] ?? ''),
            $picture,
            [],
            $data['statistics']['viewCount'] ?? 0,
            $data['statistics']['likeCount'] ?? 0,
            $data['statistics']['dislikeCount'] ?? 0,
            $data['statistics']['commentCount'] ?? 0
        );

    }

    private function dateIntervalStringToSeconds(string $duration): int
    {
        try {
            $start = new DateTime('@0'); // Unix epoch
            $start->add(new DateInterval($duration));
            return $start->getTimestamp();
        } catch(Exception $ex) {
            return 0;
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    private function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }
}
