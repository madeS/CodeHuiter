<?php

/** @var \App\Controller\DefaultController $those */
$those = null;
/** @var \CodeHuiter\Service\ByDefault\PhpRenderer $renderer */
$renderer = null;

/** @var string $bodyAjax */
$bodyAjax = '';
/** @var string $language */
$language = '';

/** @var string $template */
$template = '';

/** @var array $filters */
$filters = [];
/** @var array $pages */
$pages = [
    'per_page' => 20,
    'page' => 1,
    'total' => 200
];

/** @var string $headAfterTpl */
$headAfterTpl = '';
/** @var string|array $contentTpl */
$contentTpl = '';
/** @var string $bodyAfterTpl */
$bodyAfterTpl = '';
/** @var string $headerTpl */
$headerTpl = '';
/** @var string $footerTpl */
$footerTpl = '';
/** @var bool $customTemplate */
$customTemplate = ''; // Means is no need header and footers

/** @var string $headTitle */
$headTitle = '';
/** @var string $headDescription */
$headDescription = '';
/** @var string $headKeywords */
$headKeywords = '';
/** @var string $headImage */
$headImage = '';


/** @var \CodeHuiter\Pattern\Module\Auth\Model\UserInterface|null $userInfo */
$userInfo = false;


/** @var string $uri */
$uri = '';


