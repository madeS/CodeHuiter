-- MySQL dump 10.13  Distrib 5.7.28, for Linux (x86_64)
--
-- Host: localhost    Database: app_db
-- ------------------------------------------------------
-- Server version	5.7.28-0ubuntu0.16.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `TestWithTwoAutoIncrement`
--

DROP TABLE IF EXISTS `TestWithTwoAutoIncrement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TestWithTwoAutoIncrement` (
  `onePrimaryField` int(11) NOT NULL,
  `secondPrimaryField` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`secondPrimaryField`,`onePrimaryField`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TestWithTwoAutoIncrement`
--

/*!40000 ALTER TABLE `TestWithTwoAutoIncrement` DISABLE KEYS */;
INSERT INTO `TestWithTwoAutoIncrement` VALUES (555,1,'2019-10-01 13:57:14'),(555,2,'2019-10-01 14:01:00'),(555,3,'2019-10-01 14:03:00'),(555,4,'2019-10-01 14:03:02'),(555,5,'2019-10-01 14:03:03'),(555,6,'2019-10-01 14:03:29'),(555,7,'2019-10-01 14:03:30'),(555,8,'2019-10-01 14:03:31'),(555,9,'2019-10-01 14:03:35'),(555,10,'2019-10-01 14:03:36'),(555,11,'2019-10-01 14:03:36'),(555,12,'2019-10-01 14:03:36'),(555,13,'2019-10-06 23:00:30'),(555,14,'2019-10-07 01:00:46'),(555,15,'2019-10-07 01:17:27'),(555,16,'2019-10-07 01:19:07'),(555,17,'2019-10-07 01:32:08'),(555,18,'2019-10-07 01:34:31'),(555,19,'2019-10-07 01:35:11'),(555,20,'2019-10-23 05:38:51');
/*!40000 ALTER TABLE `TestWithTwoAutoIncrement` ENABLE KEYS */;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `is_category` tinyint(4) NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `place_type` varchar(50) NOT NULL DEFAULT 'unknown',
  `place_order` int(11) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `alias` varchar(200) NOT NULL DEFAULT '',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `data_info` text NOT NULL,
  `comments_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_up_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_down_count` int(11) NOT NULL DEFAULT '0',
  `seo_title` varchar(1000) NOT NULL DEFAULT '',
  `seo_keywords` varchar(1000) NOT NULL DEFAULT '',
  `seo_description` text NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`(191)),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES (1,1,0,0,'topmenu',0,50,'main','Главная','<strong>CI&nbsp;</strong><strong>MOP (CodeIgniter Mades Online Pattern)</strong>&nbsp; &mdash; это архитектурное решение на базе фрэймворка CodeIgniter для разработки сайтов любой сложности.<br /><br />Дополнительно к фреймворку реализовано:<br />- Сниппеты, для разносторонних задач, необходимых при разработке сайта<br />- Поддержка mjsa библиотеки (ajax навигация, формы, ошибки и сообщения)<br />- CSS решения.<br />- Компрессия JS и CSS файлов (пока собирает и отдает одним файлом. Минимизацию на лету ещё не делал)<br />- Очередь рассылки писем<br />- Авторизация (поддержка 4 социальных сетей vk, fb, google, twitter), настройки пользователя, блокировка бользователей<br />- Загрузка медиа для пользователя (фотографии, видеозаписи youtube vimeo vk, архивы zip)<br />- Фидбэк<br />- Система уведомлений пользователя<br />- Диалоги<br />- Блог<br />- Комментарии к любым объектам<br />- Лайки, дизлайки к любым объектам<br />- YaShare<br /><br />Реализовать в будущем:<br />- Бэкаппер (автоматический бэкап сайта)<br />- Крон решения разных интервалов на основе поминутного крона<br />- Каркас для интернет-магазинов (товары, категории, заказы)<br />- Чат-диалоги<br /><br />','',4,0,0,'','','',1454413219,1454415373),(2,1,0,0,'sidemenu',0,50,'','Доп страница','Доп страница&nbsp;<br />Видео на странице {#media37}&nbsp;<br />Картинка на странице&nbsp;{#media38}<br />Архив на странице&nbsp;{#media39}<br /><br />Другой контент прикрепленный к странице:','',0,0,0,'','','',1454416691,1454418137),(3,1,0,0,'topmenu',0,50,'','Тест страница 1','Контент','',0,0,0,'','','',1482075144,1482075192),(4,1,0,0,'topmenu',0,50,'contacts','Контакты','Что-то написано здесь','',0,0,0,'Контакты','','',1520690258,1520691089);
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `guest_name` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(50) NOT NULL DEFAULT 'none',
  `object_id` int(10) unsigned DEFAULT '0',
  `subobject_id` int(10) unsigned DEFAULT '0',
  `message` text NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parents_string` varchar(200) NOT NULL DEFAULT '',
  `parent_level` int(11) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  `thumbs_up_count` int(11) NOT NULL DEFAULT '0',
  `thumbs_down_count` int(11) NOT NULL DEFAULT '0',
  `complain_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `active` (`active`),
  KEY `parents_string` (`parents_string`(191))
) ENGINE=InnoDB AUTO_INCREMENT=905 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (898,2,50,'','46.216.218.36','blog',1,0,'Опа-па',0,':00000000898:',0,1454415406,1454415570,0,1,0),(899,2,50,'','46.216.218.49','blog',1,0,'12',0,':00000000899:',0,1454595155,1454595256,1,0,0),(901,2,50,'','46.216.218.49','blog',1,0,'dsfsd',0,':00000000901:',0,1454595335,1454595335,1,0,0),(904,2,50,'','46.216.218.49','blog',1,0,'dsfsd',899,':00000000899:00000000904:',1,1454595423,1454595423,0,0,0);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;

--
-- Table structure for table `dialogues`
--

