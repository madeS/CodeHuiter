<?php

namespace CodeHuiter\Pattern\Module\Media\Controller;

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Pattern\Controller\Base\BaseController;
use CodeHuiter\Pattern\Module\Connector\ConnectableObject;
use CodeHuiter\Pattern\Module\Connector\ConnectorService;
use CodeHuiter\Pattern\Module\Media\MediaService;

class MediaController extends BaseController
{
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
}
