<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

use CodeHuiter\Database\RelationalModel;
use CodeHuiter\Modifier\ArrayModifier;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Module\Auth\AuthService;

class UserModel extends RelationalModel implements UserInterface
{
    protected $_table = 'users';
    protected $_databaseServiceKey = 'db';
    protected $_primaryFields = ['id'];
    protected $_autoIncrementField = 'id';

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
    protected $_dataInfoDecoded = null;
    /** @var array|null */
    protected $_groupsDecoded = null;

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
    }

    /**
     * @return string
     */
    public function getRegtime(): string
    {
        return $this->regtime;
    }

    /**
     * @param string $regtime
     */
    public function setRegtime(string $regtime): void
    {
        $this->regtime = $regtime;
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
    }



    /**
     * @return array
     */
    public function getDataInfo(): array
    {
        if ($this->_dataInfoDecoded === null) {
            $this->_dataInfoDecoded = StringModifier::jsonDecode($this->data_info,true);
        }
        return $this->_dataInfoDecoded;
    }

    /**
     * @param array $data
     */
    public function setDataInfo(array $data): void
    {
        $this->_dataInfoDecoded = null;
        $this->data_info = StringModifier::jsonEncode($data);
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
        if ($this->_groupsDecoded === null) {
            $this->_groupsDecoded = StringModifier::jsonDecode($this->groups, true);
        }
        return $this->_groupsDecoded;
    }

    public function setGroups(array $groups): void
    {
        sort($groups);

        $diff = ArrayModifier::diff($this->_groupsDecoded, $groups);
        if (!$diff) {
            return;
        }
        $this->groups = StringModifier::jsonEncode($groups);
        $this->_groupsDecoded = $groups;
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
}
