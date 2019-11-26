<?php

namespace CodeHuiter\Pattern\Module\Auth\Model;

interface UserInterface
{
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    public function exist(): bool;

    public function getId(): int;

    public function setId(int $id): void;

    public function getLogin(): string;

    public function setLogin(string $login): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getFirstName(): string;

    public function setFirstName(string $firstname): void;

    public function getLastName(): string;

    public function setLastName(string $lastname): void;

    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getEmailConfirmed(): bool;

    public function setEmailConfirmed(bool $confirmed): void;

    public function getPassHash(): string;

    public function setPassHash(string $passHash): void;

    /**
     * TODO rename timezone offset
     */
    public function getTimezone(): string;

    public function setTimezone(string $timezone): void;

    public function getSignature(): string;

    public function setSignature(string $signature): void;

    public function getSignatureTime(): int;

    public function setSignatureTime(int $timestamp): void;

    public function getLastActive(): int;

    public function setLastActive(int $lastActive): void;

    public function getLastIp(): string;

    public function setLastIp(string $ip): void;

    public function getNotificationsCount(): int;

    public function setNotificationsCount(int $notifications_count): void;

    public function getNotificationsLast(): int;

    public function setNotificationsLast(int $notifications_last): void;

    public function getPictureId(): ?int;

    public function getPicture(): string;

    public function getPicturePreview(): string;

    public function setPicturePreview(string $picture_preview): void;

    public function getAboutMe(): string;

    public function setAboutMe(string $about_me): void;

    public function getSocialId(string $socialType): ?string;

    public function getOauthData(): array;

    public function setSocialId(string $socialType, string $socialId): void;

    public function getGender(): int;

    public function setGender(int $gender): void;

    public function getBirthday(): string;

    public function setBirthday(string $birthday): void;

    public function getCity(): string;

    public function setCity(string $city): void;

    public function getDataInfo(): array;

    public function setDataInfo(array $data): void;

    public function isInGroup(int $groupCode): bool;

    /**
     * @param int[] $groups
     */
    public function setGroups(array $groups): void;

    public function addGroup(int $group): void;

    public function removeGroup(int $group): void;

    /**
     * @return int[]
     */
    public function getGroups(): array;

}
