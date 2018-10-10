<?php

$lang['mauth.auth:incorrect_id'] = 'Пользователь не найден';
$lang['mauth.auth:incorrect_sig'] = 'Срок действия сигнатуры истёк, войдите на сайт заново';
$lang['mauth.auth:incorrect_ip'] = 'Ваш IP был изменён';
$lang['mauth.login:incorrect_logemail'] = 'Пользователь с таким Логином или Email\'ом не найден';
$lang['mauth.login:incorrect_password'] = 'Неверный пароль';
$lang['mauth.login:email_not_confirm:check_email'] = 'Ваш Email не подтверждён, на ваш email отправлено письмо с ссылкой для подтверждения';
$lang['mauth.login:user_not_active'] = 'Пользователь не активирован';
$lang['mauth.token:not_created'] = 'Что-то не так, токен не создался';
$lang['mauth.token:is_incorrect'] = 'Неверный токен подтверждения';
$lang['mauth.cant_send_email'] = 'Невозможно отправить письмо';
$lang['mauth.reg.login_already_taken'] = 'Такой логин уже занят';
$lang['mauth.reg.incorrect_password'] = 'Неверный пароль';
$lang['mauth.reg.something_wrong.user_not_found'] = 'Ошибка. Зарегистрированный пользователь не найден';
$lang['mauth.token:already_email_confirmed'] = 'Этот E-mail уже подтверждён';
$lang['mauth.reg.success_logined'] = 'Вы уже были у нас зарегистрированы. Добро Пожаловать!';


$lang['mauth.auth.logemail_need'] = 'E-mail или Логин необходим';
$lang['mauth.auth.password_need'] = 'Пароль необходим';


$lang['mauth.email_token.title'] = 'На ваш E-mail было выслано письмо с ссылкой для подтверждения';
$lang['mauth.email_token.p1'] = 'Обычно письмо приходит в течении 1-5 минут. Иногда этот процесс может занять до нескольких часов';
$lang['mauth.email_token.p2'] = 'Если вам не пришло письмо, проверьте папку спам в вашем почтовом ящике или введите ваш E-mail и Пароль в поле авторизации, вам будет выслано новое письмо';

$lang['mauth.pass_token.title'] = 'На ваш E-mail было выслано письмо с ссылкой для изменения пароля';
$lang['mauth.pass_token.p1'] = 'Обычно письмо приходит в течении 1-5 минут. Иногда этот процесс может занять до нескольких часов';
$lang['mauth.pass_token.p2'] = 'Если вам не пришло письмо, проверьте папку спам в вашем почтовом ящике или введите ваш E-mail в поле авторизации и нажмите &quot;Забыли пароль&quot; ещё раз, вам будет выслано новое письмо';

$lang['mauth.create_new_pass.fail.title'] = 'Ссылка недействительна';
$lang['mauth.create_new_pass.fail.p1'] = 'Сссылка восстановления пароля недействительна, возможно она устарела. Сгенерируйте новую ссылку';


$lang['mauth.banned.attention'] = 'Внимание!';
$lang['mauth.banned.title'] = 'На вашем аккаунте была замечена подозрительная активность, и он был заблокирован.';
$lang['mauth.banned.p1'] = '{#a_tag_open}На этой странице{#a_tag_close} вы можете ознакомиться с причинами блокировки и попробовать её оспорить.';

$lang['mauth.email_token.broken_link.title'] = 'Подтверждение E-mail неудачно.';
$lang['mauth.email_token.broken_link.p1'] = 'В ссылке отсутствуют необходимые параметры';


$lang['mauth.email_token.incorrect_link.title'] = 'Подтверждение E-mail неудачно.';
$lang['mauth.email_token.incorrect_link.p1'] = 'Причина: ';

$lang['mauth.ePattern.checkemail.subject'] = 'Проверка E-mail';
$lang['mauth.ePattern.checkemail.p11'] = 'Пользователь нашего сайта';
$lang['mauth.ePattern.checkemail.p12'] = 'при регистрации  указал этот E-mail в качестве своего.';
$lang['mauth.ePattern.checkemail.p2'] = 'Если это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:';
$lang['mauth.ePattern.checkemail.p4'] = 'Если это были не вы, то просто проигнорируйте это письмо';

$lang['mauth.recpass.email_need'] = 'Укажите ваш E-mail';
$lang['mauth.recpass.email_not_found'] = 'Пользователь с таким E-mail не найден';

$lang['mauth.ePattern.recpass.subject'] = 'Восстановление пароля';
$lang['mauth.ePattern.recpass.p11'] = 'Один из пользователей сайта';
$lang['mauth.ePattern.recpass.p12'] = 'запросил ссылку восстановления пароля';
$lang['mauth.ePattern.recpass.p2'] = 'Если это были вы, перейдите по ссылке ниже для установки нового пароля:';
$lang['mauth.ePattern.recpass.p4'] = 'Если это были не вы, то просто проигнорируйте это письмо';

$lang['mauth.recover.need_more_params'] = 'Недостаточно параметров';
$lang['mauth.recover.need_old_password'] = 'Необходимо вписать старый пароль';
$lang['mauth.recover.need_password'] = 'Необходимо вписать новый пароль';
$lang['mauth.recover.need_password_conf'] = 'Необходимо повторить введённый пароль';
$lang['mauth.recover.incorrect_password_conf'] = 'Подтверждение пароля не совпадает';
$lang['mauth.recov.empty_password'] = 'Пустой пароль';

$lang['mauth.max_photos_loaded'] = 'Достигнут максимальный предел загруженных фотографий. Удалите пару старых фото';
$lang['mauth.picture_not_found'] = 'Изображение не найдено';
$lang['mauth.access_denied'] = 'Доступ запрещён';
$lang['mauth.picture_already_as_default'] = 'Изображение уже установлено как главное';

$lang['mauth:user_banned'] = 'Пользователь заблокирован';
$lang['mauth:user_not_active'] = 'Пользователь не активирован';
$lang['mauth.join:user_banned'] = 'Присоединяемый аккаунт заблокирован';
$lang['mauth.join:another_user_find'] = 'Найден другой профиль с привязанным этим социальным аккаунтом. Сначала войдите под ним и отвяжите этот аккаунт в настройках.';
$lang['mauth.join:save_login_ability'] = 'Для того, чтобы сохранить возможность входа в профиль, привяжите "Email/Пароль"';
$lang['mauth.join:save_login_ability_email_conf'] = 'Необходимо подтвердить E-mail';

$lang['mauth:u_banned'] = 'Блокирован';
$lang['mauth:u_disactive'] = 'Деактивирован';
