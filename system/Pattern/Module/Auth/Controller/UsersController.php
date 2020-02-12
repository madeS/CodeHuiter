<?php

namespace CodeHuiter\Pattern\Module\Auth\Controller;

use CodeHuiter\Core\Response;
use CodeHuiter\Pattern\Controller\Base\BaseController;
use CodeHuiter\Pattern\Module\Auth\Model\UserRepository;
use CodeHuiter\Pattern\Module\Auth\SearchList\UserSearcher;

class UsersController extends BaseController
{
    /**
     * @return void
     */
    public function index(): void
    {
        $this->initWithAuth(false);
        $this->data['uri'] = $this->links->users();
        $this->data['breadcrumbs'] = [['name' => $this->lang->get('user:users')]];

        $userSearcher = new UserSearcher($this->app);
        $result = $userSearcher->search(
            [],
            $userSearcher->acceptFilters($this->request, ['show' => 'random']),
            $userSearcher->acceptPages($this->request, 40),
            true
        );
        $this->acceptSearchListResult($result, 'users');

        $this->render($this->app->config->authConfig->viewsPath . 'usersTemplate');
    }

    public function get(string $id = ''): void
    {
        $this->initWithAuth(false);

        /** @var UserRepository $userRepository */
        $userRepository = $this->app->get(UserRepository::class);
        $user = $userRepository->getById($id);
        if (!$user) {
            $this->errorPageByCode(Response::HTTP_CODE_NOT_FOUND);
            return;
        }

        $this->data['uri'] = $this->links->user($user);
        $this->data['breadcrumbs'] = [
            ['url' => $this->links->users(), 'name' => $this->lang->get('user:users')],
            ['name' => $this->userService->getPresentName($user)],
        ];
        $this->data['user'] = $user;

        $this->render($this->app->config->authConfig->viewsPath . 'userViewTemplate');
    }

    public function settings(): void
    {
        if (!$this->initWithAuth(true)) {
            return;
        }
        $this->data['uri'] = $this->links->userSettings();
        $this->data['breadcrumbs'] = array(
            ['url' => $this->links->users(), 'name' => $this->lang->get('user:users')],
            ['url' => $this->links->user($this->auth->user), 'name' => $this->userService->getPresentName($this->auth->user)],
            ['name' => 'Настройки'],
        );

        $this->data['userDataInfoFields'] = $this->userService->getUserDataInfoFields();
        $this->data['user'] = $this->auth->user;

        $this->render($this->app->config->authConfig->viewsPath . 'userSettingsTemplate');
    }

    public function user_edit_submit(): void
    {
        if (!$this->initWithAuth(true)) return;
        $postData = $this->request->getPostAsArray();
        $dataFields = $this->userService->getUserDataInfoFields();
        $validatorConfig = [
            'name' => ['required' => false, 'max_length' => 250, 'filters' => ['trim']],
            'firstname' => ['required' => false, 'max_length' => 250, 'filters' => ['trim']],
            'lastname' => ['required' => false, 'max_length' => 250, 'filters' => ['trim']],
            'gender' => ['required' => false, 'filters' => ['trim']],
            'birthday_day' => ['required' => false, 'filters' => ['trim']],
            'birthday_month' => ['required' => false, 'filters' => ['trim']],
            'birthday_year' => ['required' => false, 'filters' => ['trim']],
            'city' => ['required' => false, 'max_length' => 250, 'filters' => ['trim']],
            'about_me' => ['required' => false, 'max_length' => 5000, 'filters' => ['trim']],
            'allow_show_social' => ['required' => false],
        ];
        $additionalFieldsValidatorConfig = [];
        foreach ($dataFields as $dataFieldKey => $dataFieldConfig) {
            $additionalFieldsValidatorConfig[$dataFieldKey] = $dataFieldConfig['validation'];
        }
        $validatorConfig = array_merge($validatorConfig, $additionalFieldsValidatorConfig);
        $data = $this->validator->validate($postData, $validatorConfig, $this->ajaxResponse);
        if (!$data) { $this->ajaxResponse->render($this->response); return; }
        $this->userService->setUserInfo($this->auth->user, $data);

        $this->ajaxResponse->successMessage($this->lang->get('user:settings.user_info_changed'))
            ->reload()->closePopups()->render($this->response);
    }
}
