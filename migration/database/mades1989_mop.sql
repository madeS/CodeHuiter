-- phpMyAdmin SQL Dump @deprecated
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 07 2017 г., 12:57
-- Версия сервера: 5.5.52-38.3
-- Версия PHP: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mades1989_mop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `blogs`
--

CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `is_category` tinyint(4) NOT NULL DEFAULT '0',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `place_type` varchar(50) NOT NULL DEFAULT 'unknown',
  `place_order` int(11) NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `data_info` text NOT NULL,
  `comments_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_up_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_down_count` int(11) NOT NULL DEFAULT '0',
  `seo_title` varchar(1000) NOT NULL DEFAULT '',
  `seo_keywords` varchar(1000) NOT NULL DEFAULT '',
  `seo_description` text NOT NULL,
  `created_at` int(11) UNSIGNED NOT NULL,
  `updated_at` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `blogs`
--

INSERT INTO `blogs` (`id`, `active`, `is_category`, `parent_id`, `place_type`, `place_order`, `user_id`, `alias`, `title`, `content`, `data_info`, `comments_count`, `thumbs_up_count`, `thumbs_down_count`, `seo_title`, `seo_keywords`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 0, 'topmenu', 0, 50, 'main', 'Главная', '<strong>CI&nbsp;</strong><strong>MOP (CodeIgniter Mades Online Pattern)</strong>&nbsp; &mdash; это архитектурное решение на базе фрэймворка CodeIgniter для разработки сайтов любой сложности.<br /><br />Дополнительно к фреймворку реализовано:<br />- Сниппеты, для разносторонних задач, необходимых при разработке сайта<br />- Поддержка mjsa библиотеки (ajax навигация, формы, ошибки и сообщения)<br />- CSS решения.<br />- Компрессия JS и CSS файлов (пока собирает и отдает одним файлом. Минимизацию на лету ещё не делал)<br />- Очередь рассылки писем<br />- Авторизация (поддержка 4 социальных сетей vk, fb, google, twitter), настройки пользователя, блокировка бользователей<br />- Загрузка медиа для пользователя (фотографии, видеозаписи youtube vimeo vk, архивы zip)<br />- Фидбэк<br />- Система уведомлений пользователя<br />- Диалоги<br />- Блог<br />- Комментарии к любым объектам<br />- Лайки, дизлайки к любым объектам<br />- YaShare<br /><br />Реализовать в будущем:<br />- Бэкаппер (автоматический бэкап сайта)<br />- Крон решения разных интервалов на основе поминутного крона<br />- Каркас для интернет-магазинов (товары, категории, заказы)<br />- Чат-диалоги<br /><br />', '', 4, 0, 0, '', '', '', 1454413219, 1454415373),
(2, 1, 0, 0, 'sidemenu', 0, 50, '', 'Доп страница', 'Доп страница&nbsp;<br />Видео на странице {#media37}&nbsp;<br />Картинка на странице&nbsp;{#media38}<br />Архив на странице&nbsp;{#media39}<br /><br />Другой контент прикрепленный к странице:', '', 0, 0, 0, '', '', '', 1454416691, 1454418137),
(3, 1, 0, 0, 'topmenu', 0, 50, '', 'Тест страница 1', 'Контент', '', 0, 0, 0, '', '', '', 1482075144, 1482075192);

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `guest_name` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(50) NOT NULL DEFAULT 'none',
  `object_id` int(10) UNSIGNED DEFAULT '0',
  `subobject_id` int(10) UNSIGNED DEFAULT '0',
  `message` text NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `parents_string` varchar(255) NOT NULL DEFAULT '',
  `parent_level` int(11) NOT NULL DEFAULT '0',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  `thumbs_up_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_down_count` int(11) NOT NULL DEFAULT '0',
  `complain_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `active` (`active`),
  KEY `parents_string` (`parents_string`)
) ENGINE=MyISAM AUTO_INCREMENT=905 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `active`, `user_id`, `guest_name`, `ip`, `object_type`, `object_id`, `subobject_id`, `message`, `parent_id`, `parents_string`, `parent_level`, `created_at`, `updated_at`, `thumbs_up_count`, `thumbs_down_count`, `complain_count`) VALUES
(898, 2, 50, '', '46.216.218.36', 'blog', 1, 0, 'Опа-па', 0, ':00000000898:', 0, 1454415406, 1454415570, 0, 1, 0),
(899, 2, 50, '', '46.216.218.49', 'blog', 1, 0, '12', 0, ':00000000899:', 0, 1454595155, 1454595256, 1, 0, 0),
(901, 2, 50, '', '46.216.218.49', 'blog', 1, 0, 'dsfsd', 0, ':00000000901:', 0, 1454595335, 1454595335, 1, 0, 0),
(904, 2, 50, '', '46.216.218.49', 'blog', 1, 0, 'dsfsd', 899, ':00000000899:00000000904:', 1, 1454595423, 1454595423, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dialogues`
--

CREATE TABLE IF NOT EXISTS `dialogues` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID получателя',
  `message` text NOT NULL,
  `isnew` tinyint(4) NOT NULL DEFAULT '1',
  `readed_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `profile_id` (`room_id`),
  KEY `isnew` (`isnew`)
) ENGINE=MyISAM AUTO_INCREMENT=1825 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dialogues_rooms`
--

