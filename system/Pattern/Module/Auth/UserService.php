<?php

namespace CodeHuiter\Pattern\Module\Auth;

use CodeHuiter\Core\Application;
use CodeHuiter\Exception\InvalidFlowException;
use CodeHuiter\Modifier\IntModifier;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Module\Auth\Model\User;
use CodeHuiter\Pattern\Module\Auth\Model\UserRepository;
use CodeHuiter\Pattern\Service\ValidatedData;
use CodeHuiter\Service\DateService;
use CodeHuiter\Service\Language;

class UserService
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getUserDataInfoFields(): array
    {
        return [
            'requiredFieldKey' => [
                'type' => 'string',
                'name' => 'additionalFields:requiredFieldKey',
                'validation' => [
                    'required' => false,
                ],
            ]
        ];
    }

    public function getPresentName(User $user): string
    {
        if (!$user->isInGroup(AuthService::GROUP_NOT_DELETED)) {
            return $this->getLanguage()->get('user:inactive');
        }
        if (!$user->isInGroup(AuthService::GROUP_NOT_BANNED)) {
            return $this->getLanguage()->get('user:banned');
        }
        if ($user->getName()) {
            return $user->getName();
        }
        $fistLastName = trim($user->getFirstName() . ' ' . $user->getLastName());
        if ($fistLastName) {
            return $fistLastName;
        }
        if ($user->getLogin()) {
            return $user->getLogin();
        }
        return '"User #'.$user->getId();
    }

    public function isOnline(User $user): bool
    {
        $dateService = $this->getDateService();
        $inactiveTime = $dateService->getCurrentTimestamp() - $user->getLastActive();
        return $inactiveTime < $this->getOnlineTime();
    }

    public function equal(User $user1, User $user2): bool
    {
        return $user1->getId() === $user2->getId();
    }

    public function isModerator(User $user): bool
    {
        if (!$user->getId()) {
            return false;
        }
        return $user->isInGroup(AuthService::GROUP_MODERATOR);
    }

    public function isBanned(User $user): bool
    {
        if (!$user->getId()) {
            return false;
        }
        return !$user->isInGroup(AuthService::GROUP_NOT_BANNED);
    }

    public function isActive(User $user): bool
    {
        if (!$user->getId()) {
            return false;
        }
        return $user->isInGroup(AuthService::GROUP_NOT_BANNED)
            && $user->isInGroup(AuthService::GROUP_NOT_DELETED)
            && $user->isInGroup(AuthService::GROUP_ACTIVE);
    }

    public function getAge(User $user): int
    {
        return $this->getDateService()->diffDateTime($user->getBirthday())->y;
    }

    public function setUserInfo(User $user, ValidatedData $data): User
    {
        if (!$user->getId()) {
            throw new InvalidFlowException('Cant edit not exist user');
        }
        if ($data->hasField('name')) {
            $user->setName($data->getField('name'));
        }
        if ($data->hasField('firstname')) {
            $user->setFirstName($data->getField('firstname'));
        }
        if ($data->hasField('lastname')) {
            $user->setLastName($data->getField('lastname'));
        }
        if ($data->hasField('gender')
            && in_array($data->getField('gender'), [0 ,User::GENDER_MALE, User::GENDER_FEMALE], false)
        ) {
            $user->setGender((int)$data->getField('gender'));
        }
        if ($data->hasField('city')) {
            $user->setCity($data->getField('city'));
        }
        if ($data->hasField('about_me')) {
            $user->setAboutMe($data->getField('about_me'));
        }
        if ($data->hasField('birthday_year') && $data->hasField('birthday_month') && $data->hasField('birthday_day')) {
            $birthday = "{$data->getField('birthday_year')}-{$data->getField('birthday_month')}-{$data->getField('birthday_day')}";
            try {
                $user->setBirthday(
                    $this->getDateService()->dateTimeToTimeString($this->getDateService()->timeStringToDateTime($birthday))
                );
            } catch (\RuntimeException $exception) {}

        }
        if ($data->hasField('timezone')) {
            $timezone = IntModifier::normalizeBetween((int)$data->getField('timezone'), -11, 12);
            $timezone = StringModifier::mbStrPad((string)$timezone,2,'0',STR_PAD_LEFT);
            if ($timezone > 0) {
                $timezone = '+' . $timezone;
            }
            $timezone .= ':00';
            $user->setTimezone($timezone);
        }
        if ($data->hasField('allow_show_social')) {
            $dataInfo = $user->getDataInfo();
            $dataInfo['show_social_accounts'] = IntModifier::normalizeBetween((int)$data->getField('allow_show_social'));
            $user->setDataInfo($dataInfo);
        }
        $fields = $this->getUserDataInfoFields();
        foreach ($fields as $fieldKey => $options) {
            if ($data->hasField($fieldKey)) {
                $dataInfo = $user->getDataInfo();
                if ($options['type'] === 'string' && $data->getField($fieldKey) !== '') {
                    $dataInfo['info'][$fieldKey] = $data->getField($fieldKey);
                }
                $user->setDataInfo($dataInfo);
            }
        }
        $this->getUserRepository()->save($user);
        return $user;
    }

    public function isAllowShowSocial(User $user): bool
    {
        return $user->getDataInfo()['show_social_accounts'];
    }

    private function getLanguage(): Language
    {
        return $this->application->get(Language::class);
    }

    private function getDateService(): DateService
    {
        return $this->application->get(DateService::class);
    }

    private function getOnlineTime(): int
    {
        return $this->application->config->authConfig->onlineTime;
    }

    private function getUserRepository(): UserRepository
    {
        return $this->application->get(UserRepository::class);
    }
}

