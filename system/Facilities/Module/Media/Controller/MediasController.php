<?php

namespace CodeHuiter\Facilities\Module\Media\Controller;

use CodeHuiter\Config\Facilities\Module\ConnectorConfig;
use CodeHuiter\Core\Response;
use CodeHuiter\Facilities\Controller\Base\BaseController;
use CodeHuiter\Facilities\Module\Auth\Model\UserRepository;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectorService;
use CodeHuiter\Facilities\Module\Media\MediaService;
use CodeHuiter\Facilities\Module\Media\SearchList\MediaSearcher;

class MediasController extends BaseController
{
    public function media_list(int $userId): void
    {
        $this->initWithAuth(false);

        $user = $this->getUserRepository()->getById($userId);
        if (!$user) {
            $this->errorPageByCode(Response::HTTP_CODE_NOT_FOUND);
            return;
        }
        $this->data['user'] = $user;

        $this->data['uri'] = $this->links->user($user);
        $this->data['breadcrumbs'] = [
            ['url' => $this->links->users(), 'name' => $this->lang->get('user:users')],
            ['url' => $this->links->user($user), 'name' => $this->userService->getPresentName($user)],
            ['name' => $this->lang->get('users:medias')],
        ];

        $mediaSearcher = new MediaSearcher($this->app);
        $result = $mediaSearcher->search(
            ['object_type' => ConnectorConfig::TYPE_PROFILE, 'user_id' => $user->getId(), 'order' => 'id DESC'],
            $mediaSearcher->acceptFilters($this->request, ['show' => '']),
            $mediaSearcher->acceptPages($this->request, 36),
            true
        );
        $this->acceptSearchListResult($result, 'userMedias');

        $this->render($this->app->config->mediaConfig->viewsPath . 'userMediasTemplate');
    }

    public function album_list(int $userId): void
    {

    }

    public function popup_photos_upload(): void
    {
        if (!$this->initWithAuth(true)) return;

        $object = $this->getPhotoConnectObject();
        if ($object === null) return;

        $this->data['object'] = $object;
        $this->data['as_default'] = ($this->request->getPost('as_default')) ? true : false;

        $this->ajaxResponse->openPopupWithData(
            $this->renderer->render($this->app->config->mediaConfig->viewsPath . 'popupPhotosUploadTemplate', $this->data, true),
            'popupPhotosUpload',
            ['maxWidth' => 400, 'padding_hor' => 0, 'padding_ver' => 0,'close' => true,]
        )->render($this->response);
    }

    public function photos_upload(): void
    {
        if (!$this->initWithAuth(true)) return;

        $object = $this->getPhotoConnectObject();
        if ($object === null) return;

        $file = $this->request->getFile('user_media');
        if ($file->getError()) {
            $this->ajaxResponse->errorMessage($this->lang->get($file->getError(), ['file' => $file->getName()]))->render($this->response);
            return;
        }

        $mediaService = $this->getMediaService();
        $result = $mediaService->addPhoto($this->auth->getCurrentUser(), $file->getTmpFile(), $object, $file->getName());
        if (!$result->isSpecific()) {
            $this->ajaxResponse->errorMessage($this->lang->get($result->getMessage()))->render($this->response);
            return;
        }

        if ($object->getConnectorType() === ConnectorConfig::TYPE_PROFILE && $this->request->getPost('as_default')) {
            $mediaService->updateUserPhoto($this->auth->getCurrentUser(), $result->getFields()['media']);
        }

        $this->ajaxResponse->successMessage($this->lang->get('media:photo_success_upload', ['file' => $file->getName()]))->render($this->response);
    }

    private function getPhotoConnectObject(): ?ConnectableObject
    {
        $connector = $this->getConnectObjectService();
        $accessibility = $connector->getConnectAccessibility();
        $connectIdentity = $this->request->getPost('object_identity');
        $object = $connector->getConnectableObjectByIdentity($connectIdentity);
        if (!$object) {
            $this->ajaxResponse->errorMessage($this->lang->get('connector:object_not_found'))->render($this->response);
            return null;
        }
        if ($accessibility->canAddTo(ConnectorConfig::TYPE_PHOTO, $object, $this->auth->getCurrentUser()) !== true) {
            $this->ajaxResponse->errorMessage($this->lang->get('connector:you_dont_have_access_to_connect_to_this_object'))->render($this->response);
            return null;
        }
        return $object;
    }

    private function getConnectObjectService(): ConnectorService
    {
        return $this->app->get(ConnectorService::class);
    }

    private function getMediaService(): MediaService
    {
        return $this->app->get(MediaService::class);
    }

    private function getUserRepository(): UserRepository
    {
        return $this->app->get(UserRepository::class);
    }
}