DROP TABLE IF EXISTS `dialogues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialogues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `room_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID получателя',
  `message` text NOT NULL,
  `isnew` tinyint(4) NOT NULL DEFAULT '1',
  `readed_at` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `profile_id` (`room_id`),
  KEY `isnew` (`isnew`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialogues`
--

/*!40000 ALTER TABLE `dialogues` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogues` ENABLE KEYS */;

--
-- Table structure for table `dialogues_rooms`
--

DROP TABLE IF EXISTS `dialogues_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialogues_rooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('private','standart','public') NOT NULL DEFAULT 'standart',
  `name` varchar(200) NOT NULL DEFAULT '',
  `last_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_message` text NOT NULL,
  `count_total` int(11) NOT NULL DEFAULT '0',
  `count_new` int(11) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`name`(191)),
  KEY `isnew` (`count_new`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialogues_rooms`
--

/*!40000 ALTER TABLE `dialogues_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogues_rooms` ENABLE KEYS */;

--
-- Table structure for table `dialogues_rooms_invitations`
--

DROP TABLE IF EXISTS `dialogues_rooms_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dialogues_rooms_invitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `room_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'приглашающий, если это начальный с двумя или для открытой комнаты то 0',
  `join_at` int(10) unsigned NOT NULL DEFAULT '0',
  `leave_at` int(10) unsigned NOT NULL DEFAULT '0',
  `readed_at` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `isnew` (`join_at`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dialogues_rooms_invitations`
--

/*!40000 ALTER TABLE `dialogues_rooms_invitations` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogues_rooms_invitations` ENABLE KEYS */;

--
-- Table structure for table `mailer`
--

DROP TABLE IF EXISTS `mailer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `sended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `updated_at` (`updated_at`,`sended`),
  KEY `updated_at_2` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailer`
--

/*!40000 ALTER TABLE `mailer` DISABLE KEYS */;
INSERT INTO `mailer` VALUES (1,0,'Проверка E-mail на CodeHuiter Pattern','testUser@example.com','Пользователь нашего сайта testUser при регистрации  указал этот E-mail в качестве своего.\nЕсли это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:\nhttp://app.local/auth/confirm_email?user_id=83&token=f47e287b9cd8ac7650d3d6f1075e3656\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-16 07:28:02','2019-11-16 07:28:02',0),(2,0,'Смена пароля на CodeHuiter Pattern','testUser@example.com','Кто-то на сайте CodeHuiter Pattern запросил ссылку для смену пароля.\nЕсли это были вы, перейдите по ссылке ниже для смены пароля:\nhttp://app.local/auth/recovery?user_id=83&token=07534ca543de08c6ffe78a44b650f243\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-26 10:49:17','2019-11-26 10:49:17',0),(3,0,'Проверка E-mail на CodeHuiter Pattern','testUser@example.com','Пользователь нашего сайта testUser при регистрации  указал этот E-mail в качестве своего.\nЕсли это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:\nhttp://app.local/auth/confirm_email?user_id=83&token=446d692a7abe3b3671f8f8fb6788a242\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-26 15:15:27','2019-11-26 15:15:27',0),(4,0,'Проверка E-mail на CodeHuiter Pattern','testUser@example.com','Пользователь нашего сайта testUser при регистрации  указал этот E-mail в качестве своего.\nЕсли это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:\nhttp://app.local/auth/confirm_email?user_id=83&token=446d692a7abe3b3671f8f8fb6788a242\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-26 15:17:30','2019-11-26 15:17:30',0),(5,0,'Проверка E-mail на CodeHuiter Pattern','testUserTest@example.com','Пользователь нашего сайта testUser при регистрации  указал этот E-mail в качестве своего.\nЕсли это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:\nhttp://app.local/auth/confirm_email?user_id=83&token=1a38e7a0f507ae92b8f98386f6cd46ce\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-26 15:18:29','2019-11-26 15:18:29',0),(6,0,'Проверка E-mail на CodeHuiter Pattern','testUser@example.com','Пользователь нашего сайта testUser при регистрации  указал этот E-mail в качестве своего.\nЕсли это были вы, перейдите по ссылке ниже для подтверждения этого E-mail:\nhttp://app.local/auth/confirm_email?user_id=83&token=c26f18a1a9c748d52c7ea3f1b21e7ade\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-26 15:19:04','2019-11-26 15:19:04',0),(7,0,'Смена пароля на CodeHuiter Pattern','testUser@example.com','Кто-то на сайте CodeHuiter Pattern запросил ссылку для смену пароля.\nЕсли это были вы, перейдите по ссылке ниже для смены пароля:\nhttp://app.local/auth/recovery?user_id=83&token=aa78c1ecbf48c37d1c7abc87baf9215b\nЕсли это были не вы, то просто проигнорируйте это письмо','2019-11-27 04:04:14','2019-11-27 04:04:14',0);
/*!40000 ALTER TABLE `mailer` ENABLE KEYS */;

--
-- Table structure for table `mtasks`
--

DROP TABLE IF EXISTS `mtasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `params` varchar(1000) NOT NULL DEFAULT '{}',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `group_name` (`group_name`,`name`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtasks`
--

/*!40000 ALTER TABLE `mtasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtasks` ENABLE KEYS */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title_h` varchar(2047) NOT NULL DEFAULT '',
  `the_text` text NOT NULL,
  `redirect_uri` varchar(255) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '0',
  `editable` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'default','text_page1','','Text page 1 H1','<p>\n	Суть замыканий проста: внутри функции можно использовать все пременные, которые доступны в том месте, где функция была объявлена.<br />\n	<br />\n	Хотя идея замыканий проста, на практике зачастую возникает много непонятных моментов по поведению в том или ином случае. Так что для начала вспомним основы объявления переменной, а именно &ndash; &quot;переменные в JavaScript объявляются с помощью ключевого слова var&quot;:<br />\n	<br />\n	<strong>При запуске кода выведет текст &quot;Hello World&quot;, как и ожидалось. Суть происходящего проста &ndash; создаётся глобальная переменная title со значением &quot;Hello World&quot;, которое показывается пользователю с помощью alert-а.</strong> В данном примере, даже если мы опустим ключевое слово var, код всё равно сработает правильно из-за глобального контекста. Но об этом позже.<br />\n	<br />\n	Теперь попробуем объявить ту же переменную, но уже внутри функции:<br />\n	<br />\n	В результате запуска кода сгенерируется ошибка &quot;&#39;title&#39; is undefined&quot; &mdash; &quot;переменная &#39;title&#39; не была объявлена&quot;. <strong>Это происходит из-за механизма локальной области видимости переменных: все переменные, объявленные внутри фукнции являются локальными и видны только внутри этой функции. Или проще</strong>: если мы объявим какую-то переменную внутри функции, то вне этой функции доступа к этой переменной у нас не будет.<br />\n	<br />\n	Для того, чтобы вывести надпись &quot;Hello World&quot;, необходимо вызвать alert внутри вызываемой функции:<br />\n	<br />\n	<br />\n	<br />\n	<br />\n	Либо вернуть значение из функции:</p>\n','',0,2),(2,'default','about','','About H1','<p>\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.<br />\n	Some text some text there is some text.</p>\n','',0,2),(3,'default','our_team','','our team','<p>\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some POur team some text tOur teamSome text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some textOur teamip text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	SomeOur teamme teOur teamome text.Some text Our team is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some Our team there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some Our team some text there is some text.Some text some text there is some text.</p>\n','',0,2),(4,'default','our_goals','','Our goals','<p>\n	Some text some text there is some text.Some text some text there is some text.<br />\n	How to Our goals some textHow to pay text some text there is some text.<br />\n	Some text some text there is some text.Our goals goals text some Our goals there is some text.<br />\n	Some How to pay text there is some text.Some text some text there Our goals some text.<br />\n	Some text some Our Our goals there is some text.How to pay text some text there is some text.<br />\n	Some text Our goals text there Our goals some text.Some text How Our goals pay text How to pay is some text.<br />\n	Some text some text there is some text.Some Our goals some text there is some text.<br />\n	Some text some text there is Our goals text.Some text some text there is some text.<br />\n	Thanks text.Our goals tex How Our goals pay some Our goals.Some Our goals to pay some text there is some text.<br />\n	Some text some text there is some text.Some Our goals some text there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some How to Our goals there is Our goals text.Some text some How to pay there is some text.<br />\n	Some text some text there is some text.Some text some text there is some text.<br />\n	Some text some text there Our goals some text.Some text some text there is some text.<br />\n	How to pay some text there is some text.Some Our goals some text there is some text.</p>\n','',0,2),(5,'default','how_it_work','','How its work???','    Some text some text there is some text.\n	Some How it work some text there is some text.\n	Some text some text there is How it work text.\n	Some text some text there is some text.\n	Some text How it work text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some How it work some text How it work is some text.Some text some How it work there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text How it work text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some How it work some text there is some text.','',0,2),(6,'default','how_to_pay','','I need your money','    Some text some text there is some text.Some text some text there is some text.\n	How to pays some textHow to pay text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some How to pay text there is some text.Some text some text there is some text.\n	Some text some text there is some text.How to pay text some text there is some text.\n	Some text some text there is some text.Some text How to pay text How to pay is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks text.Some tex How to pay some text.Some How to pay some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some How to pay there is some text.Some text some How to pay there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	How to pay some text there is some text.Some text some text there is some text.','',0,2),(7,'default','partnership','','my partner is lesbian','    Some text some text there is some text.\n	Some Partnership some text there is some text.\n	Some text some text there isPartnership text.\n	Some text some text there is some text.\n	Some text Partnership text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some Partnershipk some text Partnerships some text.Some text somePartnershipere is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some textPartnershipxt there is some text.\n	Some text some text there is some text.\n	Some text some text there is some text.\n	Some Partnership some text there is some text.','',0,2),(8,'default','terms','','Terms of blah','dsffsdfs','',0,2),(9,'default','thanks','','Thanks me','    Some text some text there is some text.Some text some text there is some text.\n	SomeThanks some text tOur teamSome text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some Thanks text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks text.Some tex tThanksis some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some Thanks there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Some text some text there is some text.Some text some text there is some text.\n	Thanks some text there is some text.Some text some text there is some text.','',0,2),(10,'default','contact_us','','Contact us','<img src=\"../pub/files/uploads/images/2010-geneva-babes-23.jpg\" alt=\"dome\" height=\"200\" />Some text some text there is some text. Some Contact us some text there is some text. Some text some text there Contact us text. Some text some text there is some text. Some text Contact us text there is some text. Some text some text there is some text. Some text some text there is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us some text Contact us some text.Some text Contact us is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us there is some text. Some text some text there is some text. Some text some text there is some text. Some Contact us some text there is some text.','',0,2),(11,'default','cooperation','','Cooperation','<p>Some text some text there is some text.Some text some text there is some text. How Cooperation Our Cooperation some text</p>\n<p>How to Cooperation Cooperationhere is some text. Some text some text there is some text.Our Cooperation goals teCooperation goals there is some text. Some How to Cooperation text Cooperation is Cooperation text.Some text some text there Our goals some text. Some text some Our Our goals thCooperation text.How Cooperation some teCooperationre is some text. Cooperationtext some text there is some text.Some Our goals some text there is some text. Some text some tCooperationation How Our goals pay someCooperatione text there is some text. Some text some text there is some text.Some Our goals some Cooperation there is some text. Some text some text there is some text.Some text some tCooperation is some text. Some How to Cooperation Cooperation there is Our goals text.Some text some How to pay there is some text. Some text some text there is some text.Some text some texCooperatione text. Some text some text Cooperations some text.Some text some text there is some text. How to pay some text there is some text.Cooperation Our goals some text there is some text.</p>','',0,2),(12,'default','blog','','blog blog blog','<p>\n	dfsfefs fe fs efs ef sf sef e f ef sef s e sfe sf sef se sef sef sf sef e ssf</p>\n','',0,2),(13,'default','company','','О компании','Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.\n<div>Some text some text there is some text.<img src=\"../pub/files/images/content/gallery/m0-20131112164358-medium-d2e683d095b13202b2a33d5aa4a6de39.jpg\" alt=\"\" width=\"300\" /></div>\n<ol>\n<li>Some text some text there is some text.<br /> Some text some text there is some text.</li>\n<li><br /> Some text some text there is some text.<br /> Some text some text there is some text.Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.<br /> Some text some text there is some text.</li>\n</ol>','',0,2),(14,'default','promo_maxturbo8','','Супер низкая цена','цены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены <br />супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текстцены супернизкие на <strong>какой-то това</strong>р и ещё какой-то текст и ещё какой-то текст','',0,2),(15,'default','promo_fusion8','','Опа-па','1. Если вы ищете один элемент, используйте поиск по идентификатору:<br /> $(\'#color_button\') &ndash; будет выполнен максимально быстро<br /> $(\'#content .button\') &ndash; будет медленнее<br /> $(\'.button:first\') &ndash; еще медленнее<br /> <br /> <span style=\"text-decoration: underline;\">2. Если вы ищете группу элементов, указывайте ближайшего общего родственника,</span> обладающего идентификатором:<br /> $(\'#content input\') будет эффективнее, чем просто $(\'input\').<br /> <br />\n<div style=\"text-align: right;\">3. Если вы ищете элементы по классу, указывайте имя тега:</div>\n<div style=\"text-align: right;\">$(\'input.button\') выполнит поиск быстрее, чем $(\'.button\'). В первом случае, jQuery вначале найдет все элементы input, и уже среди них будет искать элементы с классом button. А во втором случае, для этого будет произведен перебор всех элементов страницы.</div>\n<br /> Из всего сказанного<em>, можно вывести два основных п</em>равила:\n<blockquote>\n<p>1) Для поиска одного элемента, используйте поиск по id: $(\'#someId\')</p>\n</blockquote>\n2) При поиске группы элементов, старайтесь придерживаться следующей формулы: $(\'#someId tagName.someClass\')<br /> <br /> <span style=\"text-decoration: line-through;\">И еще, не пытайтесь улучшить поиск по id с помощью следующих комбинаций:<br /> $(\'tagName#someId\')<br /> $(\'#wrapId #someId\')<br /> Это только замедлит выполнение поиска.</span>','',0,2);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;

--
-- Table structure for table `shop_cart_product`
--

DROP TABLE IF EXISTS `shop_cart_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_cart_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned NOT NULL,
  `shop_product_id` int(10) unsigned NOT NULL,
  `shop_product_saved_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `services` varchar(255) NOT NULL DEFAULT '',
  `order_price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_product_index` (`cart_id`,`shop_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_cart_product`
--

/*!40000 ALTER TABLE `shop_cart_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_cart_product` ENABLE KEYS */;

--
-- Table structure for table `shop_carts`
--

DROP TABLE IF EXISTS `shop_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sig` varchar(255) NOT NULL DEFAULT '',
  `ordered` tinyint(4) NOT NULL DEFAULT '0',
  `total_order_price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `payed` tinyint(4) NOT NULL DEFAULT '0',
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `customer_phone` varchar(255) NOT NULL DEFAULT '',
  `customer_email` varchar(255) NOT NULL DEFAULT '',
  `customer_address` text NOT NULL,
  `customer_comment` text NOT NULL,
  `delivery_type` varchar(50) NOT NULL DEFAULT '',
  `delivery_address` varchar(50) NOT NULL DEFAULT '',
  `pay_type` varchar(50) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sig` (`sig`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_carts`
--

/*!40000 ALTER TABLE `shop_carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_carts` ENABLE KEYS */;

--
-- Table structure for table `shop_categories`
--

DROP TABLE IF EXISTS `shop_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_uri` varchar(255) NOT NULL DEFAULT '',
  `parent_string` varchar(255) NOT NULL DEFAULT '',
  `parent_level` int(11) NOT NULL DEFAULT '1',
  `priority` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `h1` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_icon` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_description` text NOT NULL,
  `seo_keywords` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_for_main` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  KEY `slug` (`slug`(191))
) ENGINE=InnoDB AUTO_INCREMENT=545 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_categories`
--

/*!40000 ALTER TABLE `shop_categories` DISABLE KEYS */;
INSERT INTO `shop_categories` VALUES (1,1,1,'',':00000000000:',0,0,'Главная','','Корневая служебная категория','','','','','','','2017-06-20 05:30:01','2017-06-20 05:30:01',0),(535,1,543,'/dop',':00000000000:00000000001:00000000001:',2,1,'Краски подкатегория','','','','','kraski-podkategoriya','','','',NULL,NULL,0),(543,0,1,'',':00000000000:00000000001:',1,1,'доп','','','','','dop','','','',NULL,NULL,0),(544,1,1,'',':00000000000:00000000001:',1,1,'Другое','','','','','drugoe','','','',NULL,NULL,0);
/*!40000 ALTER TABLE `shop_categories` ENABLE KEYS */;

--
-- Table structure for table `shop_category_product`
--

DROP TABLE IF EXISTS `shop_category_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_category_product` (
  `shop_category_id` int(10) unsigned NOT NULL,
  `shop_product_id` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_category_id`,`shop_product_id`),
  KEY `category_product_index` (`shop_category_id`,`shop_product_id`),
  KEY `id_product` (`shop_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_category_product`
--

/*!40000 ALTER TABLE `shop_category_product` DISABLE KEYS */;
INSERT INTO `shop_category_product` VALUES (544,1,0),(544,2,0),(544,3,0),(544,4,0),(544,5,0),(544,6,0),(544,7,0),(544,8,0),(544,9,0),(544,10,0),(544,11,0),(544,12,0),(544,13,0),(544,14,0),(544,15,0),(544,16,0),(544,17,0),(544,18,0),(544,19,0),(544,20,0),(544,21,0),(544,22,0),(544,23,0),(544,24,0),(544,25,0),(544,26,0),(544,27,0),(544,28,0),(544,29,0),(544,30,0),(544,31,0),(544,32,0),(544,33,0),(544,34,0),(544,35,0),(544,36,0),(544,37,0),(544,38,0),(544,39,0),(544,40,0),(544,41,0),(544,42,0),(544,43,0),(544,44,0),(544,45,0),(544,46,0),(544,47,0),(544,48,0),(544,49,0),(544,50,0),(544,51,0),(544,52,0),(544,53,0),(544,54,0),(544,55,0),(544,56,0),(544,57,0),(544,58,0),(544,59,0),(544,60,0),(544,61,0),(544,62,0),(544,63,0),(544,64,0),(544,65,0),(544,66,0),(544,67,0),(544,68,0),(544,69,0),(544,70,0),(544,71,0),(544,72,0),(544,73,0),(544,74,0),(544,75,0),(544,76,0),(544,77,0),(544,78,0),(544,79,0),(544,80,0),(544,81,0),(544,82,0),(544,83,0),(544,84,0),(544,85,0),(544,86,0),(544,87,0),(544,88,0),(544,89,0),(544,90,0),(544,91,0),(544,92,0),(544,93,0),(544,94,0),(544,95,0),(544,96,0),(544,97,0),(544,98,0),(544,99,0),(544,100,0),(544,101,0),(544,102,0),(544,103,0),(544,104,0),(544,105,0),(544,106,0),(544,107,0),(544,108,0),(544,109,0),(544,110,0),(544,111,0),(544,112,0),(544,113,0),(544,114,0),(544,115,0),(544,116,0),(544,117,0),(544,118,0),(544,119,0),(544,120,0),(544,121,0),(544,122,0),(544,123,0),(544,124,0),(544,125,0),(544,126,0),(544,127,0),(544,128,0),(544,129,0),(544,130,0),(544,131,0),(544,132,0),(544,133,0),(544,134,0),(544,135,0),(544,136,0),(544,137,0),(544,138,0),(544,139,0),(544,140,0),(544,141,0),(544,142,0),(544,143,0),(544,144,0),(544,145,0),(544,146,0),(544,147,0),(544,148,0),(544,149,0),(544,150,0),(544,151,0),(544,152,0),(544,153,0),(544,154,0),(544,155,0),(544,156,0),(544,157,0),(544,158,0),(544,159,0),(544,160,0),(544,161,0),(544,162,0),(544,163,0),(544,164,0),(544,165,0),(544,166,0),(544,167,0),(544,168,0),(544,169,0),(544,170,0),(544,171,0),(544,172,0),(544,173,0),(544,174,0),(544,175,0),(544,176,0),(544,177,0),(544,178,0),(544,179,0),(544,180,0),(544,181,0),(544,182,0),(544,183,0),(544,184,0),(544,185,0),(544,186,0),(544,187,0),(544,188,0),(544,189,0),(544,190,0),(544,191,0),(544,192,0),(544,193,0),(544,194,0),(544,195,0),(544,196,0),(544,197,0),(544,198,0),(544,199,0),(544,200,0),(544,201,0),(544,202,0),(544,203,0),(544,204,0),(544,205,0),(544,206,0),(544,207,0),(544,208,0),(544,209,0),(544,210,0),(544,211,0),(544,212,0),(544,213,0),(544,214,0),(544,215,0),(544,216,0),(544,217,0),(544,218,0),(544,219,0);
/*!40000 ALTER TABLE `shop_category_product` ENABLE KEYS */;

--
-- Table structure for table `shop_feature_category`
--

DROP TABLE IF EXISTS `shop_feature_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_feature_category` (
  `shop_feature_id` int(10) unsigned NOT NULL,
  `shop_category_id` int(10) unsigned NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_feature_id`,`shop_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_feature_category`
--

/*!40000 ALTER TABLE `shop_feature_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_feature_category` ENABLE KEYS */;

--
-- Table structure for table `shop_feature_product`
--

DROP TABLE IF EXISTS `shop_feature_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_feature_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_feature_id` int(10) unsigned NOT NULL,
  `shop_product_id` int(10) unsigned NOT NULL,
  `feature_value_string` varchar(255) NOT NULL DEFAULT '',
  `feature_value_float` decimal(20,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`id`),
  KEY `s_feature_id` (`shop_feature_id`),
  KEY `s_product_id` (`shop_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_feature_product`
--

/*!40000 ALTER TABLE `shop_feature_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_feature_product` ENABLE KEYS */;

--
-- Table structure for table `shop_features`
--

DROP TABLE IF EXISTS `shop_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'string',
  `unit` varchar(50) NOT NULL DEFAULT '',
  `values_sort` varchar(50) NOT NULL DEFAULT 'az',
  `priority_filter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_features`
--

/*!40000 ALTER TABLE `shop_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_features` ENABLE KEYS */;

--
-- Table structure for table `shop_places`
--

DROP TABLE IF EXISTS `shop_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_places` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL DEFAULT '',
  `is_sto` tinyint(3) unsigned DEFAULT '0',
  `is_export` tinyint(4) NOT NULL DEFAULT '1',
  `shopcode` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `address1` varchar(128) NOT NULL DEFAULT '',
  `city` varchar(64) NOT NULL DEFAULT '',
  `postcode` varchar(12) NOT NULL DEFAULT '',
  `latitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `longitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `hours` text,
  `phones` text,
  `phones_description` text,
  `url` varchar(255) NOT NULL DEFAULT '',
  `fax` varchar(16) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `note` text,
  `services` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  `is_main` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_places`
--

/*!40000 ALTER TABLE `shop_places` DISABLE KEYS */;
INSERT INTO `shop_places` VALUES (11,'Беларусь',0,0,'amper500','500 Ампер - Интернет-магазин','s-shops/July2017/YR4KhX0rIbEjlbsq77Fl.jpg','','г. Минск','',0.000000,0.000000,'[{\"key\":\"Пн-Пт\",\"value\":\"9:00-19:00\"},{\"key\":\"Cб\",\"value\":\"9:00-16:00\"},{\"key\":\"Вс\",\"value\":\"9:00-16:00\"}]','[{\"key\":\"mts\",\"value\":\"+375 (29) 830-39-69\"},{\"key\":\"velcom\",\"value\":\"+375 (29) 680-39-69\"}]','','','','500amper@gmail.com','','[]',1,100,1,NULL,'2017-07-03 16:31:00'),(12,'Беларусь',0,1,'dolginovo','500 Ампер - п.Долгиново','s-shops/September2017/FCO2iItZ2QXfArsNfdmA.jpg','ул. Советская 9А','Минская область, п. Долгиново','000000',54.645370,27.477112,'[{\"key\":\"Пн-Пт\",\"value\":\"09:00-18:00\"},{\"key\":\"Cб\",\"value\":\"09:00-18:00\"},{\"key\":\"Вс\",\"value\":\"09:00-16:00\"}]','[{\"key\":\"velcom\",\"value\":\"+375 (29) 601-07-26\"}]','','','','','','',1,38,0,NULL,'2017-09-09 13:02:22');
/*!40000 ALTER TABLE `shop_places` ENABLE KEYS */;

--
-- Table structure for table `shop_product_images`
--

DROP TABLE IF EXISTS `shop_product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_product_id` int(10) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `position` smallint(2) unsigned NOT NULL DEFAULT '0',
  `cover` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `image_product` (`shop_product_id`),
  KEY `id_product_cover` (`shop_product_id`,`cover`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_images`
--

/*!40000 ALTER TABLE `shop_product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_images` ENABLE KEYS */;

--
-- Table structure for table `shop_products`
--

DROP TABLE IF EXISTS `shop_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mcode` varchar(255) NOT NULL DEFAULT '',
  `mcode_name` varchar(255) NOT NULL DEFAULT '',
  `mcode_updated_at` int(11) NOT NULL DEFAULT '0',
  `parent_uri` varchar(512) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `description_short` text NOT NULL,
  `image_copy` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_description` text NOT NULL,
  `seo_keywords` text NOT NULL,
  `shop_default_category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `units` varchar(20) NOT NULL DEFAULT '',
  `price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_popularity` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `maker` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `importer` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_category_default` (`shop_default_category_id`),
  KEY `date_add` (`created_at`),
  KEY `order_popularity` (`order_popularity`),
  KEY `mcode` (`mcode`(191))
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_products`
--

/*!40000 ALTER TABLE `shop_products` DISABLE KEYS */;
INSERT INTO `shop_products` VALUES (1,'1','',0,'/drugoe/yablonka',0,'yablonka','Яблонька','','','','','','',544,1,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(2,'2','',0,'/drugoe/kraska-yablonka-f-10-kg',0,'kraska-yablonka-f-10-kg','Краска \"Яблонька\" ф. 1,0 кг','','','','','','',544,18,'ведро',1.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(3,'3','',0,'/drugoe/kraska-yablonka-f-12-kg',0,'kraska-yablonka-f-12-kg','Краска \"Яблонька\" ф. 1,2 кг','','','','','','',544,548,'ведро',1.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(4,'4','',0,'/drugoe/kraska-yablonka-f14-kg',0,'kraska-yablonka-f14-kg','Краска \"Яблонька\" ф.14 кг','','','','','','',544,1,'ведро',1.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(5,'5','',0,'/drugoe/kraska-yablonka-f-35-kg',0,'kraska-yablonka-f-35-kg','Краска \"Яблонька\" ф. 3,5 кг','','','','','','',544,6,'ведро',1.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(6,'6','',0,'/drugoe/kraska-vd-ak-111',0,'kraska-vd-ak-111','Краска ВД-АК-111','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(7,'7','',0,'/drugoe/kraska-vd-ak-111-belaya-f12-kg',0,'kraska-vd-ak-111-belaya-f12-kg','Краска ВД-АК-111 белая  ф.1,2 кг','','','','','','',544,17,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(8,'8','',0,'/drugoe/kraska-vd-ak-111-belaya-f14-kg',0,'kraska-vd-ak-111-belaya-f14-kg','Краска ВД-АК-111 белая  ф.14 кг','','','','','','',544,66,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(9,'9','',0,'/drugoe/kraska-vd-ak-111-belaya-f25-kg',0,'kraska-vd-ak-111-belaya-f25-kg','Краска ВД-АК-111 белая  ф.25 кг','','','','','','',544,61,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(10,'10','',0,'/drugoe/kraska-vd-ak-111-belaya-f5-kg',0,'kraska-vd-ak-111-belaya-f5-kg','Краска ВД-АК-111 белая  ф.5 кг','','','','','','',544,5,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(11,'11','',0,'/drugoe/kraska-vd-ak-111-belaya-f7-kg',0,'kraska-vd-ak-111-belaya-f7-kg','Краска ВД-АК-111 белая  ф.7 кг','','','','','','',544,4,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(12,'12','',0,'/drugoe/kraska-vd-ak-111-razlichnih-pastelnih-tsvetov-f14-kg',0,'kraska-vd-ak-111-razlichnih-pastelnih-tsvetov-f14-kg','Краска ВД-АК-111 различных пастельных цветов ф.14 кг','','','','','','',544,19,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(13,'13','',0,'/drugoe/kraska-vd-ak-111-razlichnih-pastelnih-tsvetov-f25-kg',0,'kraska-vd-ak-111-razlichnih-pastelnih-tsvetov-f25-kg','Краска ВД-АК-111 различных пастельных цветов ф.25 кг','','','','','','',544,1,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(14,'14','',0,'/drugoe/kraska-vd-ak-111-razlichnih-nasishchennih-tsvetov-f14-kg',0,'kraska-vd-ak-111-razlichnih-nasishchennih-tsvetov-f14-kg','Краска ВД-АК-111 различных насыщенных цветов ф.14 кг','','','','','','',544,5,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(15,'15','',0,'/drugoe/kraska-vd-ak-111-razlichnih-nasishchennih-tsvetov-f25-kg',0,'kraska-vd-ak-111-razlichnih-nasishchennih-tsvetov-f25-kg','Краска ВД-АК-111 различных насыщенных цветов ф.25 кг','','','','','','',544,2,'ведро',6.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(16,'16','',0,'/drugoe/krask-ellay',0,'krask-ellay','Краск «Ellay»','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(17,'17','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-belaya-f-12-kg-c',0,'kraska-vodno-dispersionnaya-ellay-belaya-f-12-kg-c','Краска водно-дисперсионная \"ELLAY\"  белая ф. 1,2 кг','','','','','','',544,4,'ведро',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(18,'18','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-belaya-f-35-kg',0,'kraska-vodno-dispersionnaya-ellay-belaya-f-35-kg','Краска водно-дисперсионная \"ELLAY\"  белая ф. 3,5 кг','','','','','','',544,1,'ведро',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(19,'19','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-belaya-f-12-kg',0,'kraska-vodno-dispersionnaya-ellay-belaya-f-12-kg','Краска водно-дисперсионная \"ELLAY\"  белая ф. 12 кг','','','','','','',544,1,'ведро',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(20,'20','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-belaya-f-25-kg',0,'kraska-vodno-dispersionnaya-ellay-belaya-f-25-kg','Краска водно-дисперсионная \"ELLAY\"  белая ф. 25 кг','','','','','','',544,16,'ведро',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(21,'21','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-razlichnih-tsvetov-f-12-kg',0,'kraska-vodno-dispersionnaya-ellay-razlichnih-tsvetov-f-12-kg','Краска водно-дисперсионная \"ELLAY\" различных цветов ф. 12 кг','','','','','','',544,37,'банк.',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(22,'22','',0,'/drugoe/kraska-vodno-dispersionnaya-ellay-razlichnih-tsvetov-f-25-kg-',0,'kraska-vodno-dispersionnaya-ellay-razlichnih-tsvetov-f-25-kg-','Краска водно-дисперсионная  \"ELLAY\" различных цветов ф. 25 кг ','','','','','','',544,186,'банк.',16.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(23,'23','',0,'/drugoe/grunt-emal-bella',0,'grunt-emal-bella','Грунт-Эмаль \"Белла\"','','','','','','',544,6,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(24,'24','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zheltaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-zheltaya-f-08-kg','Грунт -эмаль \"Белла\" по ржавчине желтая ф. 0,8 кг','','','','','','',544,571,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(25,'25','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zheltaya-f-23-kg',0,'grunt-emal-bella-po-rzhavchine-zheltaya-f-23-kg','Грунт-эмаль \"Белла\" по ржавчине желтая ф. 23 кг','','','','','','',544,25,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(26,'26','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-oranzhevaya-f-08-kg-c',0,'grunt-emal-bella-po-rzhavchine-oranzhevaya-f-08-kg-c','Грунт-эмаль \"Белла\" по ржавчине оранжевая ф. 0,8 кг','','','','','','',544,333,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(27,'27','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-oranzhevaya-f-12-kg',0,'grunt-emal-bella-po-rzhavchine-oranzhevaya-f-12-kg','Грунт-эмаль \"Белла\" по ржавчине оранжевая ф. 12 кг','','','','','','',544,1,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(28,'28','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-oranzhevaya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-oranzhevaya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине оранжевая ф. 3,5 кг','','','','','','',544,192,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(29,'29','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-belaya-f-120-kg',0,'grunt-emal-bella-po-rzhavchine-belaya-f-120-kg','Грунт-эмаль \"Белла\" по ржавчине белая ф. 12,0 кг','','','','','','',544,1,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(30,'30','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-belaya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-belaya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине белая ф. 3,5 кг','','','','','','',544,4,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(31,'31','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-oranzhevaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-oranzhevaya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине оранжевая ф. 0,8 кг','','','','','','',544,195,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(32,'32','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-belaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-belaya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине белая ф. 0,8 кг','','','','','','',544,572,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(33,'33','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zelenaya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-zelenaya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине зеленая ф. 3,5 кг','','','','','','',544,2,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(34,'34','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zelenaya-f08-kg',0,'grunt-emal-bella-po-rzhavchine-zelenaya-f08-kg','Грунт-эмаль \"Белла\" по ржавчине зеленая  ф.0,8 кг','','','','','','',544,477,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(35,'35','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zelenaya-f120-kg',0,'grunt-emal-bella-po-rzhavchine-zelenaya-f120-kg','Грунт-эмаль \"Белла\" по ржавчине зеленая  ф.12,0 кг','','','','','','',544,8,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(36,'36','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-svetlo-seraya-f08-kg',0,'grunt-emal-bella-po-rzhavchine-svetlo-seraya-f08-kg','Грунт-эмаль \"Белла\" по ржавчине светло-серая  ф.0,8 кг','','','','','','',544,375,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(37,'37','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-svetlo-seraya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-svetlo-seraya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине светло-серая  ф. 3,5 кг','','','','','','',544,7,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(38,'38','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-seraya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-seraya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине серая ф. 0,8 кг','','','','','','',544,697,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(39,'39','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-seraya-f12-kg',0,'grunt-emal-bella-po-rzhavchine-seraya-f12-kg','Грунт-эмаль \"Белла\" по ржавчине серая ф.12 кг','','','','','','',544,2,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(40,'40','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-chernaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-chernaya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине черная ф. 0,8 кг','','','','','','',544,659,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(41,'41','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-chernaya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-chernaya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине черная ф. 3,5 кг','','','','','','',544,3,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(42,'42','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-chernaya-f-11-kg',0,'grunt-emal-bella-po-rzhavchine-chernaya-f-11-kg','Грунт-эмаль \"Белла\" по ржавчине черная ф. 11 кг','','','','','','',544,2,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(43,'43','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zheltaya-f-35kg',0,'grunt-emal-bella-po-rzhavchine-zheltaya-f-35kg','Грунт-Эмаль \"Бэлла\" по ржавчине желтая ф. 3,5кг','','','','','','',544,135,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(44,'44','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине мах-коричневая ф. 0,8 кг','','','','','','',544,234,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(45,'45','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f120-kg',0,'grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f120-kg','Грунт-эмаль \"Белла\" по ржавчине мах-коричневая ф.12,0 кг','','','','','','',544,1,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(46,'46','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f30-kg',0,'grunt-emal-bella-po-rzhavchine-mah-korichnevaya-f30-kg','Грунт-эмаль \"Белла\" по ржавчине мах-коричневая ф.3,0 кг','','','','','','',544,51,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(47,'47','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-krasnaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-krasnaya-f-08-kg','Грунт-эмаль \"Белла\" по ржавчине красная  ф. 0,8 кг','','','','','','',544,440,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(48,'48','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-krasnaya-f-120-kg',0,'grunt-emal-bella-po-rzhavchine-krasnaya-f-120-kg','Грунт-эмаль \"Белла\" по ржавчине красная ф. 12,0 кг','','','','','','',544,2,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(49,'49','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-krasno-korichnf-08-kg',0,'grunt-emal-bella-po-rzhavchine-krasno-korichnf-08-kg','Грунт-эмаль \"Белла\" по ржавчине красно-коричн.ф. 0,8 кг','','','','','','',544,487,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(50,'50','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zheltaya-f-30-kg',0,'grunt-emal-bella-po-rzhavchine-zheltaya-f-30-kg','Грунт-эмаль \"Белла\" по ржавчине желтая ф. 3,0 кг','','','','','','',544,121,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(51,'51','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-zheltaya-f120-kg',0,'grunt-emal-bella-po-rzhavchine-zheltaya-f120-kg','Грунт-Эмаль \"Белла\" по ржавчине желтая ф.12,0 кг','','','','','','',544,13,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(52,'52','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-serebristaya-bistrosohnushchaya-f-07-kg',0,'grunt-emal-bella-po-rzhavchine-serebristaya-bistrosohnushchaya-f-07-kg','Грунт-Эмаль \"Белла\" по ржавчине серебристая быстросохнущая ф. 0,7 кг','','','','','','',544,98,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(53,'53','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-serebristaya-bistrosohnushchaya-f-25kg',0,'grunt-emal-bella-po-rzhavchine-serebristaya-bistrosohnushchaya-f-25kg','Грунт-Эмаль \"Белла\" по ржавчине серебристая быстросохнущая ф. 2,5кг','','','','','','',544,17,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(54,'54','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-sinyaya-f-08-kg',0,'grunt-emal-bella-po-rzhavchine-sinyaya-f-08-kg','Грунт-Эмаль \"Белла\" по ржавчине синяя ф 0,8 кг','','','','','','',544,569,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(55,'55','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-sinyaya-f-35-kg',0,'grunt-emal-bella-po-rzhavchine-sinyaya-f-35-kg','Грунт-эмаль \"Белла\" по ржавчине синяя ф. 3,5 кг','','','','','','',544,4,'банк.',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(56,'56','',0,'/drugoe/grunt-emal-bella-po-rzhavchine-sinyaya-f110-kg',0,'grunt-emal-bella-po-rzhavchine-sinyaya-f110-kg','Грунт-эмаль \"Белла\" по ржавчине синяя  ф.11,0 кг','','','','','','',544,2,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(57,'57','',0,'/drugoe/grunt-emal-bellapo-rzhavchine-temno-seraya-f-230-kg',0,'grunt-emal-bellapo-rzhavchine-temno-seraya-f-230-kg','Грунт-эмаль \"Белла\"по ржавчине темно-серая  ф. 23,0 кг','','','','','','',544,41,'ведро',23.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(58,'58','',0,'/drugoe/gruntovka-volat',0,'gruntovka-volat','Грунтовка «Волат»','','','','','','',544,1,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(59,'59','',0,'/drugoe/gruntovka-vodno-dispersionnaya-volat-krasno-korichnevaya-f-120-kg',0,'gruntovka-vodno-dispersionnaya-volat-krasno-korichnevaya-f-120-kg','Грунтовка водно-дисперсионная \"Волат\" красно-коричневая ф. 12,0 кг','','','','','','',544,3,'ведро',58.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(60,'60','',0,'/drugoe/gruntovka-vd-ak-01',0,'gruntovka-vd-ak-01','Грунтовка ВД-АК-01','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(61,'61','',0,'/drugoe/gruntovka-vd-ak-01-bestsvetnaya-f-5-kg',0,'gruntovka-vd-ak-01-bestsvetnaya-f-5-kg','Грунтовка ВД-АК-01 бесцветная ф. 5 кг','','','','','','',544,7,'кнстр.',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(62,'62','',0,'/drugoe/gruntovka-vd-ak-01-bestsvetnaya-f-10-kg',0,'gruntovka-vd-ak-01-bestsvetnaya-f-10-kg','Грунтовка ВД-АК-01 бесцветная ф. 10 кг','','','','','','',544,20,'кнстр.',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(63,'63','',0,'/drugoe/gruntovka-vd-ak-01-bestsvetnaya-f-20-kg',0,'gruntovka-vd-ak-01-bestsvetnaya-f-20-kg','Грунтовка ВД-АК-01 бесцветная ф. 20 кг','','','','','','',544,20,'кнстр.',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(64,'64','',0,'/drugoe/gruntovka-vd-ak-01-kontsentrat-f-100-kg-',0,'gruntovka-vd-ak-01-kontsentrat-f-100-kg-','Грунтовка ВД-АК-01 концентрат  ф. 10,0 кг ','','','','','','',544,3,'ведро',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(65,'65','',0,'/drugoe/gruntovka-vd-ak-01-kontsentrat-f-20-kg',0,'gruntovka-vd-ak-01-kontsentrat-f-20-kg','Грунтовка ВД-АК-01 концентрат  ф. 2,0 кг','','','','','','',544,18,'кнстр.',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(66,'66','',0,'/drugoe/gruntovka-vd-ak-01-kontsentrat-f-50-kg',0,'gruntovka-vd-ak-01-kontsentrat-f-50-kg','Грунтовка ВД-АК-01 концентрат  ф. 5,0 кг','','','','','','',544,4,'кнстр.',60.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(67,'67','',0,'/drugoe/gruntovka-gf-021',0,'gruntovka-gf-021','Грунтовка ГФ-021','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(68,'68','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-f-25-kg',0,'gruntovka-gf-021-krasno-korichnevaya-f-25-kg','Грунтовка ГФ-021 красно-коричневая ф. 25 кг','','','','','','',544,2,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(69,'69','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-f09-kg',0,'gruntovka-gf-021-krasno-korichnevaya-f09-kg','Грунтовка ГФ-021 красно-коричневая ф.0,9 кг','','','','','','',544,246,'банк.',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(70,'70','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-f-23-kg',0,'gruntovka-gf-021-krasno-korichnevaya-f-23-kg','Грунтовка ГФ-021 красно-коричневая ф. 2,3 кг','','','','','','',544,10,'банк.',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(71,'71','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-f-200-kg',0,'gruntovka-gf-021-krasno-korichnevaya-f-200-kg','Грунтовка ГФ-021 красно-коричневая ф. 20,0 кг','','','','','','',544,1,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(72,'72','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-f14-kg',0,'gruntovka-gf-021-krasno-korichnevaya-f14-kg','Грунтовка ГФ-021 красно-коричневая ф.14 кг','','','','','','',544,57,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(73,'73','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-bistrosohnushchaya-f-35-kg',0,'gruntovka-gf-021-krasno-korichnevaya-bistrosohnushchaya-f-35-kg','Грунтовка ГФ-021 красно-коричневая быстросохнущая ф. 3,5 кг','','','','','','',544,107,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(74,'74','',0,'/drugoe/gruntovka-gf-021-krasno-korichnevaya-bistrosohnushchaya-f-40-kg',0,'gruntovka-gf-021-krasno-korichnevaya-bistrosohnushchaya-f-40-kg','Грунтовка ГФ-021 красно-коричневая быстросохнущая ф. 4,0 кг','','','','','','',544,4,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(75,'75','',0,'/drugoe/gruntovka-gf-021-seraya-f09-kg',0,'gruntovka-gf-021-seraya-f09-kg','Грунтовка ГФ-021 серая ф.0,9 кг','','','','','','',544,367,'банк.',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(76,'76','',0,'/drugoe/gruntovka-gf-021-seraya-f23-kg',0,'gruntovka-gf-021-seraya-f23-kg','Грунтовка ГФ-021 серая ф.2,3 кг','','','','','','',544,85,'банк.',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(77,'77','',0,'/drugoe/gruntovka-gf-021-seraya-bistrosohnushchaya-f-35-kg',0,'gruntovka-gf-021-seraya-bistrosohnushchaya-f-35-kg','Грунтовка ГФ-021 серая быстросохнущая  ф. 3,5 кг','','','','','','',544,115,'ведро',67.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(78,'78','',0,'/drugoe/gruntovka-fl-03k-',0,'gruntovka-fl-03k-','Грунтовка ФЛ-03К ','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(79,'79','',0,'/drugoe/gruntovka-fl-03k-krasno-korichnevaya-s-sikkativom-f-23280-kg',0,'gruntovka-fl-03k-krasno-korichnevaya-s-sikkativom-f-23280-kg','Грунтовка ФЛ-03К Красно-коричневая с сиккативом ф. 23,280 кг','','','','','','',544,4,'ведро',78.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(80,'80','',0,'/drugoe/kraska-betonakril',0,'kraska-betonakril','Краска \"Бетонакрил\"','','','','','','',544,501,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(81,'81','',0,'/drugoe/kraska-betonakril-belaya-f-14-kg',0,'kraska-betonakril-belaya-f-14-kg','Краска \"Бетонакрил\" белая ф. 14 кг','','','','','','',544,39,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(82,'82','',0,'/drugoe/kraska-betonakril-belaya-f-35-kg',0,'kraska-betonakril-belaya-f-35-kg','Краска \"Бетонакрил\" белая ф. 3,5 кг','','','','','','',544,79,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(83,'83','',0,'/drugoe/kraska-betonakril-belaya-f-40-kg',0,'kraska-betonakril-belaya-f-40-kg','Краска \"Бетонакрил\" белая ф. 4,0 кг','','','','','','',544,44,'банк.',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(84,'84','',0,'/drugoe/kraska-betonakril-krasno-korichnevaya-f-140-kg',0,'kraska-betonakril-krasno-korichnevaya-f-140-kg','Краска \"Бетонакрил\" красно-коричневая ф. 14,0 кг','','','','','','',544,22,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(85,'85','',0,'/drugoe/kraska-betonakril-krasno-korichnevaya-f-40-kg',0,'kraska-betonakril-krasno-korichnevaya-f-40-kg','Краска \"Бетонакрил\" красно-коричневая ф. 4,0 кг','','','','','','',544,63,'банк.',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(86,'86','',0,'/drugoe/kraska-betonakril-shokoladnaya-f-14-kg',0,'kraska-betonakril-shokoladnaya-f-14-kg','Краска \"Бетонакрил\" шоколадная ф. 14 кг','','','','','','',544,5,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(87,'87','',0,'/drugoe/kraska-betonakril-shokoladnaya-f-40-kg',0,'kraska-betonakril-shokoladnaya-f-40-kg','Краска \"Бетонакрил\" шоколадная ф. 4,0 кг','','','','','','',544,3,'банк.',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(88,'88','',0,'/drugoe/kraska-betonakril-sinyaya-f140-kg',0,'kraska-betonakril-sinyaya-f140-kg','Краска «Бетонакрил синяя ф.14,0 кг','','','','','','',544,12,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(89,'89','',0,'/drugoe/kraska-betonakril-zelenaya-f14-kg',0,'kraska-betonakril-zelenaya-f14-kg','Краска «Бетонакрил» зеленая  ф.14 кг','','','','','','',544,9,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(90,'90','',0,'/drugoe/kraska-betonakril-zelenaya-f40-kg',0,'kraska-betonakril-zelenaya-f40-kg','Краска «Бетонакрил» зеленая  ф.4,0 кг','','','','','','',544,49,'ведро',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(91,'91','',0,'/drugoe/kraska-betonakril-seraya-f140',0,'kraska-betonakril-seraya-f140','Краска «Бетонакрил» серая  ф.14,0','','','','','','',544,51,' ',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(92,'92','',0,'/drugoe/kraska-betonakril-seraya-f40-kg',0,'kraska-betonakril-seraya-f40-kg','Краска «Бетонакрил» серая  ф.4,0 кг','','','','','','',544,125,'банк.',80.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(93,'93','',0,'/drugoe/lak-valven',0,'lak-valven','Лак \"VALVEN\"','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(94,'94','',0,'/drugoe/lak-valvendlya-parketa-glyantseviy-f-10-kg',0,'lak-valvendlya-parketa-glyantseviy-f-10-kg','Лак \"VALVEN\"для паркета глянцевый ф. 1,0 кг','','','','','','',544,1,'ведро',93.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(95,'95','',0,'/drugoe/lak-valvendlya-parketa-glyantseviy-f-20-kg',0,'lak-valvendlya-parketa-glyantseviy-f-20-kg','Лак \"VALVEN\"для паркета глянцевый ф. 2,0 кг','','','','','','',544,5,'ведро',93.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(96,'96','',0,'/drugoe/lak-valvendlya-parketa-matoviy-f-10-kg',0,'lak-valvendlya-parketa-matoviy-f-10-kg','Лак \"VALVEN\"для паркета матовый ф. 1,0 кг','','','','','','',544,12,'ведро',93.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(97,'97','',0,'/drugoe/lak-valvendlya-parketa-matoviy-f-100-kg',0,'lak-valvendlya-parketa-matoviy-f-100-kg','Лак \"VALVEN\"для паркета матовый ф. 10,0 кг','','','','','','',544,20,'ведро',93.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(98,'98','',0,'/drugoe/lak-valvendlya-parketa-matoviy-f-20-kg',0,'lak-valvendlya-parketa-matoviy-f-20-kg','Лак \"VALVEN\"для паркета матовый ф. 2,0 кг','','','','','','',544,42,'ведро',93.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(99,'99','',0,'/drugoe/lak-uralux',0,'lak-uralux','Лак «URALUX»','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(100,'100','',0,'/drugoe/lak-uralux-alkidno-uretanoviy-matoviy-f16-kg',0,'lak-uralux-alkidno-uretanoviy-matoviy-f16-kg','Лак «URALUX» алкидно-уретановый матовый ф.1,6 кг','','','','','','',544,91,'банк.',99.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(101,'101','',0,'/drugoe/lak-uralux-alkidno-uretanoviy-glyantseviy-f16-kg',0,'lak-uralux-alkidno-uretanoviy-glyantseviy-f16-kg','Лак «URALUX» алкидно-уретановый глянцевый ф.1,6 кг','','','','','','',544,99,'банк.',99.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(102,'102','',0,'/drugoe/lak-alkidniy',0,'lak-alkidniy','Лак алкидный','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(103,'103','',0,'/drugoe/lak-alkidniy-f-16-kg',0,'lak-alkidniy-f-16-kg','Лак алкидный ф. 1,6 кг','','','','','','',544,2,'банк.',102.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(104,'104','',0,'/drugoe/lak-alkidniy-f-07-kg',0,'lak-alkidniy-f-07-kg','Лак алкидный ф. 0,7 кг','','','','','','',544,80,'банк.',102.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(105,'105','',0,'/drugoe/tosol-',0,'tosol-','Тосол   ','','','','','','',544,6,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(106,'106','',0,'/drugoe/tosol-a-40-nf-50l54kg',0,'tosol-a-40-nf-50l54kg','Тосол  (А-40)  н/ф 5,0л/5,4кг','','','','','','',544,101,'кнстр.',105.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(107,'107','',0,'/drugoe/antifriz',0,'antifriz','Антифриз','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(108,'108','',0,'/drugoe/antifriz-iceberg-auto-siniy-f5l535-kg',0,'antifriz-iceberg-auto-siniy-f5l535-kg','Антифриз ICEBERG-AUTO синий ф.5л/5,35 кг','','','','','','',544,6,'бут.',107.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(109,'109','',0,'/drugoe/preobrazovatel-rzhavchini-bella',0,'preobrazovatel-rzhavchini-bella','Преобразователь ржавчины \"Белла\"','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(110,'110','',0,'/drugoe/preobrazovatel-rzhavchini-bella-f04l-045-kg',0,'preobrazovatel-rzhavchini-bella-f04l-045-kg','Преобразователь ржавчины \"Белла\"  ф.0,4л/ 0,45 кг','','','','','','',544,1,'бут.',109.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(111,'111','',0,'/drugoe/preobrazovatel-rzhavchini-bella-f08l-089-kg',0,'preobrazovatel-rzhavchini-bella-f08l-089-kg','Преобразователь ржавчины \"Белла\"  ф.0,8л/ 0,89 кг','','','','','','',544,369,'бут.',109.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(112,'112','',0,'/drugoe/preobrazovatel-rzhavchini-bella-f10l-1110-kg',0,'preobrazovatel-rzhavchini-bella-f10l-1110-kg','Преобразователь ржавчины \"Белла\"  ф.1,0л/ 1,110 кг','','','','','','',544,1,'бут.',109.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(113,'113','',0,'/drugoe/preobrazovatel-rzhavchini-bella-f45l-50-kg',0,'preobrazovatel-rzhavchini-bella-f45l-50-kg','Преобразователь ржавчины \"Белла\"  ф.4,5л/ 5,0 кг','','','','','','',544,12,'бут.',109.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(114,'114','',0,'/drugoe/preobrazovatel-rzhavchini-bella-f200l220-kg',0,'preobrazovatel-rzhavchini-bella-f200l220-kg','Преобразователь ржавчины \"Белла\"  ф20,0л/22,0 кг','','','','','','',544,2,'кнстр.',109.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(115,'115','',0,'/drugoe/rastvoritel-norasolv',0,'rastvoritel-norasolv','Растворитель \"NORASOLV\"','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(116,'116','',0,'/drugoe/rastvoritel-norasolv-f04-l-035-kg',0,'rastvoritel-norasolv-f04-l-035-kg','Растворитель \"NORASOLV\"  ф.0,4 л/ 0,35 кг','','','','','','',544,4,'бут.',115.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(117,'117','',0,'/drugoe/rastvoritel-norasolv-f08-l-07-kg',0,'rastvoritel-norasolv-f08-l-07-kg','Растворитель \"NORASOLV\"  ф.0,8 л/ 0,7 кг','','','','','','',544,570,'бут.',115.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(118,'118','',0,'/drugoe/rastvoritel-norasolv-f48-l-422-kg',0,'rastvoritel-norasolv-f48-l-422-kg','Растворитель \"NORASOLV\"  ф.4,8 л/ 4,22 кг','','','','','','',544,0,'кнстр.',115.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(119,'119','',0,'/drugoe/rastvoritel-646',0,'rastvoritel-646','Растворитель 646','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(120,'120','',0,'/drugoe/rastvoritel-646-f-04l-033-kg',0,'rastvoritel-646-f-04l-033-kg','Растворитель 646 ф. 0,4л /0,33 кг','','','','','','',544,17,'бут.',119.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(121,'121','',0,'/drugoe/rastvoritel-646-f-075l063-kg',0,'rastvoritel-646-f-075l063-kg','Растворитель 646 ф. 0,75л/0,63 кг','','','','','','',544,48,'бут.',119.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(122,'122','',0,'/drugoe/rastvoritel-646-f-475l4-kg',0,'rastvoritel-646-f-475l4-kg','Растворитель 646 ф. 4,75л/4 кг','','','','','','',544,0,'кнстр.',119.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(123,'123','',0,'/drugoe/rastvoritel-r-111',0,'rastvoritel-r-111','Растворитель Р-111','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(124,'124','',0,'/drugoe/rastvoritel-r-111-f08-l-062-kg',0,'rastvoritel-r-111-f08-l-062-kg','Растворитель Р-111  ф.0,8 л/ 0,62 кг','','','','','','',544,994,'бут.',123.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(125,'125','',0,'/drugoe/rastvoritel-r-111-f475l-37kg',0,'rastvoritel-r-111-f475l-37kg','Растворитель Р-111  ф.4,75л/ 3,7кг','','','','','','',544,23,'бут.',123.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(126,'126','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-',0,'sostav-dekorativno-zashchitniy-woodart-','Состав декоративно-защитный \"Woodart\" ','','','','','','',544,0,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(127,'127','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f170-kg',0,'sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f170-kg','Состав декоративно-защитный \"Woodart\" бесцветный ф.17,0 кг','','','','','','',544,1,'шт.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(128,'128','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-8-kg',0,'sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-8-kg','Состав декоративно-защитный \"Woodart\" бесцветный ф. 8 кг','','','','','','',544,46,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(129,'129','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-beliy-f8kg',0,'sostav-dekorativno-zashchitniy-woodart-beliy-f8kg','Состав декоративно-защитный \"Woodart\" белый ф.8кг','','','','','','',544,38,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(130,'130','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-beliy-f-06kg',0,'sostav-dekorativno-zashchitniy-woodart-beliy-f-06kg','Состав декоративно-защитный \"Woodart\" белый ф. 0.6кг','','','','','','',544,232,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(131,'131','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-beliy-f-22kg',0,'sostav-dekorativno-zashchitniy-woodart-beliy-f-22kg','Состав декоративно-защитный \"Woodart\" белый ф. 2.2кг','','','','','','',544,116,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(132,'132','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-dub-f-06-kg',0,'sostav-dekorativno-zashchitniy-woodart-dub-f-06-kg','Состав декоративно-защитный \"Woodart\" дуб ф. 0,6 кг','','','','','','',544,236,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(133,'133','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-dub-f-22-kg',0,'sostav-dekorativno-zashchitniy-woodart-dub-f-22-kg','Состав декоративно-защитный \"Woodart\" дуб ф. 2,2 кг','','','','','','',544,150,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(134,'134','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-dub-f-80-kg',0,'sostav-dekorativno-zashchitniy-woodart-dub-f-80-kg','Состав декоративно-защитный \"Woodart\" дуб ф. 8,0 кг','','','','','','',544,26,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(135,'135','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-kashtan-f-06-kg',0,'sostav-dekorativno-zashchitniy-woodart-kashtan-f-06-kg','Состав декоративно-защитный \"WOODART\" каштан ф. 0,6 кг','','','','','','',544,239,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(136,'136','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-kashtan-f-17-kg',0,'sostav-dekorativno-zashchitniy-woodart-kashtan-f-17-kg','Состав декоративно-защитный \"WOODART\" каштан ф. 17 кг','','','','','','',544,2,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(137,'137','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-kashtan-f-22-kg',0,'sostav-dekorativno-zashchitniy-woodart-kashtan-f-22-kg','Состав декоративно-защитный \"WOODART\" каштан ф. 2,2 кг','','','','','','',544,172,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(138,'138','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-kashtan-f-8-kg',0,'sostav-dekorativno-zashchitniy-woodart-kashtan-f-8-kg','Состав декоративно-защитный \"WOODART\" каштан ф. 8 кг','','','','','','',544,36,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(139,'139','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-oliva-f-06-kg',0,'sostav-dekorativno-zashchitniy-woodart-oliva-f-06-kg','Состав декоративно-защитный \"Woodart\" олива ф. 0,6 кг','','','','','','',544,276,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(140,'140','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-oliva-f-22-kg',0,'sostav-dekorativno-zashchitniy-woodart-oliva-f-22-kg','Состав декоративно-защитный \"Woodart\" олива ф. 2,2 кг','','','','','','',544,161,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(141,'141','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-oliva-f-8-kg',0,'sostav-dekorativno-zashchitniy-woodart-oliva-f-8-kg','Состав декоративно-защитный \"Woodart\" олива ф. 8 кг','','','','','','',544,19,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(142,'142','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-palisandr-f-06-kg',0,'sostav-dekorativno-zashchitniy-woodart-palisandr-f-06-kg','Состав декоративно-защитный \"Woodart\" палисандр ф. 0,6 кг','','','','','','',544,269,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(143,'143','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-palisandr-f-22-kg',0,'sostav-dekorativno-zashchitniy-woodart-palisandr-f-22-kg','Состав декоративно-защитный \"Woodart\" палисандр ф. 2,2 кг','','','','','','',544,131,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(144,'144','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-palisandr-f-8-kg',0,'sostav-dekorativno-zashchitniy-woodart-palisandr-f-8-kg','Состав декоративно-защитный \"Woodart\" палисандр ф. 8 кг','','','','','','',544,24,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(145,'145','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-sosna-f06-kg',0,'sostav-dekorativno-zashchitniy-woodart-sosna-f06-kg','Состав декоративно-защитный \"WOODART\" сосна  ф.0,6 кг','','','','','','',544,187,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(146,'146','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-sosna-f22-kg',0,'sostav-dekorativno-zashchitniy-woodart-sosna-f22-kg','Состав декоративно-защитный \"WOODART\" сосна  ф.2,2 кг','','','','','','',544,94,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(147,'147','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-sosna-f-8-kg',0,'sostav-dekorativno-zashchitniy-woodart-sosna-f-8-kg','Состав декоративно-защитный \"WOODART\" сосна  ф. 8 кг','','','','','','',544,22,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(148,'148','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-06-kg',0,'sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-06-kg','Состав декоративно-защитный \"Woodart\" бесцветный ф. 0,6 кг','','','','','','',544,215,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(149,'149','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-22-kg',0,'sostav-dekorativno-zashchitniy-woodart-bestsvetniy-f-22-kg','Состав декоративно-защитный \"Woodart\" бесцветный ф. 2,2 кг','','','','','','',544,207,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(150,'150','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodartgretskiy-oreh-f06-kg',0,'sostav-dekorativno-zashchitniy-woodartgretskiy-oreh-f06-kg','Состав декоративно-защитный \"Woodart\"грецкий орех ф.0,6 кг','','','','','','',544,35,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(151,'151','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodartgretskiy-oreh-f22kg',0,'sostav-dekorativno-zashchitniy-woodartgretskiy-oreh-f22kg','Состав декоративно-защитный \"Woodart\"грецкий орех ф.2,2кг','','','','','','',544,34,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(152,'152','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodartmahagon-f06-kg',0,'sostav-dekorativno-zashchitniy-woodartmahagon-f06-kg','Состав декоративно-защитный \"Woodart\"махагон ф.0,6 кг','','','','','','',544,120,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(153,'153','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodartmahagon-f22kg',0,'sostav-dekorativno-zashchitniy-woodartmahagon-f22kg','Состав декоративно-защитный \"Woodart\"махагон ф.2,2кг','','','','','','',544,120,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(154,'154','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodartmahagon-f80kg',0,'sostav-dekorativno-zashchitniy-woodartmahagon-f80kg','Состав декоративно-защитный \"Woodart\"махагон ф.8,0кг','','','','','','',544,25,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(155,'155','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-chernoe-derevo-f06-kg',0,'sostav-dekorativno-zashchitniy-woodart-chernoe-derevo-f06-kg','Состав декоративно-защитный \"Woodart\" черное дерево ф.0,6 кг','','','','','','',544,36,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(156,'156','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-chernoe-derevo-f22kg',0,'sostav-dekorativno-zashchitniy-woodart-chernoe-derevo-f22kg','Состав декоративно-защитный \"Woodart\" черное дерево Ф.2,2КГ','','','','','','',544,2,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(157,'157','',0,'/drugoe/sostav-dekorativno-zashchitniy-woodart-tik-f-06kg',0,'sostav-dekorativno-zashchitniy-woodart-tik-f-06kg','Состав декоративно-защитный \"Woodart\" ТИК ф. 0,6кг','','','','','','',544,77,'банк.',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(158,'158','',0,'/drugoe/sostav-dekorativno-zashchitniywoodart-tik-f-22kg',0,'sostav-dekorativno-zashchitniywoodart-tik-f-22kg','Состав декоративно-защитный\"Woodart\" ТИК ф. 2,2кг','','','','','','',544,12,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(159,'159','',0,'/drugoe/sostav-dekorativno-zashchitniywoodart-tik-f-80kg',0,'sostav-dekorativno-zashchitniywoodart-tik-f-80kg','Состав декоративно-защитный\"Woodart\" ТИК ф. 8,0кг','','','','','','',544,7,'ведро',126.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(160,'160','',0,'/drugoe/emal-pf-115',0,'emal-pf-115','Эмаль ПФ-115','','','','','','',544,10,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(161,'161','',0,'/drugoe/emal-pf-115-belaya-f-08-kg',0,'emal-pf-115-belaya-f-08-kg','Эмаль ПФ-115 белая ф. 0,8 кг','','','','','','',544,682,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(162,'162','',0,'/drugoe/emal-pf-115-belaya-f12-kg',0,'emal-pf-115-belaya-f12-kg','Эмаль ПФ-115 белая ф.12 кг','','','','','','',544,102,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(163,'163','',0,'/drugoe/emal-pf-115-belaya-f3-kg',0,'emal-pf-115-belaya-f3-kg','Эмаль ПФ-115 белая ф.3 кг','','','','','','',544,230,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(164,'164','',0,'/drugoe/emal-pf-115-belaya-f35-kg',0,'emal-pf-115-belaya-f35-kg','Эмаль ПФ-115 белая ф.3,5 кг','','','','','','',544,291,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(165,'165','',0,'/drugoe/emal-pf-115-belaya-f-18-kg',0,'emal-pf-115-belaya-f-18-kg','Эмаль ПФ-115 белая ф. 1,8 кг','','','','','','',544,878,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(166,'166','',0,'/drugoe/emal-pf-115-biryuzovaya-f08-kg',0,'emal-pf-115-biryuzovaya-f08-kg','Эмаль ПФ-115 бирюзовая ф.0,8 кг','','','','','','',544,208,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(167,'167','',0,'/drugoe/emal-pf-115-biryuzovaya-f18-kg',0,'emal-pf-115-biryuzovaya-f18-kg','Эмаль ПФ-115 бирюзовая ф.1,8 кг','','','','','','',544,355,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(168,'168','',0,'/drugoe/emal-pf-115-biryuzovaya-f35-kg',0,'emal-pf-115-biryuzovaya-f35-kg','Эмаль ПФ-115 бирюзовая ф.3,5 кг','','','','','','',544,20,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(169,'169','',0,'/drugoe/emal-pf-115-vishnevaya-f08kg',0,'emal-pf-115-vishnevaya-f08kg','Эмаль ПФ-115 вишневая ф.0,8кг','','','','','','',544,242,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(170,'170','',0,'/drugoe/emal-pf-115-vishnevaya-f18kg',0,'emal-pf-115-vishnevaya-f18kg','Эмаль ПФ-115 вишневая ф.1,8кг','','','','','','',544,4,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(171,'171','',0,'/drugoe/emal-pf-115-zheltaya-f08-kg',0,'emal-pf-115-zheltaya-f08-kg','Эмаль ПФ-115 желтая ф.0,8 кг','','','','','','',544,48,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(172,'172','',0,'/drugoe/emal-pf-115-zheltaya-f18-kg',0,'emal-pf-115-zheltaya-f18-kg','Эмаль ПФ-115 желтая ф.1,8 кг','','','','','','',544,486,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(173,'173','',0,'/drugoe/emal-pf-115-zheltaya-f35-kg',0,'emal-pf-115-zheltaya-f35-kg','Эмаль ПФ-115 желтая ф.3,5 кг','','','','','','',544,2,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(174,'174','',0,'/drugoe/emal-pf-115-zashchitnaya-f08-kg',0,'emal-pf-115-zashchitnaya-f08-kg','Эмаль ПФ-115 защитная ф.0,8 кг','','','','','','',544,260,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(175,'175','',0,'/drugoe/emal-pf-115-zashchitnaya-f12-kg',0,'emal-pf-115-zashchitnaya-f12-kg','Эмаль ПФ-115 защитная ф.12 кг','','','','','','',544,44,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(176,'176','',0,'/drugoe/emal-pf-115-zashchitnaya-f60-kg',0,'emal-pf-115-zashchitnaya-f60-kg','Эмаль ПФ-115 защитная ф.6,0 кг','','','','','','',544,99,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(177,'177','',0,'/drugoe/emal-pf-115-zashchitnaya-f18-kg',0,'emal-pf-115-zashchitnaya-f18-kg','Эмаль ПФ-115 защитная ф.1,8 кг','','','','','','',544,86,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(178,'178','',0,'/drugoe/emal-pf-115-zelenaya-f08-kg',0,'emal-pf-115-zelenaya-f08-kg','Эмаль ПФ-115 зеленая ф.0,8 кг','','','','','','',544,186,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(179,'179','',0,'/drugoe/emal-pf-115-zelenaya-f-35-kg',0,'emal-pf-115-zelenaya-f-35-kg','Эмаль ПФ-115 зеленая ф. 3,5 кг','','','','','','',544,71,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(180,'180','',0,'/drugoe/emal-pf-115-zelenaya-f18-kg',0,'emal-pf-115-zelenaya-f18-kg','Эмаль ПФ-115 зеленая ф.1,8 кг','','','','','','',544,60,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(181,'181','',0,'/drugoe/emal-pf-115-zelenaya-f12-kg',0,'emal-pf-115-zelenaya-f12-kg','Эмаль ПФ-115 зеленая ф.12 кг','','','','','','',544,1,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(182,'182','',0,'/drugoe/emal-pf-115-krasnaya-f-08-kg',0,'emal-pf-115-krasnaya-f-08-kg','Эмаль ПФ-115 красная ф. 0,8 кг','','','','','','',544,336,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(183,'183','',0,'/drugoe/emal-pf-115-krasnaya-f-18-kg',0,'emal-pf-115-krasnaya-f-18-kg','Эмаль ПФ-115 красная  ф. 1,8 кг','','','','','','',544,611,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(184,'184','',0,'/drugoe/emal-pf-115-krasnaya-f35-kg',0,'emal-pf-115-krasnaya-f35-kg','Эмаль ПФ-115 красная ф.3,5 кг','','','','','','',544,177,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(185,'185','',0,'/drugoe/emal-pf-115-krasnaya-f-12-kg',0,'emal-pf-115-krasnaya-f-12-kg','Эмаль ПФ-115 красная ф. 12 кг','','','','','','',544,0,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(186,'186','',0,'/drugoe/emal-pf-115-mayskaya-zelen-f08-kg',0,'emal-pf-115-mayskaya-zelen-f08-kg','Эмаль ПФ-115 майская зелень  ф.0,8 кг','','','','','','',544,19,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(187,'187','',0,'/drugoe/emal-pf-115-oranzhevaya-f08-kg',0,'emal-pf-115-oranzhevaya-f08-kg','Эмаль ПФ-115 оранжевая ф.0,8 кг','','','','','','',544,173,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(188,'188','',0,'/drugoe/emal-pf-115-oranzhevaya-f18-kg',0,'emal-pf-115-oranzhevaya-f18-kg','Эмаль ПФ-115 оранжевая ф.1,8 кг','','','','','','',544,33,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(189,'189','',0,'/drugoe/emal-pf-115-oranzhevaya-f55-kg',0,'emal-pf-115-oranzhevaya-f55-kg','Эмаль ПФ-115 оранжевая ф.5,5 кг','','','','','','',544,55,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(190,'190','',0,'/drugoe/emal-pf-115-svetlo-golubaya-f-12-kg',0,'emal-pf-115-svetlo-golubaya-f-12-kg','Эмаль ПФ-115 светло-голубая ф. 12 кг','','','','','','',544,46,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(191,'191','',0,'/drugoe/emal-pf-115-svetlo-golubaya-f-35-kg',0,'emal-pf-115-svetlo-golubaya-f-35-kg','Эмаль ПФ-115 светло-голубая ф. 3,5 кг','','','','','','',544,163,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(192,'192','',0,'/drugoe/emal-pf-115-svetlo-golubaya-f18-kg',0,'emal-pf-115-svetlo-golubaya-f18-kg','Эмаль ПФ-115 светло-голубая ф.1,8 кг','','','','','','',544,708,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(193,'193','',0,'/drugoe/emal-pf-115-svetlo-golubaya-f-08-kg',0,'emal-pf-115-svetlo-golubaya-f-08-kg','Эмаль ПФ-115 светло-голубая ф. 0,8 кг','','','','','','',544,625,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(194,'194','',0,'/drugoe/emal-pf-115-svetlo-seraya-f18-kg',0,'emal-pf-115-svetlo-seraya-f18-kg','Эмаль ПФ-115 светло-серая ф.1,8 кг','','','','','','',544,5,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(195,'195','',0,'/drugoe/emal-pf-115-svetlo-seraya-f35-kg',0,'emal-pf-115-svetlo-seraya-f35-kg','Эмаль ПФ-115 светло-серая ф.3,5 кг','','','','','','',544,5,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(196,'196','',0,'/drugoe/emal-pf-115-svetlo-seraya-f-08-kg',0,'emal-pf-115-svetlo-seraya-f-08-kg','Эмаль ПФ-115 светло-серая ф. 0,8 кг','','','','','','',544,14,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(197,'197','',0,'/drugoe/emal-pf-115-seraya-f-08-kg',0,'emal-pf-115-seraya-f-08-kg','Эмаль ПФ-115 серая ф. 0,8 кг','','','','','','',544,245,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(198,'198','',0,'/drugoe/emal-pf-115-seraya-f-18-kg',0,'emal-pf-115-seraya-f-18-kg','Эмаль ПФ-115 серая ф. 1,8 кг','','','','','','',544,588,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(199,'199','',0,'/drugoe/emal-pf-115-seraya-f-120-kg',0,'emal-pf-115-seraya-f-120-kg','Эмаль ПФ-115 серая ф. 12,0 кг','','','','','','',544,1,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(200,'200','',0,'/drugoe/emal-pf-115-seraya-f-35-kg',0,'emal-pf-115-seraya-f-35-kg','Эмаль ПФ-115 серая ф. 3,5 кг','','','','','','',544,105,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(201,'201','',0,'/drugoe/emal-pf-115-sinyaya-f18-kg',0,'emal-pf-115-sinyaya-f18-kg','Эмаль ПФ-115 синяя ф.1,8 кг','','','','','','',544,74,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(202,'202','',0,'/drugoe/emal-pf-115-sinyaya-f-35-kg',0,'emal-pf-115-sinyaya-f-35-kg','Эмаль ПФ-115 синяя ф. 3,5 кг','','','','','','',544,108,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(203,'203','',0,'/drugoe/emal-pf-115-sinyaya-f-08-kg',0,'emal-pf-115-sinyaya-f-08-kg','Эмаль ПФ-115 синяя ф. 0,8 кг','','','','','','',544,494,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(204,'204','',0,'/drugoe/emal-pf-115-sinyaya-f-12-kg',0,'emal-pf-115-sinyaya-f-12-kg','Эмаль ПФ-115 синяя ф. 12 кг','','','','','','',544,46,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(205,'205','',0,'/drugoe/emal-pf-115-slon-kost-f-18-kg',0,'emal-pf-115-slon-kost-f-18-kg','Эмаль ПФ-115 слон-кость ф. 1,8 кг','','','','','','',544,116,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(206,'206','',0,'/drugoe/emal-pf-115-fistashkovaya-f-18-kg',0,'emal-pf-115-fistashkovaya-f-18-kg','Эмаль ПФ-115 фисташковая  ф. 1,8 кг','','','','','','',544,318,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(207,'207','',0,'/drugoe/emal-pf-115-chernaya-f18-kg',0,'emal-pf-115-chernaya-f18-kg','Эмаль ПФ-115 черная ф.1,8 кг','','','','','','',544,118,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(208,'208','',0,'/drugoe/emal-pf-115-chernaya-f-08-kg',0,'emal-pf-115-chernaya-f-08-kg','Эмаль ПФ-115 черная ф. 0,8 кг','','','','','','',544,201,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(209,'209','',0,'/drugoe/emal-pf-115-chernaya-f11-kg',0,'emal-pf-115-chernaya-f11-kg','Эмаль ПФ-115 черная ф.11 кг','','','','','','',544,47,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(210,'210','',0,'/drugoe/emal-pf-115-chernaya-f35-kg',0,'emal-pf-115-chernaya-f35-kg','Эмаль ПФ-115 черная ф.3,5 кг','','','','','','',544,477,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(211,'211','',0,'/drugoe/emal-pf-115-shokoladnaya-f18-kg',0,'emal-pf-115-shokoladnaya-f18-kg','Эмаль ПФ-115 шоколадная ф.1,8 кг','','','','','','',544,1,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(212,'212','',0,'/drugoe/emal-pf-115-shokoladnaya-f12-kg',0,'emal-pf-115-shokoladnaya-f12-kg','Эмаль ПФ-115 шоколадная ф.12 кг','','','','','','',544,18,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(213,'213','',0,'/drugoe/emal-pf-115-shokoladnaya-f27-kg',0,'emal-pf-115-shokoladnaya-f27-kg','Эмаль ПФ-115 шоколадная ф.2,7 кг','','','','','','',544,125,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(214,'214','',0,'/drugoe/emal-pf-115-shokoladnaya-f35-kg',0,'emal-pf-115-shokoladnaya-f35-kg','Эмаль ПФ-115 шоколадная ф.3,5 кг','','','','','','',544,8,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(215,'215','',0,'/drugoe/emal-pf-115-shokoladnaya-f60-kg',0,'emal-pf-115-shokoladnaya-f60-kg','Эмаль ПФ-115 шоколадная ф.6,0 кг','','','','','','',544,41,'ведро',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(216,'216','',0,'/drugoe/emal-pf-115-shokoladnaya-f08kg',0,'emal-pf-115-shokoladnaya-f08kg','Эмаль ПФ-115 шоколадная ф.0,8кг','','','','','','',544,198,'банк.',160.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(217,'217','',0,'/drugoe/emal-pf-266',0,'emal-pf-266','Эмаль \"ПФ-266\"','','','','','','',544,392,'',0.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(218,'218','',0,'/drugoe/emal-pf-266-lyuks-zolotisto-zheltaya-f-35-kg',0,'emal-pf-266-lyuks-zolotisto-zheltaya-f-35-kg','Эмаль ПФ-266 ЛЮКС золотисто желтая ф. 3,5 кг','','','','','','',544,70,'банк.',217.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','',''),(219,'219','',0,'/drugoe/emal-pf-266-lyuks-zolotisto-zheltaya-f-08-kg',0,'emal-pf-266-lyuks-zolotisto-zheltaya-f-08-kg','Эмаль ПФ-266 ЛЮКС золотисто-желтая ф. 0,8 кг','','','','','','',544,306,'банк.',217.00,1,0,'1970-01-01 00:00:00','1970-01-01 00:00:00','','','');
/*!40000 ALTER TABLE `shop_products` ENABLE KEYS */;

--
-- Table structure for table `site_params`
--

DROP TABLE IF EXISTS `site_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_params` (
  `the_key` varchar(60) NOT NULL,
  `the_value` text NOT NULL,
  `descript` varchar(1023) NOT NULL DEFAULT '',
  `editable` int(2) unsigned NOT NULL DEFAULT '0',
  `provided` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`the_key`),
  KEY `editable` (`editable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_params`
--

/*!40000 ALTER TABLE `site_params` DISABLE KEYS */;
INSERT INTO `site_params` VALUES ('info_email','info@gilletteopt.by','E-Mail для связи',1,0),('info_phone','+375 (29) 777-77-77\n','Телефон',1,0),('marks','[{\"markName\":\"Acura\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Acura\",\"markAlias\":\"acura\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Alfa Romeo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Alfa_Romeo\",\"markAlias\":\"alfa_romeo\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Aston Martin\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Aston_Martin\",\"markAlias\":\"aston_martin\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Audi\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Audi\",\"markAlias\":\"audi\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Bentley\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Bentley\",\"markAlias\":\"bentley\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"BMW\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/BMW\",\"markAlias\":\"bmw\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Brilliance\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Brilliance\",\"markAlias\":\"brilliance\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Buick\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Buick\",\"markAlias\":\"buick\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"BYD\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/BYD\",\"markAlias\":\"byd\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Cadillac\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Cadillac\",\"markAlias\":\"cadillac\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Chana\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chana\",\"markAlias\":\"chana\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Chery\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chery\",\"markAlias\":\"chery\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Chevrolet\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chevrolet\",\"markAlias\":\"chevrolet\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Chrysler\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Chrysler\",\"markAlias\":\"chrysler\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Citroen\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Citroen\",\"markAlias\":\"citroen\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Dacia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Dacia\",\"markAlias\":\"dacia\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Daewoo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Daewoo\",\"markAlias\":\"daewoo\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Daihatsu\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Daihatsu\",\"markAlias\":\"daihatsu\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Dodge\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Dodge\",\"markAlias\":\"dodge\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Ferrari\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ferrari\",\"markAlias\":\"ferrari\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Fiat\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Fiat\",\"markAlias\":\"fiat\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Ford\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ford\",\"markAlias\":\"ford\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Ford USA\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ford_USA\",\"markAlias\":\"ford_usa\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Foton\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Foton\",\"markAlias\":\"foton\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Geely\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Geely\",\"markAlias\":\"geely\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"GMC\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/GMC\",\"markAlias\":\"gmc\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Great Wall\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Great_Wall\",\"markAlias\":\"great_wall\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Hafei\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hafei\",\"markAlias\":\"hafei\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Honda\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Honda\",\"markAlias\":\"honda\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Hummer\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hummer\",\"markAlias\":\"hummer\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Hyundai\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Hyundai\",\"markAlias\":\"hyundai\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Infiniti\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Infiniti\",\"markAlias\":\"infiniti\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Isuzu\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Isuzu\",\"markAlias\":\"isuzu\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"JAC\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/JAC\",\"markAlias\":\"jac\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Jaguar\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Jaguar\",\"markAlias\":\"jaguar\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Jeep\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Jeep\",\"markAlias\":\"jeep\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Kia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Kia\",\"markAlias\":\"kia\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Lancia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lancia\",\"markAlias\":\"lancia\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Land Rover\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Land_Rover\",\"markAlias\":\"land_rover\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Lexus\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lexus\",\"markAlias\":\"lexus\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Lifan\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lifan\",\"markAlias\":\"lifan\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Lincoln\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Lincoln\",\"markAlias\":\"lincoln\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Maserati\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Maserati\",\"markAlias\":\"maserati\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Maybach\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Maybach\",\"markAlias\":\"maybach\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Mazda\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mazda\",\"markAlias\":\"mazda\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Mercedes\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mercedes\",\"markAlias\":\"mercedes\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"MG\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/MG\",\"markAlias\":\"mg\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"MINI\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/MINI\",\"markAlias\":\"mini\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Mitsubishi\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Mitsubishi\",\"markAlias\":\"mitsubishi\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Nissan\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Nissan\",\"markAlias\":\"nissan\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Opel\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Opel\",\"markAlias\":\"opel\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Peugeot\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Peugeot\",\"markAlias\":\"peugeot\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Pontiac\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Pontiac\",\"markAlias\":\"pontiac\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Porsche\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Porsche\",\"markAlias\":\"porsche\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Proton\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Proton\",\"markAlias\":\"proton\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Renault\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Renault\",\"markAlias\":\"renault\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Rolls-Royce\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Rolls-Royce\",\"markAlias\":\"rolls-royce\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Rover\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Rover\",\"markAlias\":\"rover\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Saab\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Saab\",\"markAlias\":\"saab\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Seat\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Seat\",\"markAlias\":\"seat\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Skoda\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Skoda\",\"markAlias\":\"skoda\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Smart\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Smart\",\"markAlias\":\"smart\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Ssangyong\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Ssangyong\",\"markAlias\":\"ssangyong\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Subaru\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Subaru\",\"markAlias\":\"subaru\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Suzuki\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Suzuki\",\"markAlias\":\"suzuki\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Toyota\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Toyota\",\"markAlias\":\"toyota\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Volkswagen\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Volkswagen\",\"markAlias\":\"volkswagen\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Volvo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/Volvo\",\"markAlias\":\"volvo\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"ВАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%92%d0%90%d0%97\",\"markAlias\":\"vaz\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"ГАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%93%d0%90%d0%97\",\"markAlias\":\"gaz\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"ЗАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%97%d0%90%d0%97\",\"markAlias\":\"zaz\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Москвич\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%9c%d0%be%d1%81%d0%ba%d0%b2%d0%b8%d1%87\",\"markAlias\":\"moskvich\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"УАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Cars\\/%d0%a3%d0%90%d0%97\",\"markAlias\":\"uaz\",\"categoryAlias\":\"cars\",\"parsed\":\"1\"},{\"markName\":\"Avia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Avia\",\"markAlias\":\"avia\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"DAF\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/DAF\",\"markAlias\":\"daf\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Fiat\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Fiat\",\"markAlias\":\"fiat\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Ford\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Ford\",\"markAlias\":\"ford\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Hyundai\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Hyundai\",\"markAlias\":\"hyundai\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Isuzu\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Isuzu\",\"markAlias\":\"isuzu\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Iveco\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Iveco\",\"markAlias\":\"iveco\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"MAN\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/MAN\",\"markAlias\":\"man\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Mercedes\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Mercedes\",\"markAlias\":\"mercedes\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Mitsubishi\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Mitsubishi\",\"markAlias\":\"mitsubishi\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Neoplan\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Neoplan\",\"markAlias\":\"neoplan\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Nissan\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Nissan\",\"markAlias\":\"nissan\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Renault Trucks\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Renault_Trucks\",\"markAlias\":\"renault_trucks\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Scania\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Scania\",\"markAlias\":\"scania\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Setra\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Setra\",\"markAlias\":\"setra\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Van Hool\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Van_Hool\",\"markAlias\":\"van_hool\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Volkswagen\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Volkswagen\",\"markAlias\":\"volkswagen\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Volvo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/Volvo\",\"markAlias\":\"volvo\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"КАМАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/%d0%9a%d0%90%d0%9c%d0%90%d0%97\",\"markAlias\":\"kamaz\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"МАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Trucks\\/%d0%9c%d0%90%d0%97\",\"markAlias\":\"maz\",\"categoryAlias\":\"trucks\",\"parsed\":\"1\"},{\"markName\":\"Alfa Romeo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Alfa_Romeo\",\"markAlias\":\"alfa_romeo\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Avia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Avia\",\"markAlias\":\"avia\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Chevrolet\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Chevrolet\",\"markAlias\":\"chevrolet\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Citroen\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Citroen\",\"markAlias\":\"citroen\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Daewoo\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Daewoo\",\"markAlias\":\"daewoo\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"DAF\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/DAF\",\"markAlias\":\"daf\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Daihatsu\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Daihatsu\",\"markAlias\":\"daihatsu\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Dodge\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Dodge\",\"markAlias\":\"dodge\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Fiat\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Fiat\",\"markAlias\":\"fiat\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Ford\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ford\",\"markAlias\":\"ford\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Ford USA\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ford_USA\",\"markAlias\":\"ford_usa\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Foton\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Foton\",\"markAlias\":\"foton\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"GMC\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/GMC\",\"markAlias\":\"gmc\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Honda\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Honda\",\"markAlias\":\"honda\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Hyundai\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Hyundai\",\"markAlias\":\"hyundai\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Isuzu\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Isuzu\",\"markAlias\":\"isuzu\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Iveco\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Iveco\",\"markAlias\":\"iveco\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Kia\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Kia\",\"markAlias\":\"kia\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"LDV\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/LDV\",\"markAlias\":\"ldv\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Mazda\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mazda\",\"markAlias\":\"mazda\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Mercedes\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mercedes\",\"markAlias\":\"mercedes\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Mitsubishi\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Mitsubishi\",\"markAlias\":\"mitsubishi\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Nissan\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Nissan\",\"markAlias\":\"nissan\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Opel\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Opel\",\"markAlias\":\"opel\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Peugeot\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Peugeot\",\"markAlias\":\"peugeot\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Renault\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Renault\",\"markAlias\":\"renault\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Renault Trucks\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Renault_Trucks\",\"markAlias\":\"renault_trucks\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Ssangyong\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Ssangyong\",\"markAlias\":\"ssangyong\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Suzuki\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Suzuki\",\"markAlias\":\"suzuki\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Toyota\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Toyota\",\"markAlias\":\"toyota\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"Volkswagen\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/Volkswagen\",\"markAlias\":\"volkswagen\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"},{\"markName\":\"ГАЗ\",\"url\":\"http:\\/\\/exist.ru\\/cat\\/TecDoc\\/Commercial\\/%d0%93%d0%90%d0%97\",\"markAlias\":\"gaz\",\"categoryAlias\":\"commercial\",\"parsed\":\"1\"}]','',0,0),('order_email','ourtravelru@gmail.com','E-Mail для заказов',1,0),('price-list','pricelist_2013-07-17_10-07.jpg','',0,0),('test_param','default_value4','Тестовый параметр',2,0),('use_time','1532380622','',0,0);
/*!40000 ALTER TABLE `site_params` ENABLE KEYS */;

--
-- Table structure for table `site_seo`
--

DROP TABLE IF EXISTS `site_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_seo` (
  `table_name` varchar(63) NOT NULL,
  `primary_value` varchar(127) NOT NULL,
  `title` varchar(2048) NOT NULL DEFAULT '',
  `description` varchar(2048) NOT NULL DEFAULT '',
  `keywords` varchar(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_name`,`primary_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_seo`
--

/*!40000 ALTER TABLE `site_seo` DISABLE KEYS */;
INSERT INTO `site_seo` VALUES ('pages','15','SEO Title ','SEO Description   	','SEO Keywords  ');
/*!40000 ALTER TABLE `site_seo` ENABLE KEYS */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `private_level` tinyint(4) NOT NULL,
  `objects_count` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;

--
-- Table structure for table `temps`
--

DROP TABLE IF EXISTS `temps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'file',
  `object` varchar(255) NOT NULL DEFAULT 'unknown',
  `to_object` varchar(255) NOT NULL DEFAULT 'unknown',
  `file` varchar(2047) NOT NULL DEFAULT '',
  `file_path` varchar(2047) NOT NULL DEFAULT '',
  `data` varchar(4095) NOT NULL DEFAULT '{}',
  `linked_id` int(10) unsigned NOT NULL DEFAULT '0',
  `thetime` datetime NOT NULL,
  `utime` int(10) unsigned NOT NULL DEFAULT '0',
  `uhash` varchar(49) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temps`
--

/*!40000 ALTER TABLE `temps` DISABLE KEYS */;
/*!40000 ALTER TABLE `temps` ENABLE KEYS */;

--
-- Table structure for table `thumbs`
--

DROP TABLE IF EXISTS `thumbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thumbs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(60) NOT NULL DEFAULT 'none',
  `object_id` int(11) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 - down, 2 - up, -1 - complain',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `object_index` (`object_type`,`object_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1212 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thumbs`
--

/*!40000 ALTER TABLE `thumbs` DISABLE KEYS */;
INSERT INTO `thumbs` VALUES (1207,50,'46.216.218.49','comment',901,2,1454584643,1454584643),(1210,50,'46.216.218.49','comment',898,1,1454584660,1454584660),(1211,50,'80.249.93.6','comment',899,2,1454595577,1454595577);
/*!40000 ALTER TABLE `thumbs` ENABLE KEYS */;

--
-- Table structure for table `user_albums`
--

DROP TABLE IF EXISTS `user_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(255) NOT NULL DEFAULT 'default',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT 'default',
  `object_type` varchar(50) NOT NULL DEFAULT '',
  `object_id` int(10) unsigned DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `medias_count` int(10) unsigned NOT NULL DEFAULT '0',
  `picture_id` int(10) unsigned NOT NULL DEFAULT '0',
  `picture_preview` varchar(255) NOT NULL DEFAULT 'default/photo_empty.png',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) NOT NULL DEFAULT '0',
  `date_show` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `category` (`category`),
  KEY `alias` (`alias`(191))
) ENGINE=InnoDB AUTO_INCREMENT=2524 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_albums`
--

/*!40000 ALTER TABLE `user_albums` DISABLE KEYS */;
INSERT INTO `user_albums` VALUES (2523,1,'default',50,'Тест альбом','','default','profile',0,'','',0,72,'2016-11/m50-20161110063225-preview-7b157fcd121009bd4cb69bf9ba47b6a7.jpg',1478701601,1478702969,1478687201);
/*!40000 ALTER TABLE `user_albums` ENABLE KEYS */;

--
-- Table structure for table `user_medias`
--

DROP TABLE IF EXISTS `user_medias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_medias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `type` varchar(50) NOT NULL DEFAULT 'unknown',
  `user_id` int(10) unsigned NOT NULL,
  `object_type` varchar(50) NOT NULL DEFAULT 'unknown',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  `sortnum` int(11) NOT NULL DEFAULT '0',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `picture_orig` varchar(200) NOT NULL,
  `picture` varchar(200) NOT NULL,
  `picture_preview` varchar(200) NOT NULL,
  `preview_params` varchar(255) NOT NULL DEFAULT '[]',
  `picture_data` text NOT NULL COMMENT 'exif data',
  `video_source` varchar(50) NOT NULL DEFAULT '',
  `video_code` varchar(255) NOT NULL DEFAULT '',
  `video_embed` varchar(255) NOT NULL DEFAULT '',
  `video_duration` int(11) NOT NULL DEFAULT '0',
  `content_size` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `picture_orig` (`picture_orig`(191)),
  KEY `created_at` (`created_at`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `acrive` (`active`),
  KEY `type` (`type`),
  KEY `video_code` (`video_code`(191))
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_medias`
--

/*!40000 ALTER TABLE `user_medias` DISABLE KEYS */;
INSERT INTO `user_medias` VALUES (36,1,'video',50,'blog',2,1454417497,1454417507,0,'Когда надо бежать от ПСИХОЛОГА.','Психотерапевты тоже люди, со всеми вытекающими последствиями) Они могут помочь, а могут и навредить.','2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg','2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg','2016-02/v50-20160202125143-preview-2b285094c2ed7a0d4088a00419e0d298.jpg','[]','','youtube','Ck1SkxF2Sak','Ck1SkxF2Sak',549,0),(37,1,'video',50,'blog',2,1454417704,1454417706,0,'Драйв от соревнования с собой вчерашним','Интервью для проекта Pro$to, записанное во время визита в Бишкек с тренингами “Харизма лидера: имидж и мистика, психология и власть” и “Крест лидера”.<br/><br/>Пост в блоге: http://blog.radislavgandapas.com/drive/<br/><br/>Слушайте в интервью:<br/>- Каким ребенком был Радислав, и был ли он лидером в детские годы<br/>- Откуда берется желание стать лидером<br/>- О важности решений, которые мы принимаем каждый день<br/>- Интересный вопрос, который Радиславу еще никто не задавал<br/>- Какого результата достигают посетители тренингов Радислава<br/><br/>__<br/><br/>Официальный сайт Радислава Гандапаса: http://www.radislavgandapas.com/<br/>Блог: http://blog.radislavgandapas.com/<br/><br/>Радислав Гандапас в социальных сетях:<br/><br/>Twitter: http://twitter.com/gandapas<br/>Facebook: https://www.facebook.com/gandapas<br/>ВКонтакте: http://vk.com/radislavgandapas_vk<br/>Instagram: http://instagram.com/radislavgandapas/<br/>LinkedIn: http://ru.linkedin.com/pub/radislav-gandapas/18/212/b98','2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg','2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg','2016-02/v50-20160202125504-preview-fa1dcc4fc69429f717dc4b9a08e3190d.jpg','[]','','youtube','Hmf9WMLgFLo','Hmf9WMLgFLo',2427,0),(38,1,'photo',50,'blog',2,1454417905,1454417905,0,'','','2016-02/m50-20160202125825-big-299bbb.jpg','2016-02/m50-20160202125825-medium-1490c4.jpg','2016-02/m50-20160202125825-preview-d64825206822c475dd10de8c51ac8bcb.jpg','{\"orig_width\":1300,\"orig_height\":1166,\"src_width\":1166,\"src_height\":1166,\"srcx\":67,\"srcy\":0,\"new_width\":300,\"new_height\":300}','{\"longitude\":\"\",\"latitude\":\"\",\"make\":\"\",\"model\":\"\",\"exposure\":\"\",\"aperture\":\"\",\"apertureValue\":\"\",\"iso\":\"\",\"focalLength35mm\":\"\",\"focalLength\":\"\",\"meteringMode\":\"\",\"flash\":\"\",\"exposureBiasValue\":\"\",\"sensingMethod\":\"\",\"gainControl\":\"\",\"exposureProgram\":\"\",\"maxApertureValue\":\"\",\"datetime\":\"\",\"orientation\":\"\"}','','','',0,585),(39,1,'zip',50,'blog',2,1454418049,1454418049,0,'Глеб Архангельский - Тайм-драйв. Как успевать жить и работать.zip','','2016-02/m50-20160202130049-big-6b2252.zip','2016-02/m50-20160202130049-big-6b2252.zip','default/zip_empty.png','{}','{}','','','',0,516),(40,1,'photo',1,'profile',1,1454420228,1454420228,0,'','','2016-02/m1-20160202133708-big-a6e729.jpg','2016-02/m1-20160202133708-medium-fd3a37.jpg','2016-02/m1-20160202133708-preview-2ee68f9a816e9b43361922641394e788.jpg','{\"orig_width\":1920,\"orig_height\":1080,\"src_width\":1080,\"src_height\":1080,\"srcx\":420,\"srcy\":0,\"new_width\":300,\"new_height\":300}','[]','','','',0,571),(41,1,'photo',50,'blog',2,1454494815,1454494901,0,'fsdfs','','2016-02/m50-20160203102015-big-fa71ea.jpg','2016-02/m50-20160203102015-medium-ad8927.jpg','2016-02/m50-20160203102015-preview-a2bff0d62675b6a6696e0042076e2bad.jpg','{\"orig_width\":768,\"orig_height\":1024,\"src_width\":768,\"src_height\":768,\"srcx\":0,\"srcy\":128,\"new_width\":300,\"new_height\":300}','[]','','','',0,146),(72,1,'photo',50,'album',2523,1478759545,1478759545,5,'','','2016-11/m50-20161110063225-big-dfabe7.jpg','2016-11/m50-20161110063225-big-dfabe7.jpg','2016-11/m50-20161110063225-preview-7b157fcd121009bd4cb69bf9ba47b6a7.jpg','{\"orig_width\":475,\"orig_height\":712,\"src_width\":475,\"src_height\":475,\"srcx\":0,\"srcy\":118,\"new_width\":300,\"new_height\":300}','[]','','','',0,99),(73,1,'photo',50,'album',2523,1478759546,1478759546,2,'','','2016-11/m50-20161110063226-big-d90262.jpg','2016-11/m50-20161110063226-medium-74a94a.jpg','2016-11/m50-20161110063226-preview-fb80c7270efb7de29b67f39bb38dc533.jpg','{\"orig_width\":864,\"orig_height\":1440,\"src_width\":864,\"src_height\":864,\"srcx\":0,\"srcy\":288,\"new_width\":300,\"new_height\":300}','[]','','','',0,218),(74,1,'photo',50,'album',2523,1478759546,1478759546,4,'','','2016-11/m50-20161110063226-big-b8a8bb.jpg','2016-11/m50-20161110063226-medium-3a4752.jpg','2016-11/m50-20161110063227-preview-1fd5e51132b6a34ba81b9e351f5ed152.jpg','{\"orig_width\":864,\"orig_height\":1440,\"src_width\":864,\"src_height\":864,\"srcx\":0,\"srcy\":288,\"new_width\":300,\"new_height\":300}','[]','','','',0,171),(75,1,'photo',50,'album',2523,1478759547,1478759547,7,'','','2016-11/m50-20161110063227-big-28d263.jpg','2016-11/m50-20161110063227-big-28d263.jpg','2016-11/m50-20161110063227-preview-e806be1b2b5d7275385daa1c334b9715.jpg','{\"orig_width\":640,\"orig_height\":640,\"src_width\":640,\"src_height\":640,\"srcx\":0,\"srcy\":0,\"new_width\":300,\"new_height\":300}','[]','','','',0,85),(76,1,'photo',50,'album',2523,1478759547,1478759547,3,'','','2016-11/m50-20161110063227-big-95185c.jpg','2016-11/m50-20161110063227-medium-15a6b5.jpg','2016-11/m50-20161110063227-preview-479fc5189522a84b757cffccc6ce0a6b.jpg','{\"orig_width\":960,\"orig_height\":1378,\"src_width\":960,\"src_height\":960,\"srcx\":0,\"srcy\":209,\"new_width\":300,\"new_height\":300}','[]','','','',0,187),(77,1,'photo',50,'album',2523,1478759548,1478759548,8,'','','2016-11/m50-20161110063228-big-445175.jpg','2016-11/m50-20161110063228-medium-5ba8d1.jpg','2016-11/m50-20161110063228-preview-9c3ffcbd8f5d2e568459a4ea93fbff5d.jpg','{\"orig_width\":575,\"orig_height\":1024,\"src_width\":575,\"src_height\":575,\"srcx\":0,\"srcy\":224,\"new_width\":300,\"new_height\":300}','[]','','','',0,149),(78,1,'photo',50,'album',2523,1478759548,1478759548,6,'','','2016-11/m50-20161110063228-big-4ac94c.jpg','2016-11/m50-20161110063228-big-4ac94c.jpg','2016-11/m50-20161110063228-preview-a6a51a8cd1beb597c70c391589ad28ca.jpg','{\"orig_width\":640,\"orig_height\":640,\"src_width\":640,\"src_height\":640,\"srcx\":0,\"srcy\":0,\"new_width\":300,\"new_height\":300}','[]','','','',0,87),(79,1,'photo',50,'album',2523,1478759549,1478759549,1,'','','2016-11/m50-20161110063229-big-8cf57a.jpg','2016-11/m50-20161110063229-medium-364f17.jpg','2016-11/m50-20161110063229-preview-165d06e51816e1129e2738927e1a1e75.jpg','{\"orig_width\":959,\"orig_height\":1440,\"src_width\":959,\"src_height\":959,\"srcx\":0,\"srcy\":240,\"new_width\":300,\"new_height\":300}','[]','','','',0,271);
/*!40000 ALTER TABLE `user_medias` ENABLE KEYS */;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Профиль',
  `type` enum('new_subscriber','new_message','new_room_message') NOT NULL,
  `object_id` int(11) unsigned NOT NULL COMMENT 'На кого подписка',
  `from_user_id` int(10) unsigned NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `isnew` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`type`,`object_id`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`),
  KEY `user_id` (`user_id`),
  KEY `isnew` (`isnew`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Уведомления пользователя';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(200) NOT NULL COMMENT 'логин',
  `passhash` varchar(255) NOT NULL DEFAULT '' COMMENT 'односторонняя функция с солью пароля',
  `sig` varchar(255) NOT NULL DEFAULT '' COMMENT 'сеансовый токен',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'для проверки email и др',
  `regtime` datetime NOT NULL COMMENT 'время регистрации',
  `sigtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'врямя установки сессии',
  `lastact` int(11) unsigned NOT NULL COMMENT 'юникстайм времени последнего посищения',
  `groups` varchar(30) NOT NULL DEFAULT '[1]' COMMENT 'Группы пользователя [1-NotBanned,2-NotDeleted,3-Activated,5-Moderator,6-Admin]',
  `lastip` varchar(127) NOT NULL DEFAULT '' COMMENT 'последний ip',
  `timezone` varchar(7) NOT NULL DEFAULT '',
  `has_picture` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `picture_id` int(10) unsigned NOT NULL DEFAULT '0',
  `picture_orig` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture.png',
  `picture` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture.png',
  `picture_preview` varchar(255) NOT NULL DEFAULT 'default/profile_nopicture_preview.png',
  `rating` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_conf` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `settings` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1-show_social_accounts',
  `vk_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'Id vk',
  `vk_access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'only offline token if given',
  `fb_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'id facebook',
  `gl_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'id google',
  `ig_id` varchar(100) NOT NULL DEFAULT '',
  `tw_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'id twitter',
  `od_id` varchar(100) NOT NULL DEFAULT '' COMMENT 'id odnoklassniki',
  `oauths` text NOT NULL,
  `skype_id` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'редактируемое имя',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL DEFAULT '' COMMENT 'first name',
  `lastname` varchar(255) NOT NULL DEFAULT '' COMMENT 'last name',
  `birthday` date NOT NULL DEFAULT '2000-01-01' COMMENT 'birthday',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'gender 1-make 2-female',
  `city` varchar(255) NOT NULL DEFAULT '',
  `longitude` int(11) NOT NULL DEFAULT '-500',
  `latitude` int(11) NOT NULL DEFAULT '-500',
  `about_me` text NOT NULL,
  `notifications_count` int(11) NOT NULL DEFAULT '0',
  `notifications_last` int(11) NOT NULL DEFAULT '0',
  `data_info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lastact` (`lastact`),
  KEY `gender` (`gender`),
  KEY `rating` (`rating`),
  KEY `alias` (`alias`(191)),
  KEY `login` (`login`(191))
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COMMENT='Пользователи';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'mades1989','ab9ec6c2a37748e2fda8ec0eff788924','NULL','','2013-07-12 21:40:17',1454647261,1454647451,'[1,2,3]','80.249.93.6','-180',1,40,'2016-02/m1-20160202133708-big-a6e729.jpg','2016-02/m1-20160202133708-medium-fd3a37.jpg','2016-02/m1-20160202133708-preview-2ee68f9a816e9b43361922641394e788.jpg',0,'mades1989@gmail.com',1,0,'','','','','','','','[]','','Administrator','','','','1970-01-01',0,'',-500,-500,'',0,0,''),(7,'madeS','3db1b88589fa1a69926927d9b0d59f87','619d20455ac1f7c8557e1be2176af077','','2013-03-16 20:02:05',1400133102,1400134227,'[1,2,3]','84.201.231.69','-180',0,0,'default/profile_nopicture.png','default/profile_nopicture.png','default/profile_nopicture_preview.png',0,'bogxp@mail.ru',1,0,'4518080','','','','','164211031','','{\"vk_id\":{\"sync\":{\"field\":\"vk_id\",\"value\":4518080},\"id\":4518080,\"name\":\"\\u0410\\u043d\\u0434\\u0440\\u0435\\u0439 \\u0411\\u043e\\u0433\\u0430\\u0440\\u0435\\u0432\\u0438\\u0447\",\"firstname\":\"\\u0410\\u043d\\u0434\\u0440\\u0435\\u0439\",\"lastname\":\"\\u0411\\u043e\\u0433\\u0430\\u0440\\u0435\\u0432\\u0438\\u0447\",\"social\":\"vk\",\"gender\":1,\"profilePhoto\":\"http:\\/\\/cs317928.vk.me\\/v317928080\\/62e4\\/xraQXgjtgUw.jpg\",\"profileBirthday\":\"1989-05-26\"},\"tw_id\":{\"sync\":{\"field\":\"tw_id\",\"value\":164211031},\"id\":164211031,\"name\":\"Andrei\",\"social\":\"twitter\"}}','','Андрей','','Андрей','Богаревич','1989-05-26',1,'Минск, Беларусь',-500,-500,'Бурильщик',1,1453224062,''),(50,'&lt;scripterOK/&gt;','abc74db4a14d3861bcdcb1e1d3ffeb83','','password_a8a382c13bb119b50f90865ce691f1bd','2013-08-24 20:54:23',1542003797,1542003798,'[1,2,3,5,6]','192.168.157.1','-180',0,0,'default/profile_nopicture.png','default/profile_nopicture.png','default/profile_nopicture_preview.png',0,'ourtravelru@gmail.com',1,0,'','','','','','','','[]','','scripterOK','','','','1920-01-01',1,'',-500,-500,'О себе тут',0,0,'{\"person_description\":\"хелло\"}'),(51,'ourtravelru2@gmail.com','db7ec4c7e6fb4b5ff6bf125f1f3ee8f1','','','2018-10-09 09:32:48',0,1539077568,'[1]','','',0,0,'default/profile_nopicture.png','default/profile_nopicture.png','default/profile_nopicture_preview.png',0,'ourtravelru2@gmail.com',0,0,'','','','','','','','{}','','','','','','2000-01-01',0,'',-500,-500,'',0,0,'{}'),(83,'testUser','9bb8785cb51190e1fe6d7db4dcd833b3','','','2019-11-16 07:28:02',1575988844,1575989995,'[1,2,3]','192.168.157.1','-180',0,0,'','default/profile_nopicture.png','default/profile_nopicture_preview.png',0,'testUser@example.com',1,0,'','','','','','','','{}','','','','','','1926-05-05',1,'Минск',-500,-500,'fwefwefwe',0,0,'{\"info\":{\"requiredFieldKey\":\"fwefwefwe2\"}}');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-12 13:59:56
