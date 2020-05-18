<?php

namespace App\Module\ChromeExtension\Controller;

use App\Module\ChromeExtension\Model\YoutubeCacheModel;
use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Facilities\Module\ThirdPartyApi\ThirdPartyApiProvider;
use CodeHuiter\Modifier\StringModifier;

class Youtube_Controller extends \CodeHuiter\Facilities\Controller\Base\BaseController
{
    public function index(): void
    {
        //return $this->error404();
        echo 'Youtube Is Test index function';
    }

    public function get_videos_data(): void
    {
        $callback = $this->request->getGet('callback');
        $videoIds = $this->request->getGet('q');

        $videos = explode(',', $videoIds);
        $requestVideos = [];

        $repository = new RelationalModelRepository($this->app, new YoutubeCacheModel());

        $result = [];

        foreach ($videos as $video) {
            /** @var YoutubeCacheModel $cacheModel */
            $cacheModel = $repository->getById([$video]);
            if ($cacheModel === null) {
                $requestVideos[] = $video;
                continue;
            }
            $cacheTime = $this->date->getCurrentDateTime()->getTimestamp() - $this->date->timeStringToDateTime($cacheModel->updated_at)->getTimestamp();
            $cacheData = StringModifier::jsonDecode($cacheModel->data);
            if (
                ($cacheData['dislikeCount'] < 1 && $cacheTime > (60 * 60 * 24))
                || ($cacheData['dislikeCount'] < 100 && $cacheTime > (60 * 60 * 24 * 4))
                || $cacheTime > (60 * 60 * 24 * 14)) {
                $requestVideos[] = $video;
                continue;
            }
            $result[] = $cacheData;
        }

        $videosData = $this->getApiProvider()->getYoutubeApi()->getVideosData($requestVideos);
        if ($videosData === null) {
            echo $this->getApiProvider()->getYoutubeApi()->getErrorMessage();
            return;
        }
        foreach ($videosData as $videoData) {
            $cacheModel = $repository->getById([$videoData->getId()]);
            if ($cacheModel === null) {
                $cacheModel = YoutubeCacheModel::getEmpty();
            }
            $cacheModel->id = $videoData->getId();
            $cacheModel->data = StringModifier::jsonEncode($videoData->toArray());
            $repository->save($cacheModel);

            $result[] = $videoData->toArray();
        }
        $response = StringModifier::jsonEncode(['results' => $result]);
        echo "$callback($response)";

//        echo '<pre>';
//        print_r($result);
//        echo '</pre>';
//        echo "Youtube This is get method of test controller with data1 = $videoIds;";
    }

    private function getApiProvider(): ThirdPartyApiProvider
    {
        return $this->app->get(ThirdPartyApiProvider::class);
    }
}
