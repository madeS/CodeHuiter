<?php

namespace CodeHuiter\Facilities\Module\Auth\Oauth;

class OAuthData
{
    public const GENDER_UNKNOWN = 0;
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    /**
     * @var string
     */
    private $originSource;
    /**
     * @var string
     */
    private $originId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $profilePhoto;
    /**
     * @var string
     */
    private $birthday;
    /**
     * @var int
     */
    private $gender;
    /**
     * @var string|null
     */
    private $accessToken;
    /**
     * @var array
     */
    private $additionalData;

    public function __construct(
        string $originSource,
        string $originId,
        string $name,
        string $firstName,
        string $lastName,
        string $profilePhoto,
        string $birthday,
        int $gender,
        array $additionalData
    ) {
        $this->originSource = $originSource;
        $this->originId = $originId;
        $this->name = $name;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->profilePhoto = $profilePhoto;
        $this->birthday = $birthday;
        $this->gender = $gender;
        $this->additionalData = $additionalData;
    }

    public function getOriginSource(): string
    {
        return $this->originSource;
    }

    public function getOriginId(): string
    {
        return $this->originId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getProfilePhoto(): string
    {
        return $this->profilePhoto;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }

    public function getGender(): int
    {
        return $this->gender;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getAsArray(): array
    {
        return [
            'originSource' => $this->getOriginSource(),
            'originId' => $this->getOriginId(),
            'name' => $this->getName(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'profilePhoto' => $this->getProfilePhoto(),
            'birthday' => $this->getBirthday(),
            'gender' => $this->getGender(),
            'accessToken' => $this->getAccessToken(),
            'additionalData' => $this->additionalData,
        ];
    }
}