CREATE TABLE IF NOT EXISTS `dialogues_rooms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('private','standart','public') NOT NULL DEFAULT 'standart',
  `name` varchar(255) NOT NULL DEFAULT '',
  `last_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_message` text NOT NULL,
  `count_total` int(11) NOT NULL DEFAULT '0',
  `count_new` int(11) NOT NULL DEFAULT '0',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`name`),
  KEY `isnew` (`count_new`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=1065 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `dialogues_rooms_invitations`
--

CREATE TABLE IF NOT EXISTS `dialogues_rooms_invitations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `room_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `from_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'приглашающий, если это начальный с двумя или для открытой комнаты то 0',
  `join_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `leave_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `readed_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `isnew` (`join_at`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1130 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `mailer`
--

CREATE TABLE IF NOT EXISTS `mailer` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `sended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `updated_at` (`updated_at`,`sended`),
  KEY `updated_at_2` (`updated_at`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `mailer`
--

INSERT INTO `mailer` (`id`, `user_id`, `subject`, `email`, `message`, `created_at`, `updated_at`, `sended`) VALUES
(9, 0, '[Mades Online Pattern] Восстановление пароля', 'mades1989@gmail.com', 'Один из пользователей сайта запросил ссылку восстановления пароля\nЕсли это были вы, перейдите по ссылке ниже для установки нового пароля:\nhttp://mop.bogarevich.com/auth/recovery_password_email?user_id=1&token=dd7df68a940c8c29fa46773e9f95e164  \nЕсли это были не вы, то просто проигнорируйте это письмо\n', 1454419956, 1454419956, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `mtasks`
--

CREATE TABLE IF NOT EXISTS `mtasks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `params` varchar(1000) NOT NULL DEFAULT '{}',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `group_name` (`group_name`,`name`,`updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title_h` varchar(2047) NOT NULL DEFAULT '',
  `the_text` text NOT NULL,
  `redirect_uri` varchar(255) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '0',
  `editable` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` (`id`, `type`, `alias`, `name`, `title_h`, `the_text`, `redirect_uri`, `priority`, `editable`) VALUES
(1, 'default', 'text_page1', '', 'Text page 1 H1', '<p>\n	Суть замыканий проста: внутри функции можно использовать все пременные, которые доступны в том месте, где функция была объявлена.<br />\n	<br />\n	Хотя идея замыканий проста, на практике зачастую возникает много непонятных моментов по поведению в том или ином случае. Так что для начала вспомним основы объявления переменной, а именно &ndash; &quot;переменные в JavaScript объявляются с помощью ключевого слова var&quot;:<br />\n	<br />\n	<strong>При запуске кода выведет текст &quot;Hello World&quot;, как и ожидалось. Суть происходящего проста &ndash; создаётся глобальная переменная title со значением &quot;Hello World&quot;, которое показывается пользователю с помощью alert-а.</strong> В данном примере, даже если мы опустим ключевое слово var, код всё равно сработает правильно из-за глобального контекста. Но об этом позже.<br />\n	<br />\n	Теперь попробуем объявить ту же переменную, но уже внутри функции:<br />\n	<br />\n	В результате запуска кода сгенерируется ошибка &quot;&#39;title&#39; is undefined&quot; &mdash; &quot;переменная &#39;title&#39; не была объявлена&quot;. <strong>Это происходит из-за механизма локальной области видимости переменных: все переменные, объявленные внутри фукнции являются локальными и видны только внутри этой функции. Или проще</strong>: если мы объявим какую-то переменную внутри функции, то вне этой функции доступа к этой переменной у нас не будет.<br />\n	<br />\n	Для того, чтобы вывести надпись &quot;Hello World&quot;, необходимо вызвать alert внутри вызываемой функции:<br />\n	<br />\n	<br />\n	<br />\n	<br />\n	Либо вернуть значение из функции:</p>\n', '', 0, 2),
(2, 'default', 'about', '', 'About H1', '<p>\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.</p>\n', '', 0, 2),
(3, 'default', 'our_team', '', 'our team', '<p>\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some POur team some text tOur teamSome text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some textOur teamip text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	SomeOur teamme teOur teamome text.Some text Our team is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some Our team there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some Our team some text there is some text.Some text some text there is some text.</p>\n', '', 0, 2),
(4, 'default', 'our_goals', '', 'Our goals', '<p>\n	Some text some text there is some text.Some text some text there is some text.<br />\n	How to Our goals some textHow to pay text some text there is some text.<br />\n	Some text some text there is some text.Our goals goals text some Our goals there is some text.<br />\n	Some How to pay text there is some text.Some text some text there Our goals some text.<br />\n	Some text some Our Our goals there is some text.How to pay text some text there is some text.<br />\n	Some text Our goals text there Our goals some text.Some text How Our goals pay text How to pay is some text.<br />\n	Some text some text there is some text.Some Our goals some text there is some text.<br />\n	Some text some text there is Our goals text.Some text some text there is some text.<br />\n	Thanks text.Our goals tex How Our goals pay some Our goals.Some Our goals to pay some text there is some text.<br />\n	Some text some text there is some text.Some Our goals some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some How to Our goals there is Our goals text.Some text some How to pay there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there Our goals some text.Some text some text there is some text.<br />\n	How to pay some text there is some text.Some Our goals some text there is some text.</p>\n', '', 0, 2),
(5, 'default', 'how_it_work', '', 'How its work???', '    Some text some text there is some text.\n	Some How it work some text there is some text.\n	Some text some text there is How it work text.\n	Some text some text there is some text.\n	Some text How it work text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some How it work some text How it work is some text.Some text some How it work there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text How it work text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some How it work some text there is some text.', '', 0, 2),
(6, 'default', 'how_to_pay', '', 'I need your money', '    Some text some text there is some text.Some text some text there is some text.\n	How to pays some textHow to pay text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some How to pay text there is some text.Some text some text there is some text.\n	Some text some text there is some text.How to pay text some text there is some text.\n	Some text some text there is some text.Some text How to pay text How to pay is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks text.Some tex How to pay some text.Some How to pay some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some How to pay there is some text.Some text some How to pay there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	How to pay some text there is some text.Some text some text there is some text.', '', 0, 2),
(7, 'default', 'partnership', '', 'my partner is lesbian', '    Some text some text there is some text.\n	Some Partnership some text there is some text.\n	Some text some text there isPartnership text.\n	Some text some text there is some text.\n	Some text Partnership text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some Partnershipk some text Partnerships some text.Some text somePartnershipere is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some textPartnershipxt there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some Partnership some text there is some text.', '', 0, 2),
(8, 'default', 'terms', '', 'Terms of blah', 'dsffsdfs', '', 0, 2),
(9, 'default', 'thanks', '', 'Thanks me', '    Some text some text there is some text.Some text some text there is some text.\n	SomeThanks some text tOur teamSome text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some Thanks text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks text.Some tex tThanksis some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some Thanks there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks some text there is some text.Some text some text there is some text.', '', 0, 2),
(10, 'default', 'contact_us', '', 'Contact us', '<img src="../pub/files/uploads/images/2010-geneva-babes-23.jpg" alt="dome" height="200" />Some text some text there is some text. Some Contact us some text there is some text. Some text some text there Contact us text. Some text some text there is some text. Some text Contact us text there is some text. Some text some text there is some text. Some text some text there is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us some text Contact us some text.Some text Contact us is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us there is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us some text there is some text.', '', 0, 2),
(11, 'default', 'cooperation', '', 'Cooperation', '<p>Some text some text there is some text.Some text some text there is some text. How Cooperation Our Cooperation some text</p>\n<p>How to Cooperation Cooperationhere is some text. Some text some text there is some text.Our Cooperation goals teCooperation goals there is some text. Some How to Cooperation text Cooperation is Cooperation text.Some text some text there Our goals some text. Some text some Our Our goals thCooperation text.How Cooperation some teCooperationre is some text. Cooperationtext some text there is some text.Some Our goals some text there is some text. Some text some tCooperationation How Our goals pay someCooperatione text there is some text. Some text some text there is some text.Some Our goals some Cooperation there is some text. Some text some text there is some text.Some text some tCooperation is some text. Some How to Cooperation Cooperation there is Our goals text.Some text some How to pay there is some text. Some text some text there is some text.Some text some texCooperatione text. Some text some text Cooperations some text.Some text some text there is some text. How to pay some text there is some text.Cooperation Our goals some text there is some text.</p>', '', 0, 2),
(12, 'default', 'blog', '', 'blog blog blog', '<p>\n	dfsfefs fe fs efs ef sf sef e f ef sef s e sfe sf sef se sef sef sf sef e ssf</p>\n', '', 0, 2),
(13, 'default', 'company', '', 'О компании', 'Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.\n<div>Some text some text there is some text.<img src="../pub/files/images/content/gallery/m0-20131112164358-medium-d2e683d095b13202b2a33d5aa4a6de39.jpg" alt="" width="300" /></div>\n<ol>\n<li>Some text some text there is some text.<br /> Some text some text there is some text.</li>\n<li><br /> Some text some text there is some text.<br /> Some text some text there is some text.Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.</li>\n</ol>', '', 0, 2),
(14, 'default', 'promo_maxturbo8', '', 'Супер низкая цена', 'цены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены <br />супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текст', '', 0, 2),
(15, 'default', 'promo_fusion8', '', 'Опа-па', '1. Если вы ищете один элемент, используйте поиск по идентификатору:<br /> $(\'#color_button\') &ndash; будет выполнен максимально быстро<br /> $(\'#content .button\') &ndash; будет медленнее<br /> $(\'.button:first\') &ndash; еще медленнее<br /> <br /> <span style="text-decoration: underline;">2. Если вы ищете группу элементов, указывайте ближайшего общего родственника,</span> обладающего идентификатором:<br /> $(\'#content input\') будет эффективнее, чем просто $(\'input\').<br /> <br />\n<div style="text-align: right;">3. Если вы ищете элементы по классу, указывайте имя тега:</div>\n<div style="text-align: right;">$(\'input.button\') выполнит поиск быстрее, чем $(\'.button\'). В первом случае, jQuery вначале найдет все элементы input, и уже среди них будет искать элементы с классом button. А во втором случае, для этого будет произведен перебор всех элементов страницы.</div>\n<br /> Из всего сказанного<em>, можно вывести два основных п</em>равила:\n<blockquote>\n<p>1) Для поиска одного элемента, используйте поиск по id: $(\'#someId\')</p>\n</blockquote>\n2) При поиске группы элементов, старайтесь придерживаться следующей формулы: $(\'#someId tagName.someClass\')<br /> <br /> <span style="text-decoration: line-through;">И еще, не пытайтесь улучшить поиск по id с помощью следующих комбинаций:<br /> $(\'tagName#someId\')<br /> $(\'#wrapId #someId\')<br /> Это только замедлит выполнение поиска.</span>', '', 0, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `site_params`
--

CREATE TABLE IF NOT EXISTS `site_params` (
  `the_key` varchar(255) NOT NULL,
  `the_value` text NOT NULL,
  `descript` varchar(1023) NOT NULL DEFAULT '',
  `editable` int(2) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`the_key`),
  KEY `editable` (`editable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `site_params`
--

INSERT INTO `site_params` (`the_key`, `the_value`, `descript`, `editable`) VALUES
('info_email', 'info@gilletteopt.by', 'E-Mail для связи', 1),
('info_phone', '+375 (29) 777-77-77\n', 'Телефон', 1),
('price-list', 'pricelist_2013-07-17_10-07.jpg', '', 0),
('order_email', 'ourtravelru@gmail.com', 'E-Mail для заказов', 1),
('test_param', 'default_value4', 'Тестовый параметр', 2),
('use_time', '1486294009', '', 0),
('marks', '[{"markName":"Acura","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Acura","markAlias":"acura","categoryAlias":"cars","parsed":"1"},{"markName":"Alfa Romeo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Alfa_Romeo","markAlias":"alfa_romeo","categoryAlias":"cars","parsed":"1"},{"markName":"Aston Martin","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Aston_Martin","markAlias":"aston_martin","categoryAlias":"cars","parsed":"1"},{"markName":"Audi","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Audi","markAlias":"audi","categoryAlias":"cars","parsed":"1"},{"markName":"Bentley","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Bentley","markAlias":"bentley","categoryAlias":"cars","parsed":"1"},{"markName":"BMW","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/BMW","markAlias":"bmw","categoryAlias":"cars","parsed":"1"},{"markName":"Brilliance","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Brilliance","markAlias":"brilliance","categoryAlias":"cars","parsed":"1"},{"markName":"Buick","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Buick","markAlias":"buick","categoryAlias":"cars","parsed":"1"},{"markName":"BYD","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/BYD","markAlias":"byd","categoryAlias":"cars","parsed":"1"},{"markName":"Cadillac","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Cadillac","markAlias":"cadillac","categoryAlias":"cars","parsed":"1"},{"markName":"Chana","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chana","markAlias":"chana","categoryAlias":"cars","parsed":"1"},{"markName":"Chery","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chery","markAlias":"chery","categoryAlias":"cars","parsed":"1"},{"markName":"Chevrolet","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chevrolet","markAlias":"chevrolet","categoryAlias":"cars","parsed":"1"},{"markName":"Chrysler","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chrysler","markAlias":"chrysler","categoryAlias":"cars","parsed":"1"},{"markName":"Citroen","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Citroen","markAlias":"citroen","categoryAlias":"cars","parsed":"1"},{"markName":"Dacia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Dacia","markAlias":"dacia","categoryAlias":"cars","parsed":"1"},{"markName":"Daewoo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Daewoo","markAlias":"daewoo","categoryAlias":"cars","parsed":"1"},{"markName":"Daihatsu","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Daihatsu","markAlias":"daihatsu","categoryAlias":"cars","parsed":"1"},{"markName":"Dodge","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Dodge","markAlias":"dodge","categoryAlias":"cars","parsed":"1"},{"markName":"Ferrari","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ferrari","markAlias":"ferrari","categoryAlias":"cars","parsed":"1"},{"markName":"Fiat","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Fiat","markAlias":"fiat","categoryAlias":"cars","parsed":"1"},{"markName":"Ford","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ford","markAlias":"ford","categoryAlias":"cars","parsed":"1"},{"markName":"Ford USA","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ford_USA","markAlias":"ford_usa","categoryAlias":"cars","parsed":"1"},{"markName":"Foton","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Foton","markAlias":"foton","categoryAlias":"cars","parsed":"1"},{"markName":"Geely","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Geely","markAlias":"geely","categoryAlias":"cars","parsed":"1"},{"markName":"GMC","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/GMC","markAlias":"gmc","categoryAlias":"cars","parsed":"1"},{"markName":"Great Wall","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Great_Wall","markAlias":"great_wall","categoryAlias":"cars","parsed":"1"},{"markName":"Hafei","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hafei","markAlias":"hafei","categoryAlias":"cars","parsed":"1"},{"markName":"Honda","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Honda","markAlias":"honda","categoryAlias":"cars","parsed":"1"},{"markName":"Hummer","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hummer","markAlias":"hummer","categoryAlias":"cars","parsed":"1"},{"markName":"Hyundai","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hyundai","markAlias":"hyundai","categoryAlias":"cars","parsed":"1"},{"markName":"Infiniti","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Infiniti","markAlias":"infiniti","categoryAlias":"cars","parsed":"1"},{"markName":"Isuzu","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Isuzu","markAlias":"isuzu","categoryAlias":"cars","parsed":"1"},{"markName":"JAC","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/JAC","markAlias":"jac","categoryAlias":"cars","parsed":"1"},{"markName":"Jaguar","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Jaguar","markAlias":"jaguar","categoryAlias":"cars","parsed":"1"},{"markName":"Jeep","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Jeep","markAlias":"jeep","categoryAlias":"cars","parsed":"1"},{"markName":"Kia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Kia","markAlias":"kia","categoryAlias":"cars","parsed":"1"},{"markName":"Lancia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lancia","markAlias":"lancia","categoryAlias":"cars","parsed":"1"},{"markName":"Land Rover","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Land_Rover","markAlias":"land_rover","categoryAlias":"cars","parsed":"1"},{"markName":"Lexus","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lexus","markAlias":"lexus","categoryAlias":"cars","parsed":"1"},{"markName":"Lifan","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lifan","markAlias":"lifan","categoryAlias":"cars","parsed":"1"},{"markName":"Lincoln","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lincoln","markAlias":"lincoln","categoryAlias":"cars","parsed":"1"},{"markName":"Maserati","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Maserati","markAlias":"maserati","categoryAlias":"cars","parsed":"1"},{"markName":"Maybach","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Maybach","markAlias":"maybach","categoryAlias":"cars","parsed":"1"},{"markName":"Mazda","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mazda","markAlias":"mazda","categoryAlias":"cars","parsed":"1"},{"markName":"Mercedes","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mercedes","markAlias":"mercedes","categoryAlias":"cars","parsed":"1"},{"markName":"MG","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/MG","markAlias":"mg","categoryAlias":"cars","parsed":"1"},{"markName":"MINI","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/MINI","markAlias":"mini","categoryAlias":"cars","parsed":"1"},{"markName":"Mitsubishi","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mitsubishi","markAlias":"mitsubishi","categoryAlias":"cars","parsed":"1"},{"markName":"Nissan","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Nissan","markAlias":"nissan","categoryAlias":"cars","parsed":"1"},{"markName":"Opel","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Opel","markAlias":"opel","categoryAlias":"cars","parsed":"1"},{"markName":"Peugeot","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Peugeot","markAlias":"peugeot","categoryAlias":"cars","parsed":"1"},{"markName":"Pontiac","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Pontiac","markAlias":"pontiac","categoryAlias":"cars","parsed":"1"},{"markName":"Porsche","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Porsche","markAlias":"porsche","categoryAlias":"cars","parsed":"1"},{"markName":"Proton","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Proton","markAlias":"proton","categoryAlias":"cars","parsed":"1"},{"markName":"Renault","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Renault","markAlias":"renault","categoryAlias":"cars","parsed":"1"},{"markName":"Rolls-Royce","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Rolls-Royce","markAlias":"rolls-royce","categoryAlias":"cars","parsed":"1"},{"markName":"Rover","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Rover","markAlias":"rover","categoryAlias":"cars","parsed":"1"},{"markName":"Saab","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Saab","markAlias":"saab","categoryAlias":"cars","parsed":"1"},{"markName":"Seat","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Seat","markAlias":"seat","categoryAlias":"cars","parsed":"1"},{"markName":"Skoda","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Skoda","markAlias":"skoda","categoryAlias":"cars","parsed":"1"},{"markName":"Smart","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Smart","markAlias":"smart","categoryAlias":"cars","parsed":"1"},{"markName":"Ssangyong","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ssangyong","markAlias":"ssangyong","categoryAlias":"cars","parsed":"1"},{"markName":"Subaru","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Subaru","markAlias":"subaru","categoryAlias":"cars","parsed":"1"},{"markName":"Suzuki","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Suzuki","markAlias":"suzuki","categoryAlias":"cars","parsed":"1"},{"markName":"Toyota","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Toyota","markAlias":"toyota","categoryAlias":"cars","parsed":"1"},{"markName":"Volkswagen","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Volkswagen","markAlias":"volkswagen","categoryAlias":"cars","parsed":"1"},{"markName":"Volvo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Volvo","markAlias":"volvo","categoryAlias":"cars","parsed":"1"},{"markName":"ВАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%92%d0%90%d0%97","markAlias":"vaz","categoryAlias":"cars","parsed":"1"},{"markName":"ГАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%93%d0%90%d0%97","markAlias":"gaz","categoryAlias":"cars","parsed":"1"},{"markName":"ЗАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%97%d0%90%d0%97","markAlias":"zaz","categoryAlias":"cars","parsed":"1"},{"markName":"Москвич","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%9c%d0%be%d1%81%d0%ba%d0%b2%d0%b8%d1%87","markAlias":"moskvich","categoryAlias":"cars","parsed":"1"},{"markName":"УАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%a3%d0%90%d0%97","markAlias":"uaz","categoryAlias":"cars","parsed":"1"},{"markName":"Avia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Avia","markAlias":"avia","categoryAlias":"trucks","parsed":"1"},{"markName":"DAF","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/DAF","markAlias":"daf","categoryAlias":"trucks","parsed":"1"},{"markName":"Fiat","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Fiat","markAlias":"fiat","categoryAlias":"trucks","parsed":"1"},{"markName":"Ford","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Ford","markAlias":"ford","categoryAlias":"trucks","parsed":"1"},{"markName":"Hyundai","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Hyundai","markAlias":"hyundai","categoryAlias":"trucks","parsed":"1"},{"markName":"Isuzu","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Isuzu","markAlias":"isuzu","categoryAlias":"trucks","parsed":"1"},{"markName":"Iveco","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Iveco","markAlias":"iveco","categoryAlias":"trucks","parsed":"1"},{"markName":"MAN","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/MAN","markAlias":"man","categoryAlias":"trucks","parsed":"1"},{"markName":"Mercedes","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Mercedes","markAlias":"mercedes","categoryAlias":"trucks","parsed":"1"},{"markName":"Mitsubishi","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Mitsubishi","markAlias":"mitsubishi","categoryAlias":"trucks","parsed":"1"},{"markName":"Neoplan","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Neoplan","markAlias":"neoplan","categoryAlias":"trucks","parsed":"1"},{"markName":"Nissan","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Nissan","markAlias":"nissan","categoryAlias":"trucks","parsed":"1"},{"markName":"Renault Trucks","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Renault_Trucks","markAlias":"renault_trucks","categoryAlias":"trucks","parsed":"1"},{"markName":"Scania","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Scania","markAlias":"scania","categoryAlias":"trucks","parsed":"1"},{"markName":"Setra","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Setra","markAlias":"setra","categoryAlias":"trucks","parsed":"1"},{"markName":"Van Hool","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Van_Hool","markAlias":"van_hool","categoryAlias":"trucks","parsed":"1"},{"markName":"Volkswagen","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Volkswagen","markAlias":"volkswagen","categoryAlias":"trucks","parsed":"1"},{"markName":"Volvo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Volvo","markAlias":"volvo","categoryAlias":"trucks","parsed":"1"},{"markName":"КАМАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/%d0%9a%d0%90%d0%9c%d0%90%d0%97","markAlias":"kamaz","categoryAlias":"trucks","parsed":"1"},{"markName":"МАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/%d0%9c%d0%90%d0%97","markAlias":"maz","categoryAlias":"trucks","parsed":"1"},{"markName":"Alfa Romeo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Alfa_Romeo","markAlias":"alfa_romeo","categoryAlias":"commercial","parsed":"1"},{"markName":"Avia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Avia","markAlias":"avia","categoryAlias":"commercial","parsed":"1"},{"markName":"Chevrolet","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Chevrolet","markAlias":"chevrolet","categoryAlias":"commercial","parsed":"1"},{"markName":"Citroen","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Citroen","markAlias":"citroen","categoryAlias":"commercial","parsed":"1"},{"markName":"Daewoo","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Daewoo","markAlias":"daewoo","categoryAlias":"commercial","parsed":"1"},{"markName":"DAF","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/DAF","markAlias":"daf","categoryAlias":"commercial","parsed":"1"},{"markName":"Daihatsu","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Daihatsu","markAlias":"daihatsu","categoryAlias":"commercial","parsed":"1"},{"markName":"Dodge","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Dodge","markAlias":"dodge","categoryAlias":"commercial","parsed":"1"},{"markName":"Fiat","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Fiat","markAlias":"fiat","categoryAlias":"commercial","parsed":"1"},{"markName":"Ford","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ford","markAlias":"ford","categoryAlias":"commercial","parsed":"1"},{"markName":"Ford USA","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ford_USA","markAlias":"ford_usa","categoryAlias":"commercial","parsed":"1"},{"markName":"Foton","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Foton","markAlias":"foton","categoryAlias":"commercial","parsed":"1"},{"markName":"GMC","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/GMC","markAlias":"gmc","categoryAlias":"commercial","parsed":"1"},{"markName":"Honda","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Honda","markAlias":"honda","categoryAlias":"commercial","parsed":"1"},{"markName":"Hyundai","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Hyundai","markAlias":"hyundai","categoryAlias":"commercial","parsed":"1"},{"markName":"Isuzu","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Isuzu","markAlias":"isuzu","categoryAlias":"commercial","parsed":"1"},{"markName":"Iveco","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Iveco","markAlias":"iveco","categoryAlias":"commercial","parsed":"1"},{"markName":"Kia","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Kia","markAlias":"kia","categoryAlias":"commercial","parsed":"1"},{"markName":"LDV","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/LDV","markAlias":"ldv","categoryAlias":"commercial","parsed":"1"},{"markName":"Mazda","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mazda","markAlias":"mazda","categoryAlias":"commercial","parsed":"1"},{"markName":"Mercedes","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mercedes","markAlias":"mercedes","categoryAlias":"commercial","parsed":"1"},{"markName":"Mitsubishi","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mitsubishi","markAlias":"mitsubishi","categoryAlias":"commercial","parsed":"1"},{"markName":"Nissan","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Nissan","markAlias":"nissan","categoryAlias":"commercial","parsed":"1"},{"markName":"Opel","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Opel","markAlias":"opel","categoryAlias":"commercial","parsed":"1"},{"markName":"Peugeot","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Peugeot","markAlias":"peugeot","categoryAlias":"commercial","parsed":"1"},{"markName":"Renault","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Renault","markAlias":"renault","categoryAlias":"commercial","parsed":"1"},{"markName":"Renault Trucks","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Renault_Trucks","markAlias":"renault_trucks","categoryAlias":"commercial","parsed":"1"},{"markName":"Ssangyong","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ssangyong","markAlias":"ssangyong","categoryAlias":"commercial","parsed":"1"},{"markName":"Suzuki","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Suzuki","markAlias":"suzuki","categoryAlias":"commercial","parsed":"1"},{"markName":"Toyota","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Toyota","markAlias":"toyota","categoryAlias":"commercial","parsed":"1"},{"markName":"Volkswagen","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Volkswagen","markAlias":"volkswagen","categoryAlias":"commercial","parsed":"1"},{"markName":"ГАЗ","url":"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/%d0%93%d0%90%d0%97","markAlias":"gaz","categoryAlias":"commercial","parsed":"1"}]', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `site_seo`
--

CREATE TABLE IF NOT EXISTS `site_seo` (
  `table_name` varchar(63) NOT NULL,
  `primary_value` varchar(127) NOT NULL,
  `title` varchar(2048) NOT NULL DEFAULT '',
  `description` varchar(2048) NOT NULL DEFAULT '',
  `keywords` varchar(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_name`,`primary_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `site_seo`
--

INSERT INTO `site_seo` (`table_name`, `primary_value`, `title`, `description`, `keywords`) VALUES
('pages', '15', 'SEO Title ', 'SEO Description   	', 'SEO Keywords  ');

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `private_level` tinyint(4) NOT NULL,
  `objects_count` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `temps`
--

CREATE TABLE IF NOT EXISTS `temps` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'file',
  `object` varchar(255) NOT NULL DEFAULT 'unknown',
  `to_object` varchar(255) NOT NULL DEFAULT 'unknown',
  `file` varchar(2047) NOT NULL DEFAULT '',
  `file_path` varchar(2047) NOT NULL DEFAULT '',
  `data` varchar(4095) NOT NULL DEFAULT '{}',
  `linked_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `thetime` datetime NOT NULL,
  `utime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uhash` varchar(49) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `thumbs`
--

CREATE TABLE IF NOT EXISTS `thumbs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(255) NOT NULL DEFAULT 'none',
  `object_id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 - down, 2 - up, -1 - complain',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `object_index` (`object_type`,`object_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1212 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `thumbs`
--

INSERT INTO `thumbs` (`id`, `user_id`, `ip`, `object_type`, `object_id`, `type`, `created_at`, `updated_at`) VALUES
(1207, 50, '46.216.218.49', 'comment', 901, 2, 1454584643, 1454584643),
(1210, 50, '46.216.218.49', 'comment', 898, 1, 1454584660, 1454584660),
(1211, 50, '80.249.93.6', 'comment', 899, 2, 1454595577, 1454595577);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL COMMENT 'логин',
  `passhash` varchar(255) NOT NULL DEFAULT '' COMMENT 'односторонняя функция с солью пароля',
  `sig` varchar(255) NOT NULL DEFAULT '' COMMENT 'сеансовый токен',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'для проверки email и др',
  `regtime` datetime NOT NULL COMMENT 'время регистрации',
  `sigtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'врямя установки сессии',
  `lastact` int(11) UNSIGNED NOT NULL COMMENT 'юникстайм времени последнего посищения',
  `groups` varchar(30) NOT NULL DEFAULT '[1]' COMMENT 'Группы пользователя [1-NotBanned, 2-NotDeleted, 3-Active ...]',
  `lastip` varchar(127) NOT NULL DEFAULT '' COMMENT 'последний ip',
  `timezone` varchar(7) NOT NULL DEFAULT '',
  `has_picture` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `picture_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `picture_orig` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture.png',
  `picture` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture.png',
  `picture_preview` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture_preview.png',
  `rating` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_conf` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `settings` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1-show_social_accounts',
  `vk_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Id vk',
  `vk_access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'only offline token if given',
  `fb_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'id facebook',
  `gl_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'id google',
  `tw_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'id twitter',
  `od_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'id odnoklassniki',
  `oauths` varchar(8181) NOT NULL DEFAULT '[]',
  `skype_id` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'редактируемое имя',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL DEFAULT '' COMMENT 'first name',
  `lastname` varchar(255) NOT NULL DEFAULT '' COMMENT 'last name',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT 'birthday',
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'gender 1-make 2-female',
  `city` varchar(511) NOT NULL DEFAULT '',
  `longitude` int(11) NOT NULL DEFAULT '-500',
  `latitude` int(11) NOT NULL DEFAULT '-500',
  `about_me` varchar(4095) NOT NULL DEFAULT '',
  `notifications_count` int(11) NOT NULL DEFAULT '0',
  `notifications_last` int(11) NOT NULL DEFAULT '0',
  `data_info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lastact` (`lastact`),
  KEY `gender` (`gender`),
  KEY `rating` (`rating`),
  KEY `alias` (`alias`),
  KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='пользователи';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `passhash`, `sig`, `token`, `regtime`, `sigtime`, `lastact`, `level`, `lastip`, `timezone`, `has_picture`, `picture_id`, `picture_orig`, `picture`, `picture_preview`, `rating`, `email`, `email_conf`, `settings`, `vk_id`, `vk_access_token`, `fb_id`, `gl_id`, `tw_id`, `od_id`, `oauths`, `skype_id`, `name`, `alias`, `firstname`, `lastname`, `birthday`, `gender`, `city`, `longitude`, `latitude`, `about_me`, `notifications_count`, `notifications_last`, `data_info`) VALUES
(7, 'madeS', '3db1b88589fa1a69926927d9b0d59f87', '619d20455ac1f7c8557e1be2176af077', '', '2013-03-16 20:02:05', 1400133102, 1400134227, '[1,2,3]', '84.201.231.69', '-180', 0, 0, 'default/profile_nopicture.png', 'default/profile_nopicture.png', 'default/profile_nopicture_preview.png', 0, 'bogxp@mail.ru', 1, 0, '4518080', '', '', '', '164211031', '', '{"vk_id":{"sync":{"field":"vk_id","value":4518080},"id":4518080,"name":"\\u0410\\u043d\\u0434\\u0440\\u0435\\u0439 \\u0411\\u043e\\u0433\\u0430\\u0440\\u0435\\u0432\\u0438\\u0447","firstname":"\\u0410\\u043d\\u0434\\u0440\\u0435\\u0439","lastname":"\\u0411\\u043e\\u0433\\u0430\\u0440\\u0435\\u0432\\u0438\\u0447","social":"vk","gender":1,"profilePhoto":"http:\\/\\/cs317928.vk.me\\/v317928080\\/62e4\\/xraQXgjtgUw.jpg","profileBirthday":"1989-05-26"},"tw_id":{"sync":{"field":"tw_id","value":164211031},"id":164211031,"name":"Andrei","social":"twitter"}}', '', 'Андрей', '', 'Андрей', 'Богаревич', '1989-05-26', 1, 'Минск, Беларусь', -500, -500, 'Бурильщик', 1, 1453224062, ''),
(1, 'mades1989', 'ab9ec6c2a37748e2fda8ec0eff788924', 'NULL', '', '2013-07-12 21:40:17', 1454647261, 1454647451, '[1,2,3,5]', '80.249.93.6', '-180', 1, 40, '2016-02/m1-20160202133708-big-a6e729.jpg', '2016-02/m1-20160202133708-medium-fd3a37.jpg', '2016-02/m1-20160202133708-preview-2ee68f9a816e9b43361922641394e788.jpg', 0, 'mades1989@gmail.com', 1, 0, '', '', '', '', '', '', '[]', '', 'Administrator', '', '', '', '0000-00-00', 0, '', -500, -500, '', 0, 0, ''),
(50, '&lt;scripterOK/&gt;', 'abc74db4a14d3861bcdcb1e1d3ffeb83', 'NULL', 'password_a8a382c13bb119b50f90865ce691f1bd', '2013-08-24 20:54:23', 1482305225, 1482305225, '[1,2,3,5,6]', '46.216.216.84', '-180', 0, 0, 'default/profile_nopicture.png', 'default/profile_nopicture.png', 'default/profile_nopicture_preview.png', 0, 'ourtravelru@gmail.com', 1, 0, '', '', '', '', '', '', '[]', '', 'scripterOK', '', '', '', '1920-01-01', 1, '', -500, -500, 'О себе тут', 0, 0, '{"person_description":"хелло"}');

-- --------------------------------------------------------

--
-- Структура таблицы `user_albums`
--

CREATE TABLE IF NOT EXISTS `user_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT 'default',
  `object_type` varchar(50) NOT NULL DEFAULT '',
  `object_id` int(10) UNSIGNED DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `medias_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `picture_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `picture_preview` varchar(255) NOT NULL DEFAULT 'default/photo_empty.png',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int(10) NOT NULL DEFAULT '0',
  `date_show` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `category` (`category`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=2524 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `user_albums`
--

INSERT INTO `user_albums` (`id`, `active`, `type`, `user_id`, `title`, `alias`, `category`, `object_type`, `object_id`, `description`, `content`, `medias_count`, `picture_id`, `picture_preview`, `created_at`, `updated_at`, `date_show`) VALUES
(2523, 1, 'default', 50, 'Тест альбом', '', 'default', 'profile', 0, '', '', 0, 72, '2016-11/m50-20161110063225-preview-7b157fcd121009bd4cb69bf9ba47b6a7.jpg', 1478701601, 1478702969, 1478687201);

-- --------------------------------------------------------

--
-- Структура таблицы `user_medias`
--

CREATE TABLE IF NOT EXISTS `user_medias` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `type` varchar(50) NOT NULL DEFAULT 'unknown',
  `user_id` int(10) UNSIGNED NOT NULL,
  `object_type` varchar(50) NOT NULL DEFAULT 'unknown',
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` int(11) UNSIGNED NOT NULL,
  `updated_at` int(11) UNSIGNED NOT NULL,
  `sortnum` int(11) NOT NULL DEFAULT '0',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `picture_orig` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `picture_preview` varchar(255) NOT NULL,
  `preview_params` varchar(255) NOT NULL DEFAULT '[]',
  `picture_data` text NOT NULL COMMENT 'exif data',
  `video_source` varchar(50) NOT NULL DEFAULT '',
  `video_code` varchar(255) NOT NULL DEFAULT '',
  `video_embed` varchar(255) NOT NULL DEFAULT '',
  `video_duration` int(11) NOT NULL DEFAULT '0',
  `content_size` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `picture_orig` (`picture_orig`),
  KEY `created_at` (`created_at`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `acrive` (`active`),
  KEY `type` (`type`),
  KEY `video_code` (`video_code`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `user_medias`
--

INSERT INTO `user_medias` (`id`, `active`, `type`, `user_id`, `object_type`, `object_id`, `created_at`, `updated_at`, `sortnum`, `title`, `description`, `picture_orig`, `picture`, `picture_preview`, `preview_params`, `picture_data`, `video_source`, `video_code`, `video_embed`, `video_duration`, `content_size`) VALUES
(40, 1, 'photo', 1, 'profile', 1, 1454420228, 1454420228, 0, '', '', '2016-02/m1-20160202133708-big-a6e729.jpg', '2016-02/m1-20160202133708-medium-fd3a37.jpg', '2016-02/m1-20160202133708-preview-2ee68f9a816e9b43361922641394e788.jpg', '{"orig_width":1920,"orig_height":1080,"src_width":1080,"src_height":1080,"srcx":420,"srcy":0,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 571),
(41, 1, 'photo', 50, 'blog', 2, 1454494815, 1454494901, 0, 'fsdfs', '', '2016-02/m50-20160203102015-big-fa71ea.jpg', '2016-02/m50-20160203102015-medium-ad8927.jpg', '2016-02/m50-20160203102015-preview-a2bff0d62675b6a6696e0042076e2bad.jpg', '{"orig_width":768,"orig_height":1024,"src_width":768,"src_height":768,"srcx":0,"srcy":128,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 146),
(39, 1, 'zip', 50, 'blog', 2, 1454418049, 1454418049, 0, 'Глеб Архангельский - Тайм-драйв. Как успевать жить и работать.zip', '', '2016-02/m50-20160202130049-big-6b2252.zip', '2016-02/m50-20160202130049-big-6b2252.zip', 'default/zip_empty.png', '{}', '{}', '', '', '', 0, 516),
(36, 1, 'video', 50, 'blog', 2, 1454417497, 1454417507, 0, 'Когда надо бежать от ПСИХОЛОГА.', 'Психотерапевты тоже люди, со всеми вытекающими последствиями) Они могут помочь, а могут и навредить.', '2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg', '2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg', '2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg', '[]', '', 'youtube', 'Ck1SkxF2Sak', 'Ck1SkxF2Sak', 549, 0),
(37, 1, 'video', 50, 'blog', 2, 1454417704, 1454417706, 0, 'Драйв от соревнования с собой вчерашним', 'Интервью для проекта Pro$to, записанное во время визита в Бишкек с тренингами “Харизма лидера: имидж и мистика, психология и власть” и “Крест лидера”.<br/><br/>Пост в блоге: http://blog.radislavgandapas.com/drive/<br/><br/>Слушайте в интервью:<br/>- Каким ребенком был Радислав, и был ли он лидером в детские годы<br/>- Откуда берется желание стать лидером<br/>- О важности решений, которые мы принимаем каждый день<br/>- Интересный вопрос, который Радиславу еще никто не задавал<br/>- Какого результата достигают посетители тренингов Радислава<br/><br/>__<br/><br/>Официальный сайт Радислава Гандапаса: http://www.radislavgandapas.com/<br/>Блог: http://blog.radislavgandapas.com/<br/><br/>Радислав Гандапас в социальных сетях:<br/><br/>Twitter: http://twitter.com/gandapas<br/>Facebook: https://www.facebook.com/gandapas<br/>ВКонтакте: http://vk.com/radislavgandapas_vk<br/>Instagram: http://instagram.com/radislavgandapas/<br/>LinkedIn: http://ru.linkedin.com/pub/radislav-gandapas/18/212/b98', '2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg', '2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg', '2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg', '[]', '', 'youtube', 'Hmf9WMLgFLo', 'Hmf9WMLgFLo', 2427, 0),
(38, 1, 'photo', 50, 'blog', 2, 1454417905, 1454417905, 0, '', '', '2016-02/m50-20160202125825-big-299bbb.jpg', '2016-02/m50-20160202125825-medium-1490c4.jpg', '2016-02/m50-20160202125825-preview-d64825206822c475dd10de8c51ac8bcb.jpg', '{"orig_width":1300,"orig_height":1166,"src_width":1166,"src_height":1166,"srcx":67,"srcy":0,"new_width":300,"new_height":300}', '{"longitude":"","latitude":"","make":"","model":"","exposure":"","aperture":"","apertureValue":"","iso":"","focalLength35mm":"","focalLength":"","meteringMode":"","flash":"","exposureBiasValue":"","sensingMethod":"","gainControl":"","exposureProgram":"","maxApertureValue":"","datetime":"","orientation":""}', '', '', '', 0, 585),
(72, 1, 'photo', 50, 'album', 2523, 1478759545, 1478759545, 5, '', '', '2016-11/m50-20161110063225-big-dfabe7.jpg', '2016-11/m50-20161110063225-big-dfabe7.jpg', '2016-11/m50-20161110063225-preview-7b157fcd121009bd4cb69bf9ba47b6a7.jpg', '{"orig_width":475,"orig_height":712,"src_width":475,"src_height":475,"srcx":0,"srcy":118,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 99),
(73, 1, 'photo', 50, 'album', 2523, 1478759546, 1478759546, 2, '', '', '2016-11/m50-20161110063226-big-d90262.jpg', '2016-11/m50-20161110063226-medium-74a94a.jpg', '2016-11/m50-20161110063226-preview-fb80c7270efb7de29b67f39bb38dc533.jpg', '{"orig_width":864,"orig_height":1440,"src_width":864,"src_height":864,"srcx":0,"srcy":288,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 218),
(74, 1, 'photo', 50, 'album', 2523, 1478759546, 1478759546, 4, '', '', '2016-11/m50-20161110063226-big-b8a8bb.jpg', '2016-11/m50-20161110063226-medium-3a4752.jpg', '2016-11/m50-20161110063227-preview-1fd5e51132b6a34ba81b9e351f5ed152.jpg', '{"orig_width":864,"orig_height":1440,"src_width":864,"src_height":864,"srcx":0,"srcy":288,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 171),
(75, 1, 'photo', 50, 'album', 2523, 1478759547, 1478759547, 7, '', '', '2016-11/m50-20161110063227-big-28d263.jpg', '2016-11/m50-20161110063227-big-28d263.jpg', '2016-11/m50-20161110063227-preview-e806be1b2b5d7275385daa1c334b9715.jpg', '{"orig_width":640,"orig_height":640,"src_width":640,"src_height":640,"srcx":0,"srcy":0,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 85),
(76, 1, 'photo', 50, 'album', 2523, 1478759547, 1478759547, 3, '', '', '2016-11/m50-20161110063227-big-95185c.jpg', '2016-11/m50-20161110063227-medium-15a6b5.jpg', '2016-11/m50-20161110063227-preview-479fc5189522a84b757cffccc6ce0a6b.jpg', '{"orig_width":960,"orig_height":1378,"src_width":960,"src_height":960,"srcx":0,"srcy":209,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 187),
(77, 1, 'photo', 50, 'album', 2523, 1478759548, 1478759548, 8, '', '', '2016-11/m50-20161110063228-big-445175.jpg', '2016-11/m50-20161110063228-medium-5ba8d1.jpg', '2016-11/m50-20161110063228-preview-9c3ffcbd8f5d2e568459a4ea93fbff5d.jpg', '{"orig_width":575,"orig_height":1024,"src_width":575,"src_height":575,"srcx":0,"srcy":224,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 149),
(78, 1, 'photo', 50, 'album', 2523, 1478759548, 1478759548, 6, '', '', '2016-11/m50-20161110063228-big-4ac94c.jpg', '2016-11/m50-20161110063228-big-4ac94c.jpg', '2016-11/m50-20161110063228-preview-a6a51a8cd1beb597c70c391589ad28ca.jpg', '{"orig_width":640,"orig_height":640,"src_width":640,"src_height":640,"srcx":0,"srcy":0,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 87),
(79, 1, 'photo', 50, 'album', 2523, 1478759549, 1478759549, 1, '', '', '2016-11/m50-20161110063229-big-8cf57a.jpg', '2016-11/m50-20161110063229-medium-364f17.jpg', '2016-11/m50-20161110063229-preview-165d06e51816e1129e2738927e1a1e75.jpg', '{"orig_width":959,"orig_height":1440,"src_width":959,"src_height":959,"srcx":0,"srcy":240,"new_width":300,"new_height":300}', '[]', '', '', '', 0, 271);

-- --------------------------------------------------------

--
-- Структура таблицы `user_notifications`
--

CREATE TABLE IF NOT EXISTS `user_notifications` (
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'Профиль',
  `type` enum('new_subscriber','new_message','new_room_message') NOT NULL,
  `object_id` int(11) UNSIGNED NOT NULL COMMENT 'На кого подписка',
  `from_user_id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `isnew` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`type`,`object_id`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`),
  KEY `user_id` (`user_id`),
  KEY `isnew` (`isnew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Уведомления пользователя';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
