<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\Model;
use CodeHuiter\Modifier\ArrayModifier;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Module\Auth\AuthService;
use CodeHuiter\Pattern\Module\Auth\Event\UserDeletingEvent;

class UserModel extends Model implements UserInterface
{
    protected static $database = 'db'; // database_default config
    protected static $table = 'users';
    protected static $primaryFields = ['id'];
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
    protected $login = '';
    protected $passhash = '';
    protected $sig = '';
    protected $token = '';
    /** @var string */
    protected $regtime;
    protected $sigtime = 0;
    protected $lastact = 0;
    protected $groups = '[]';
    protected $lastip = '';
    protected $timezone = '0';
    protected $has_picture = 0;
    protected $picture_id = 0;
    protected $picture_orig = '';
    protected $picture = 'default/profile_nopicture.png';
    protected $picture_preview = 'default/profile_nopicture_preview.png';
    protected $rating = 0;
    protected $email = '';
    protected $email_conf = 0;
    /** TODO remove deprecated field */
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

    public function exist(): bool
    {
        return (int)$this->id > 0;
    }

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
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
        $this->touch('timezone');
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

    /** @inheritdoc */
    public function getNotificationsCount(): int
    {
        return $this->notifications_count;
    }

    /** @inheritdoc */
    public function setNotificationsCount(int $notifications_count): void
    {
        $this->notifications_count = $notifications_count;
        $this->touch('notifications_count');
    }

    /** @inheritdoc */
    public function getNotificationsLast(): int
    {
        return $this->notifications_last;
    }

    /** @inheritdoc */
    public function setNotificationsLast(int $notifications_last): void
    {
        $this->notifications_last = $notifications_last;
        $this->touch('notifications_last');
    }

    /** @inheritdoc */
    public function getPicturePreview(): string
    {
        return $this->picture_preview;
    }

    /** @inheritdoc */
    public function setPicturePreview(string $picture_preview): void
    {
        $this->picture_preview = $picture_preview;
        $this->touch('picture_preview');
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
            $this->update(['groups' => $this->groups]);
        }
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
        if (!$this->id) {
            $this->regtime = self::getDateService()->sqlTime();
        }
        /** @var self $user */
        $user = parent::save(true);
        return $user;
    }

    /** @inheritdoc */
    public function deleteUser(): void
    {
        Application::getInstance()->fireEvent(new UserDeletingEvent($this));
        $this->delete();
    }
}
