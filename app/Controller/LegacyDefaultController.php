<?php

namespace App\Controller;

use CodeHuiter\Pattern\Module\Auth\AuthService;

/**
 * @property-read \CodeHuiter\OldFrameworkAdapter\Service\Mauth $mauth
 * @property-read \CodeHuiter\OldFrameworkAdapter\Service\Mm $mm
 */
class LegacyDefaultController extends DefaultController
{
    protected function initWithAuth($require, $requiredGroups = [
        AuthService::GROUP_NOT_BANNED,
        AuthService::GROUP_ACTIVE,
    ], $customActions = [])
    {

        if (isset($this->data['ui']) && $this->data['ui']['id']) return true;
        $this->load->model(array('mm','mauth'));
        $this->load->model($this->mm->models);
        $ret = $this->authUser($require);
        if ($require && !$ret) return false;

        foreach($this->mm->models as $model){
            $this->{$model}->setUi($this->data['ui']);
        }

        return $ret;
    }

    protected function authUser($required = false){
        if ($required){
            $this->data['ui'] = $this->mauth->getUiOrAuth();
            if (!$this->data['ui']) return false;
        } else {
            $this->data['ui'] = $this->mauth->getUiWhatever();
        }
        $this->appm->setUi($this->data['ui']);
        return true;
    }

}
