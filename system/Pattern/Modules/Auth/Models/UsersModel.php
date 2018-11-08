<?php

namespace CodeHuiter\Pattern\Modules\Auth\Models;

use CodeHuiter\Database\Model;
use CodeHuiter\Modifiers\ArrayModifier;
use CodeHuiter\Modifiers\StringModifier;
use CodeHuiter\Pattern\Modules\Auth\AuthService;

class UsersModel extends Model
{
    protected static $database = 'db'; // database_default config
    protected static $table = 'users';
    protected static $primaryKeys = ['id'];
    protected static $fields = [
        'id',
        'login',
        'passhash',
        'sig',
        'token',
        'regtime',
        'sigtime',
        'lastact',
        'groups',
        'lastip',
        'timezone',
        'has_picture',
        'picture_id',
        'picture_orig',
        'picture',
        'picture_preview',
        'rating',
        'email',
        'email_conf',
        'settings',
        'vk_id',
        'vk_access_token',
        'fb_id',
        'gl_id',
        'tw_id',
        'od_id',
        'oauths',
        'skype_id',
        'name',
        'alias',
        'firstname',
        'lastname',
        'birthday',
        'gender',
        'city',
        'longitude',
        'latitude',
        'about_me',
        'notifications_count',
        'notifications_last',
        'data_info',
    ];

    public $id;
    public $login;
    public $passhash;
    public $sig;
    public $token;
    public $regtime;
    public $sigtime;
    public $lastact;
    protected $groups;
    public $lastip;
    public $timezone;
    public $has_picture;
    public $picture_id;
    public $picture_orig;
    public $picture;
    public $picture_preview;
    public $rating;
    public $email;
    public $email_conf;
    public $settings;
    public $vk_id;
    public $vk_access_token;
    public $fb_id;
    public $gl_id;
    public $tw_id;
    public $od_id;
    public $oauths;
    public $skype_id;
    public $name;
    public $alias;
    public $firstname;
    public $lastname;
    public $birthday;
    public $gender;
    public $city;
    public $longitude;
    public $latitude;
    public $about_me;
    public $notifications_count;
    public $notifications_last;
    public $data_info;


    protected $dataInfoDecoded = null;
    protected $groupsDecoded = null;


    public static function createNewUser($email, $login, $passhash)
    {
        $date = self::getDateService();
        $id = self::insert([
            'email' => $email,
            'email_conf' => 0,
            'login' => $login,
            'passhash' => $passhash,
            'regtime' => $date->sqlTime(null),
            'lastact' => $date->now,
            'oauths' => '{}',
            'about_me' => '',
            'data_info' => '{}',
        ]);
        //return self::getOneWhere(['id' => $id]);
    }

    /**
     * @return array
     */
    public function getDataInfo()
    {
        if ($this->dataInfoDecoded === null) {
            $this->dataInfoDecoded = StringModifier::jsonDecode($this->data_info,true);
        }
        return $this->dataInfoDecoded;
    }

    /**
     * @param array $data
     */
    public function updateDataInfo(array $data)
    {
        $this->dataInfoDecoded = null;
        $this->data_info = StringModifier::jsonEncode($data);
        $this->update(['data_info' => $this->data_info]);
    }

    public function isInGroup($requiredGroup)
    {
        $groups = $this->getGroups();
        if ($groups && is_array($groups)) {
            foreach($groups as $group) {
                if ($group === $requiredGroup) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getGroups()
    {
        if ($this->groupsDecoded === null) {
            $this->groupsDecoded = StringModifier::jsonDecode($this->groups);
        }
        return $this->groupsDecoded;
    }

    public function setGroups(array $groups, $withSave = true)
    {
        sort($groups);

        $diff = ArrayModifier::diff($this->groupsDecoded, $groups);
        if (!$diff) {
            return false;
        }

        // Saving
        $this->groups = StringModifier::jsonEncode($groups);
        $this->groupsDecoded = null;

        if ($withSave) {
            return false;
        }
        $this->update(['groups' => $this->groups]);
        $this->groupsChanged($diff);
    }

    public function addGroup($group)
    {
        $groups = $this->getGroups();
        if (!in_array($group, $groups)) {
            $groups[] = $group;
            $this->setGroups($groups);
        }
    }

    public function removeGroup($group)
    {
        $groups = $this->getGroups();
        $foundedKey = array_search($group,$groups);
        if ($foundedKey !== false) {
            unset($groups[$foundedKey]);
            $this->setGroups($groups);
        }
    }

    protected function groupsChanged($diff)
    {
        if (
            in_array(AuthService::GROUP_NOT_BANNED, $diff['added'])
            || in_array(AuthService::GROUP_NOT_DELETED, $diff['added'])
        ) {
            // @todo restore default photo
        }

        if (in_array(AuthService::GROUP_NOT_BANNED, $diff['removed'])) {
            // @todo set banned photo
        }

        if (in_array(AuthService::GROUP_NOT_DELETED, $diff['removed'])) {
            // @todo set deleted photo
        }

    }

}
