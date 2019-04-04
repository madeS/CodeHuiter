<?php

namespace CodeHuiter\Pattern\Services;

use CodeHuiter\Config\LinksConfig;
use CodeHuiter\Config\PatternConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Exceptions\InvalidFlowException;
use CodeHuiter\Pattern\Modules\Auth\Models\UserInterface;
use CodeHuiter\Pattern\Modules\Auth\Models\UserRepositoryInterface;

class Links
{
    /** @var Application */
    protected $app;

    /** @var LinksConfig  */
    protected $config;

    /** @var UserRepositoryInterface */
    protected $userRepository;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
        $this->config = $application->config->linksConfig;
    }

    /**
     * @return UserRepositoryInterface
     */
    private function getUserRepository(): UserRepositoryInterface
    {
        return $this->app->get(PatternConfig::SERVICE_USER_REPOSITORY);
    }

    /**
     * @return string
     */
    public function main()
    {
        return '/';
    }

    public function users()
    {
        return $this->url('users');
    }

    /**
     * @param UserInterface $user
     * @return null|string|string[]
     */
    public function user($user)
    {
        return $this->url('user_view',$user->getId());
    }

    public function userSettings($type = '')
    {
        return $this->url('user_settings');
    }

    /**
     * @param string $type
     * @param bool $userOrRoom
     * @return null|string|string[]
     * @throws InvalidFlowException
     */
    public function messages($type = '', $userOrRoom = false)
    {
        if ($type && $userOrRoom){
            if ($type === 'user'){
                return $this->url('messages_user',$userOrRoom['id']);
            } elseif ($type === 'room') {
                return $this->url('messages_room',$userOrRoom['id']);
            }
        } else {
            return $this->url('messages');
        }
        throw new InvalidFlowException('Unknown type for message url');
    }

    /**
     * @param UserInterface|bool $user
     * @return null|string|string[]
     */
    public function userMedias($user = false){
        if ($user){
            return $this->url('user_medias',$user->getId());
        } else {
            return $this->url('user_medias');
        }
    }

    /**
     * @param UserInterface|bool $user
     * @return null|string|string[]
     */
    public function userAlbums($user = false)
    {
        if ($user){
            return $this->url('user_albums',$user->getId());
        } else {
            return $this->url('users_albums');
        }
    }

    public function userAlbum($album)
    {
        return $this->url('user_album',$album['user']['id'],$album['id']);
    }

    public function userAlbumEdit($album)
    {
        return $this->url('user_album_edit',$album['user']['id'],$album['id']);
    }

    /**
     * @param UserInterface|bool $user
     * @return null|string|string[]
     */
    public function userVideos($user = false)
    {
        if ($user){
            return $this->url('user_videos', $user->getId());
        } else {
            return $this->url('users_videos');
        }
    }

    /**
     * @param $type
     * @param $object_id
     * @return null|string|string[]
     * @throws InvalidFlowException
     */
    public function notification($type, $object_id)
    {
        if ($type === 'new_subscriber'){
            $user = $this->getUserRepository()->newInstance();
            $user->setId($object_id);
            return $this->user($user);
        }
        if ($type === 'new_message') {
            $room = array('id' => $object_id);
            return $this->messages('room', $room);
        }
        return 'UNKNOWN_NOTIFICATION_LINK';
    }


    public function blogAdd()
    {
        return $this->url('blog_add');
    }

    public function blogEdit($blog)
    {
        return $this->url('blog_edit',$blog['id']);
    }

    public function blogPage($blog)
    {
        if ($blog['alias'] == 'main'){
            return $this->main();
        }
        if ($blog['parent_id']) {
            // Это подстраница
            $par1 = ($blog['alias']) ? $blog['alias'] : $blog['id'] ;
            $par2 = ($blog['parent']['alias']) ? $blog['parent']['alias'] : $blog['parent']['id'] ;
            return $this->url('blog_page_categored',$par2,$par1);
        }
        if ($blog['alias']) {
            return $this->url('blog_page',$blog['alias']);
        } else {
            return $this->url('blog_page',$blog['id']);
        }
    }

    protected function url()
    {
        $args = func_get_args();
        if (!count($args)) return '[NO ARGS]';
        if(!(isset($this->config->aliases[$args[0]]) && $this->config->aliases[$args[0]])){
            return '[ALIAS NO EXIST]';
        }
        $ret = $this->config->aliases[$args[0]];
        for ($i = 1; $i < count($args); $i++){
            $ret = preg_replace('/{#param}/', $args[$i], $ret, 1);
        }
//        if ($this->app_properties['urls_enable'] === TRUE){
//            $this->load->library('site_urls');
//            return $this->site_urls->getUrl($ret);
//        }
        return $ret;
    }
}
