<?php

namespace CodeHuiter\Pattern\Modules\Auth\Models;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\Model;
use CodeHuiter\Modifiers\ArrayModifier;
use CodeHuiter\Modifiers\StringModifier;
use CodeHuiter\Pattern\Modules\Auth\AuthService;
use CodeHuiter\Pattern\Modules\Auth\Events\UserDeletingEvent;

class UserModel extends Model implements UserInterface
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

    /** @var int */
    protected $id;
    /** @var string */
    protected $login = '';
    /** @var string */
    protected $passhash = '';
    /** @var string */
    protected $sig = '';
    /** @var string  */
    protected $token = '';
    /** @var string */
    protected $regtime;
    /** @var int */
    protected $sigtime = 0;
    /** @var int */
    protected $lastact = 0;
    /** @var string json */
    protected $groups = '[]';
    /** @var string */
    protected $lastip = '';
    /** @var string */
    protected $timezone = '0';
    /** @var int */
    protected $has_picture = 0;
    /** @var int  */
    protected $picture_id = 0;
    /** @var string  */
    protected $picture_orig = '';
    /** @var string  */
    protected $picture = '';
    /** @var string  */
    protected $picture_preview = '';
    /** @var int  */
    protected $rating = 0;
    /** @var string */
    protected $email;
    /** @var int */
    protected $email_conf = 0;
    /** @var @deprecated (show social account - 1)*/
    protected $settings = 0;
    protected $vk_id = '';
    protected $vk_access_token = '';
    protected $fb_id = '';
    protected $gl_id = '';
    protected $tw_id = '';
    protected $od_id = '';
    protected $oauths = '{}';
    protected $skype_id = '';
    protected $name = '';
    protected $alias = '';
    protected $firstname = '';
    protected $lastname = '';
    protected $birthday = '1970-01-01';
    protected $gender = 0;
    protected $city = '';
    protected $longitude = -500;
    protected $latitude = -500;
    protected $about_me = '';
    protected $notifications_count = 0;
    protected $notifications_last = 0;
    protected $data_info = '{}';

    /** @var array|null */
    protected $dataInfoDecoded = null;
    /** @var array|null */
    protected $groupsDecoded = null;

    /** @inheritdoc */
    public function getId(): int
    {
        return (int)$this->id;
    }

    /** @inheritdoc */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /** @inheritdoc */
    public function getLogin(): string
    {
        return $this->login;
    }

    /** @inheritdoc */
    public function setLogin(string $login): void
    {
        $this->login = $login;
        $this->touch('login');
    }

    /** @inheritdoc */
    public function getEmail(): string
    {
        return $this->email;
    }

    /** @inheritdoc */
    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->touch('email');
    }

    /** @inheritdoc */
    public function getEmailConfirmed(): bool
    {
        return (bool)$this->email_conf;
    }

    /** @inheritdoc */
    public function setEmailConfirmed(bool $confirmed): void
    {
        $this->email_conf = (int)$confirmed;
        $this->touch('email_conf');
    }

    /** @inheritdoc */
    public function getPassHash(): string
    {
        return $this->passhash;
    }

    /** @inheritdoc */
    public function setPassHash(string $passHash): void
    {
        $this->passhash = $passHash;
        $this->touch('passhash');
    }

    /** @inheritdoc */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /** @inheritdoc */
    public function getSignature(): string
    {
        return $this->sig;
    }

    /** @inheritdoc */
    public function setSignature(string $signature): void
    {
        $this->sig = $signature;
        $this->touch('sig');
    }

    /** @inheritdoc */
    public function getSignatureTime(): int
    {
        return (int)$this->sigtime;
    }

    /** @inheritdoc */
    public function setSignatureTime(int $timestamp): void
    {
        $this->sigtime = $timestamp;
        $this->touch('sigtime');
    }

    /** @inheritdoc */
    public function getLastActive(): int
    {
        return (int)$this->lastact;
    }

    /** @inheritdoc */
    public function setLastActive(int $lastActive): void
    {
        $this->lastact = $lastActive;
        $this->touch('lastact');
    }

    /** @inheritdoc */
    public function getLastIp(): string
    {
        return $this->lastip;
    }

    /** @inheritdoc */
    public function setLastIp(string $ip): void
    {
        $this->lastip = $ip;
        $this->touch('lastip');
    }

    /**
     * @return array
     */
    public function getDataInfo(): array
    {
        if ($this->dataInfoDecoded === null) {
            $this->dataInfoDecoded = StringModifier::jsonDecode($this->data_info,true);
        }
        return $this->dataInfoDecoded;
    }

    /**
     * @param array $data
     */
    public function setDataInfo(array $data): void
    {
        $this->dataInfoDecoded = null;
        $newDataInfo = StringModifier::jsonEncode($data);
        if ($newDataInfo !== $this->data_info) {
            $this->touch('data_info');
        }
        $this->data_info = $newDataInfo;
    }

    public function isInGroup(int $groupCode): bool
    {
        $groups = $this->getGroups();
        if ($groups && is_array($groups)) {
            foreach($groups as $group) {
                if ($group === $groupCode) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getGroups(): array
    {
        if ($this->groupsDecoded === null) {
            $this->groupsDecoded = StringModifier::jsonDecode($this->groups);
        }
        return $this->groupsDecoded;
    }

    public function setGroups(array $groups, bool $withSave = true): void
    {
        sort($groups);

        $diff = ArrayModifier::diff($this->groupsDecoded, $groups);
        if (!$diff) {
            return;
        }

        // Saving
        $this->groups = StringModifier::jsonEncode($groups);
        $this->groupsDecoded = null;

        if ($withSave) {
            return;
        }
        $this->update(['groups' => $this->groups]);
    }

    public function addGroup(int $group): void
    {
        $groups = $this->getGroups();
        if (!in_array($group, $groups)) {
            $groups[] = $group;
            $this->setGroups($groups);
        }
    }

    public function removeGroup(int $group): void
    {
        $groups = $this->getGroups();
        $foundedKey = array_search($group,$groups);
        if ($foundedKey !== false) {
            unset($groups[$foundedKey]);
            $this->setGroups($groups);
        }
    }

    /**
     * TODO Subscribe to GroupsChangedEvent
     */
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

    /** @inheritdoc */
    public function saveUser(): UserInterface
    {
        $onlyTouched = true;
        if (!$this->id) {
            $dateService = self::getDateService();
            $this->regtime = $dateService->sqlTime($dateService->now);
            $onlyTouched = false;
        }
        /** @var self $user */
        $user = parent::save($onlyTouched);
        return $user;
    }

    /** @inheritdoc */
    public function deleteUser(): void
    {
        Application::getInstance()->fireEvent(new UserDeletingEvent($this));
        $this->delete();
    }
}
