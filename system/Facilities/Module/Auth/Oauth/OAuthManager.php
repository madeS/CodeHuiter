<?php

namespace CodeHuiter\Facilities\Module\Auth\Oauth;

interface OAuthManager
{
    public function addPermission(array $permissions): void;

    public function getSourceAccessLink(): string;

    public function login(array $getParams): ?OAuthData;

    public function getLastErrorMessage(): string;
}

