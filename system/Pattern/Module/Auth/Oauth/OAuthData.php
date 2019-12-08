<?php

namespace CodeHuiter\Pattern\Module\Auth\Oauth;

class OAuthData
{
    public function getOriginSource(): string
    {
        return '';
    }

    public function getOriginId(): string
    {
        return '';
    }

    public function getFirstName(): string
    {
        return '';
    }

    public function getLastName(): string
    {
        return '';
    }

    public function getAsArray(): array
    {
        return [
            'originSource' => $this->getOriginSource(),
            'originId' => $this->getOriginId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
        ];
    }
}

