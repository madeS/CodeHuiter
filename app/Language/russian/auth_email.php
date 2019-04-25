<?php

$lang['auth_email:confirm_subject'] = 'Проверка E-mail на {#siteName}';
$lang['auth_email:confirm_body'] =
    'Пользователь нашего сайта {#login} при регистрации  указал этот E-mail в качестве своего.' . "\n"
    . 'Если это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:' . "\n"
    . '{#siteUrl}/auth/confirm_email?user_id={#userId}&token={#token}' . "\n"
    . 'Если это были не вы, то просто проигнорируйте это письмо';
$lang['auth_email:recovery_subject'] = 'Смена пароля на {#siteName}';
$lang['auth_email:recovery_body'] =
    'Кто-то на сайте {#siteName} запросил ссылку для смену пароля.' . "\n"
    . 'Если это были вы, перейдите по ссылке ниже для смены пароля:' . "\n"
    . '{#siteUrl}/auth/confirm_email?user_id={#userId}&token={#token}' . "\n"
    . 'Если это были не вы, то просто проигнорируйте это письмо';
