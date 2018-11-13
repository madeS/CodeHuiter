<?php if (false):?>
<style>
<?php endif;?>

<?php

class CustomSiteColors extends \CodeHuiter\Services\SiteColors
{
    public $font = 'Arial, Helvetica, sans-serif, Georgia';
    public $fontSize = '14px';

    public $textWhite = '#fff';
    public $text = '#414141';
    public $textRed = '#EA3F2B';
    public $links = '#0773c6';
    public $linksHover = '#ea3f2b';
    public $customFirst = '#0773c6';
    public $customSecond = '#e2e0d6';
    public $customGray = '#f1f1f1'; // site bg
    public $customAlerts = '#f98e3c'; // alerts orange

    public $headerBg = '#ffffff';
    public $headerBgAlpha = '0.95';

    public $bg = '~customGray';
    public $isBgNoised = false;
    public $hasLeftContainer = false;
    public $isBgGradi = false;

    public $btnDefaultBg = '#fcfcfc';
    public $btnDefaultBgT = '#F6F6F6';
    public $btnDefaultBgB = '#DADADA';
    public $btnDefaultBgHover = '#f8f8f8';
    public $btnDefaultBgHoverT = '#E6E6E6';
    public $btnDefaultBgHoverB = '#CACACA';
    public $btnDefaultText = '#414141';

    public $btnGreenBg = '#359A13';
    public $btnGreenBgT = '#52DF23';
    public $btnGreenBgB = '#359A13';
    public $btnGreenBgHover = '#258A03';
    public $btnGreenBgHoverT = '#42CF13';
    public $btnGreenBgHoverB = '#258A03';
    public $btnGreenText = '~textWhite';

    public $btnBlueBg = '#2793e6';
    public $btnBlueBgT = '#2793e6';
    public $btnBlueBgB = '#0773c6';
    public $btnBlueBgHover = '#0773c6';
    public $btnBlueBgHoverT = '#1783d6';
    public $btnBlueBgHoverB = '#0053a6';
    public $btnBlueText = '~textWhite';

    public $btnRedBg = '#CF3E13';
    public $btnRedBgT = '#CF6234';
    public $btnRedBgB = '#CF3E13';
    public $btnRedBgHover = '#BF2E03';
    public $btnRedBgHoverT = '#BF5224';
    public $btnRedBgHoverB = '#BF2E03';
    public $btnRedText = '~textWhite';

    public $iblockBg = '#fff';
    public $iblockControlsBg = '#efefef';
    public $iblockHeaderBg = '~customFirst';
    public $iblockHeaderText = '~textWhite';
    public $iblockHeaderFlagBg = '#fff';

    public $breadcrumbsBg = '~textWhite';
    public $breadcrumbsText = '~customFirst';
    public $breadcrumbsHoverBg = '~customFirst';
    public $breadcrumbsHoverText = '~textWhite';

    public $pagesBg = '~textWhite';
    public $pagesText = '~customFirst';
    public $pagesHoverBg = '~customFirst';
    public $pagesHoverText = '~textWhite';
    public $pagesActiveBg = '~customFirst';
    public $pagesActiveText = '~textWhite';

    public $childLinksPxPaddingCont = 6;
    public $childLinksPxPaddingItem = 6;
    public $childLinksPxPaddingItemLeft = 6;
    public $childLinksPxDefaultBorder = 0;
    public $childLinksPxActiveBorder = 4;
    public $childLinksDefaultBorder = '#d7d7d7';
    public $childLinksActiveBorder = '~linksHover';
    public $childLinksDefaultBg = 'transparent';
    public $childLinksHoverBg = 'transparent';
    public $childLinksActiveBg = '~linksHover';
    public $childLinksDefaultText = '~links';
    public $childLinksHoverText = '~linksHover';
    public $childLinksActiveText = '~textWhite';
    public $childLinksIsTextDecor = true;

    public $userOnline = '#0ED50E';

    public $topMenuDefaultBg = 'transparent';
    public $topMenuDefaultText = '#858587';
    public $topMenuHoverBg = '#858587';
    public $topMenuHoverText = '~textWhite';
    public $topMenuActiveBg = '#858587';
    public $topMenuActiveText = '~textWhite';
    public $topMenuSubBg = '#e6e6e6';
    public $topMenuSubText = '~topMenuDefaultText';

    public $progressBarTrack = '~links';

    public $popupCloseBg = '#0773C6';
    public $popupCloseText = '~textWhite';
}

$colors = new CustomSiteColors();

?>

/* Reset */
<?php if (true):?>
* { margin:0; padding:0; border:0; font-size:100%; vertical-align:baseline; background:transparent; }
article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section { display:block; }
blockquote, q { quotes:none; }
blockquote:before, blockquote:after, q:before, q:after {content: ''; content:none;}
table {border-collapse:collapse; border-spacing:0;}
:focus {outline:none;}
:active {outline:none;}
input, select {vertical-align:middle; border-radius: 0;}
input[type="file"] { border: 2px solid #999; padding: 1px; }
pre {color: #333; background-color: #eee; border-radius: 5px; padding: 10px; }
/* END reset */
<?php endif;?>

/* Basic */
<?php if(true):?>
body {
	font-family:<?=$colors->font?>;
	font-size:<?=$colors->fontSize?>;
	line-height:18px;
	font-style:normal;
	color:<?=$colors->text?>;
	overflow-y: scroll;
	background-color: #fff;
}
a{ color:<?=$colors->links?>; text-decoration:none; }
a:hover{ color:<?=$colors->linksHover?>; text-decoration:underline; }
h1, h2, h3, h4, h5, h6 { margin-bottom:5px; margin-top:0px; font-weight:bold; }
h1 { margin-bottom:0px; font-size:20px; line-height:26px; }
h2 { font-size:22px; line-height:25px; }
h3 { font-size:18px; line-height:20px; }
h4 { font-size:16px; line-height:20px; }
h5, h6 { font-size:14px; line-height:18px; }
p { margin-bottom:5px; }
b, strong { font-size:100%; font-weight:bold; }
small { font-size:87%; }
ol, ul { padding:5px 0 0 13px; margin:0 0 18px 13px; }
ol li, ul li { margin:0 0 8px 0; }
blockquote { margin:0 0 20px 25px; font-style:italic; color:#888; line-height:19px; }
input, select, textarea { background-color: #fff;}
button, input, select, textarea { font-family: <?=$colors->font?>; font-size:<?=$colors->fontSize?>; }
hr {  display:block;  height:1px; border:0; border-top:1px solid #e3e3e3; margin:1.6em 0 1.7em 0; padding:0; }
/* END basic */
<?php endif;?>

/* Body*/
<?php if (true):?>
body {
<?php if($colors->isBgNoised):?>
	/* if has noised +2kb */
	background-image: url(data:image/png;base64,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);
<?php endif;?>
	background-color:<?=$colors->bg?>;
}
/* END Body */
<?php endif;?>

/* Social icons*/
<?php if (true):?>
[class^="sik-"], [class*=" sik-"]{ width: 32px; height: 32px; display: inline-block; border-radius: 3px; opacity:0.90; color:#fff; text-align: center; line-height: 32px; font-size:14px;}
[class^="sik-"]:hover, [class*=" sik-"]:hover{ opacity: 1; color:#fff;}
.sik-vk{ background: #49739f;}
.sik-ig{ background: #966842;}
.sik-ok{ background: #eb722e;}
.sik-fb{ background: #3b5998;}
.sik-tw{ background: #00aced;}
.sik-gl{ background: #dd4c39;}
[class^="sik-"].big, [class*=" sik-"].big{ width: 45px; height: 45px; line-height: 45px;}
[class^="sik-"].small, [class*=" sik-"].small{ width: 28px; height: 28px; line-height: 28px}
[class^="sik-"].translucent, [class*=" sik-"].translucent{ opacity: 0.3;}
[class^="sik-"].translucent:hover, [class*=" sik-"].translucent:hover{ opacity: 1;}
.sik-btn {width: auto; padding: 0 10px 0 5px; line-height: 32px; color: #fff;  font-weight: bold; margin-bottom: 5px;}
.sik-btn:hover {color: #fff;}
/* END social icons */
<?php endif;?>

/* Text fader */
<?php if (true):?>
span.tf.lightgray{
   background-image: -moz-linear-gradient(left, <?=$colors->colorConvert($colors->customGray,0)?>, <?=$colors->colorConvert($colors->customGray,1)?>);
   background-image: -webkit-linear-gradient(left, <?=$colors->colorConvert($colors->customGray,0)?>, <?=$colors->colorConvert($colors->customGray,1)?>);
   background-image: -o-linear-gradient(left, <?=$colors->colorConvert($colors->customGray,0)?>, <?=$colors->colorConvert($colors->customGray,1)?>);
   background-image: -ms-linear-gradient(left, <?=$colors->colorConvert($colors->customGray,0)?>, <?=$colors->colorConvert($colors->customGray,1)?>);
   background-image: linear-gradient(left, <?=$colors->colorConvert($colors->customGray,0)?>, <?=$colors->colorConvert($colors->customGray,1)?>);
}
span.tf.fcolor{
   background-image: -moz-linear-gradient(left, <?=$colors->colorConvert($colors->customFirst,0)?>, <?=$colors->colorConvert($colors->customFirst,1)?>);
   background-image: -webkit-linear-gradient(left, <?=$colors->colorConvert($colors->customFirst,0)?>, <?=$colors->colorConvert($colors->customFirst,1)?>);
   background-image: -o-linear-gradient(left, <?=$colors->colorConvert($colors->customFirst,0)?>, <?=$colors->colorConvert($colors->customFirst,1)?>);
   background-image: -ms-linear-gradient(left, <?=$colors->colorConvert($colors->customFirst,0)?>, <?=$colors->colorConvert($colors->customFirst,1)?>);
   background-image: linear-gradient(left, <?=$colors->colorConvert($colors->customFirst,0)?>, <?=$colors->colorConvert($colors->customFirst,1)?>);
}
/* END text fader*/
<?php endif;?>

/* Site Basics Helpers */
<?php if(true):?>
.likea{cursor: pointer;}
a,.likea{color: <?=$colors->links?>; }
a:hover,.likea:hover{color:<?=$colors->linksHover?>;text-decoration:underline;}
.mjsa_hints_container .mjsa_hint {opacity: 0.9;}
.noa { color: <?=$colors->text?>; text-decoration: none;  }
.noa:hover { color: <?=$colors->text?>; text-decoration: none; }
.clearline {height: 0px; clear: both;}
.transition { transition: 0.5s; }

.textovered{ white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
.imgcentred{ position: absolute; top:0; right: 0; bottom: 0; left: 0; margin: auto; }
.centered {text-align: center} 
.righted {text-align: right}
.lefted {text-align: left}
.hidden {display:none;}
div.divinline {display:inline;}
.inliner { display: inline-block; float: none;}
.relative {position:relative;}

.margintop {margin-top:10px;}
/* END Site Basics Helpers */
<?php endif;?>

/* Site Btns */
<?php if (true):?>
.btn {  
	min-height: 33px; font-weight: bold; line-height: 33px;  display:inline-block; padding: 0 8px; 
	box-sizing: border-box;
	background-color: <?=$colors->btnDefaultBg?>; color: <?=$colors->btnDefaultText?>;
	background-image: -webkit-linear-gradient(top,<?=$colors->btnDefaultBgT?> 0,<?=$colors->btnDefaultBgB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnDefaultBgT?> 0px, <?=$colors->btnDefaultBgB?> 100%);
	cursor: pointer;
}
.btn:focus { outline: 1px solid <?=$colors->links?>; }
.btn:active { outline: 1px solid <?=$colors->linksHover?>;  }
.btn:hover { text-decoration: none;  
	background-color: <?=$colors->btnDefaultBgHover?>; color: <?=$colors->text?>;
	background-image: -webkit-linear-gradient(top,<?=$colors->btnDefaultBgHoverT?> 0,<?=$colors->btnDefaultBgHoverB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnDefaultBgHoverT?> 0px, <?=$colors->btnDefaultBgHoverB?> 100%);
}
#body_cont .btn.disable,
#body_cont .nobtn.disable,
.scoll_popup_container .btn.disable {
	-webkit-animation: btndisable 0.5s ease-in-out 0s infinite normal;
	animation: btndisable 0.5s ease-in-out 0s infinite normal;
}
.btn span.ficon { width: 30px; }

@-webkit-keyframes btndisable {
  0% { box-shadow: 0 2px 1px <?=$colors->linksHover?>;  }
  25% { box-shadow: 4px 0px 1px <?=$colors->linksHover?>; }
  50% { box-shadow: 0px -2px 1px <?=$colors->linksHover?>; }
  75% { box-shadow: -4px 0px 1px <?=$colors->linksHover?>; }
  100% { box-shadow: 0 2px 1px <?=$colors->linksHover?>;  }
}
@keyframes btndisable {
  0% { box-shadow: 0 2px 1px <?=$colors->linksHover?>;  }
  25% { box-shadow: 4px 0px 1px <?=$colors->linksHover?>; }
  50% { box-shadow: 0px -2px 1px <?=$colors->linksHover?>; }
  75% { box-shadow: -4px 0px 1px <?=$colors->linksHover?>; }
  100% { box-shadow: 0 2px 1px <?=$colors->linksHover?>;  }
}

.btn.green {
	background-color: <?=$colors->btnGreenBg?>; color: <?=$colors->btnGreenText?>;
	background-image: -webkit-linear-gradient(top,<?=$colors->btnGreenBgT?> 0px, <?=$colors->btnGreenBgB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnGreenBgT?> 0px, <?=$colors->btnGreenBgB?> 100%);
}
.btn.green:hover {
	background-color: <?=$colors->btnGreenBgHover?>;
	background-image: -webkit-linear-gradient(top, <?=$colors->btnGreenBgHoverT?> 0px, <?=$colors->btnGreenBgHoverB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnGreenBgHoverT?> 0px, <?=$colors->btnGreenBgHoverB?> 100%);
}
.btn.blue {
	background-color: <?=$colors->btnBlueBg?>; color: <?=$colors->btnBlueText?>;
	background-image: -webkit-linear-gradient(top,<?=$colors->btnBlueBgT?> 0px, <?=$colors->btnBlueBgB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnBlueBgT?> 0px, <?=$colors->btnBlueBgB?> 100%);
}
.btn.blue:hover{
	background-color: <?=$colors->btnBlueBgHover?>;
	background-image: -webkit-linear-gradient(top, <?=$colors->btnBlueBgHoverT?> 0px, <?=$colors->btnBlueBgHoverB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnBlueBgHoverT?> 0px, <?=$colors->btnBlueBgHoverB?> 100%);
}
.btn.red {
	background-color: <?=$colors->btnRedBg?>; color: <?=$colors->btnRedText?>;
	background-image: -webkit-linear-gradient(top,<?=$colors->btnRedBgT?> 0px, <?=$colors->btnRedBgB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnRedBgT?> 0px, <?=$colors->btnRedBgB?> 100%);
}
.btn.red:hover {
	background-color: <?=$colors->btnRedBgHover?>;
	background-image: -webkit-linear-gradient(top, <?=$colors->btnRedBgHoverT?> 0px, <?=$colors->btnRedBgHoverB?> 100%);
	background-image: linear-gradient(to bottom, <?=$colors->btnRedBgHoverT?> 0px, <?=$colors->btnRedBgHoverB?> 100%);
}
.btn.maxiusable { display: inline-block; border-radius: 7px; padding: 2px 23px; in-height: 40px; line-height: 36px; box-shadow: 0px 3px 9px -2px #000; }
.btn.maxiusable:hover{  }
.btn.maxiusable:active, .btn.maxiusable:focus { }

/* END Site Btns */
<?php endif;?>

/* Styled Form */
<?php if(true):?>
.styled_form {}
.styled_form h2{ text-align: center;}
.styled_form .row{ padding: 0 3px 6px; text-align: left;}
.styled_form span.label{ display: inline-block; width: 37%; min-height: 30px; line-height: 30px; text-align: right; padding: 0 8px 0 0; vertical-align: top; box-sizing: border-box; }
.styled_form span.divinline{ line-height: 30px; display:inline-block; }
.styled_form input.in, .styled_form select.in, .styled_form textarea.in { border: 1px solid #e3e3e3; height:28px; line-height: 28px; width: 60%; box-sizing: border-box;padding: 0 0 0 8px; }
.styled_form .likeinput{ width: 60%; box-sizing: border-box; padding: 0 0 0 0px;}
.styled_form .selecttwo{ width: 100%; }
.styled_form input.in[type="checkbox"] {height: 26px; padding: 0px; width: 26px}
.styled_form .in.ronly{ background-color: #eee; }
.styled_form select.in.birthday { width: 60px;}
.styled_form select.in.birthday.year { width:80px; }
.styled_form textarea.in {min-height: 90px; max-width: 60%; min-width: 60%; line-height: 18px; }
.styled_form .btn{display: inline-block; }
.styled_form .in_error{color: <?=$colors->textRed?>; margin-bottom: 10px; margin-left: 37%;}
.styled_form .red { color: <?=$colors->textRed?>; }
.styled_form .in_error.stepped {margin-left: 0%;}
.styled_form .row.stepped span.label, .styled_form .row.stepped input.in, .styled_form .row.stepped select.in, .styled_form .row.stepped textarea.in{ display:block; width: 100%; text-align: left; max-width: 100%; min-width: 100%;}

/* form customed */
.auth_form .whatpass{ margin-left: 20px; } 
.regauth_form_container {text-align:center;}
.regauth_form_container h3 { margin-bottom:10px; }
.register_form, .auth_form{ width: 390px; display: inline-block; vertical-align: top; margin-top: 10px;}
.popup_scroll .register_form, .popup_scroll .auth_form { width:100%; }
.popup_feedback { max-width: 500px; }

/* END Styled Form */
<?php endif;?>

/* BodyAjax Loader */
<?php if(true):?>
.mjsa_ajax_shadow .mjsa_loader{ position: absolute; top: 20px;/* 36%;*/ left: 50%; margin-left: -50px; margin-top: 0px;}
.cssload-loader {position: relative;left: calc(50% - 50px);width: 100px;height: 100px;border-radius: 50%;perspective: 780px;}
.cssload-inner {position: absolute;width: 100%;height: 100%;box-sizing: border-box;border-radius: 50%;	}
.cssload-inner.cssload-one {left: 0%;top: 0%;animation: cssload-rotate-one 1.15s linear infinite;border-bottom: 5px solid rgb(0,0,0);}
.cssload-inner.cssload-two {right: 0%;top: 0%;animation: cssload-rotate-two 1.15s linear infinite;border-right: 5px solid rgb(0,0,0);}
.cssload-inner.cssload-three {right: 0%;bottom: 0%;animation: cssload-rotate-three 1.15s linear infinite;border-top: 5px solid rgb(0,0,0);}
@keyframes cssload-rotate-one {
	0% {transform: rotateX(35deg) rotateY(-45deg) rotateZ(0deg);}
	100% {transform: rotateX(35deg) rotateY(-45deg) rotateZ(360deg);}
}
@keyframes cssload-rotate-two {
	0% {transform: rotateX(50deg) rotateY(10deg) rotateZ(0deg);}
	100% {transform: rotateX(50deg) rotateY(10deg) rotateZ(360deg);}
}
@keyframes cssload-rotate-three {
	0% {transform: rotateX(35deg) rotateY(55deg) rotateZ(0deg);}
	100% {transform: rotateX(35deg) rotateY(55deg) rotateZ(360deg);}
}
/* BodyAjax Loader */
<?php endif;?>

/* iBlock */
<?php if(true):?>
.iblock { display: block; box-shadow: 0px 2px 5px 0px #ccc; background-color: <?=$colors->iblockBg?>; }
.iblock.noshadow { box-shadow: none; }
.iblock .bheader{ display:block;  background-color: <?=$colors->iblockHeaderBg?>; color: <?=$colors->iblockHeaderText?>; font-weight: bold; padding: 0 8px; line-height: 35px; height: 35px;  }
.iblock .bheader.alert { display:block; background-color: <?=$colors->customAlerts?>; }
.iblock .bheader .flag{ float:left; height: 29px; box-sizing: border-box; padding: 3px; background-color: <?=$colors->iblockHeaderFlagBg?>; margin: 3px 6px 0 0; }
.iblock .bheader .flag img{ max-height: 100%; }
.iblock .bheader h1{ line-height: 35px;  }
.iblock .bheader a{ color:<?=$colors->iblockHeaderText?>; text-decoration: none; }
.iblock .padded{ padding: 10px; }
.iblock .padded_vertical{ padding: 10px 0; }
.iblock .control_btns { padding: 10px 8px 10px; background-color: <?=$colors->iblockControlsBg?>; }
.iblock .control_btns p{ margin-bottom: 0; } 
.iblock .control_btns .right_btns{ float: right; text-align: right; }
.iblock .control_btns .left_btns{ float: left; }

.left_container .iblock {margin-bottom: 20px; }
.right_container .iblock {margin-bottom: 20px; }
/* END iBlock */
<?php endif;?>

/* Breadcrumbs */
<?php if(true):?>
.breadcrumbs { position: relative; margin: 0 0 15px 0; min-height: 28px; padding: 6px 0; line-height: 28px; }
.breadcrumbs .crumb{ color:<?=$colors->breadcrumbsText?>; display: inline-block; vertical-align: top; position: relative; background-color: <?=$colors->breadcrumbsBg?>; height: 28px; line-height: 28px; margin-right: 18px; padding: 0px 5px;  box-shadow: 0px 1px 5px 0px #a3a3a3; }
.breadcrumbs a.crumb:hover { color:<?=$colors->breadcrumbsHoverText?>; background-color:<?=$colors->breadcrumbsHoverBg?>; text-decoration: none; }
.breadcrumbs a.crumb:hover:before { border-top-color: <?=$colors->breadcrumbsHoverBg?>; border-bottom-color: <?=$colors->breadcrumbsHoverBg?>; }
.breadcrumbs a.crumb:hover:after { border-left-color: <?=$colors->breadcrumbsHoverBg?>; }
.breadcrumbs .crumb [class^="ficon-"] {font-size: 20px; vertical-align: top; line-height: 28px;}
.breadcrumbs .crumb:before{ content: '';  position: absolute; top: 0; left: -14px; display: block; width: 0; height: 0;  border-top: 14px solid <?=$colors->breadcrumbsBg?>; border-bottom: 14px solid <?=$colors->breadcrumbsBg?>; border-left: 14px solid transparent; border-right: none; }
.breadcrumbs .crumb.first:before { border-left: none;}
.breadcrumbs .crumb:after{ content: '';  position: absolute; top: 0; right: -14px; display: block; width: 0; height: 0; border-top: 14px solid transparent; border-bottom: 14px solid transparent; border-left: 14px solid <?=$colors->breadcrumbsBg?>; border-right: none;}
.after_breadcrumbs_line {clear:both; display: block;}
/* END Breadcrumbs */
<?php endif;?>

/* Pages */
<?php if(true):?>
.pages { display:block; clear: both;}
.pages a{ display: inline-block; height: 25px; line-height: 25px; background-color: <?=$colors->pagesBg?>; color: <?=$colors->pagesText?>; padding: 0px 12px; font-weight: bold; }
.pages a:hover,.pages a:active{ text-decoration: none; background-color: <?=$colors->pagesHoverBg?>; color: <?=$colors->pagesHoverText?>;}
.pages a.selected{ background-color: <?=$colors->pagesActiveBg?>; color: <?=$colors->pagesActiveText?>; }
/* END Pages */
<?php endif;?>

/* Containers */
<?php if(true):?>
.page_container { position:  relative;}
.page_container .center_container { margin: 0 225px 0 <?=($colors->hasLeftContainer)?'225px':'0'?>;} 
.page_container .left_container { position: absolute; top: 0; left: 0; width: 205px }
.page_container .right_container { position: absolute; top: 0; right: 0; width: 205px; }
/* END Containers */
<?php endif;?>

/* Child_links */
<?php if(true):?>
.child_links {  }
.child_links.items, .child_links .items { padding: <?=$colors->childLinksPxPaddingCont?>px 0; }
.child_links .item {  display:block;  padding: <?=$colors->childLinksPxPaddingItem?>px <?=$colors->childLinksPxPaddingItem?>px <?=$colors->childLinksPxPaddingItem?>px <?=$colors->childLinksPxPaddingItemLeft+($colors->childLinksPxActiveBorder-$colors->childLinksPxDefaultBorder)?>px; color: <?=$colors->childLinksDefaultText?>; border-left: <?=$colors->childLinksPxDefaultBorder?>px solid <?=$colors->childLinksDefaultBorder?>; text-align: left; background-color: <?=$colors->childLinksDefaultBg?>;}
.child_links .item:hover,.child_links .item.active { color: <?=$colors->childLinksHoverText?>; border-left: <?=$colors->childLinksPxActiveBorder?>px solid <?=$colors->childLinksActiveBorder?>; padding-left: <?=$colors->childLinksPxPaddingItemLeft?>px; background-color: <?=$colors->childLinksHoverBg?>; <?=(!$colors->childLinksIsTextDecor)?'text-decoration: none;':''?> }
.child_links .item.active { color: <?=$colors->childLinksActiveText?>; background-color: <?=$colors->childLinksActiveBg?>; }
/* END Child_links */
<?php endif;?>

/* Page_searcher */
<?php if(true):?>
.page_searcher_cont { margin-bottom: 40px; }
.page_searcher { padding-right: 100px; position: relative; margin-bottom: 10px; height: 40px; }
.page_searcher .page_searcher_input { box-sizing: border-box; padding: 8px 10px; width: 100%; height: 40px; line-height: 24px; font-size: 19px; }
.page_searcher .page_searcher_submit { position: absolute; right: 0; top: 0; height: 100%; width: 100px; line-height: 40px; text-align: center;}
/* END Page_searcher */
<?php endif;?>

/* imglinkblock */
<?php if(true):?>
.imglinkblock-item { position: relative; display: block; margin-bottom: 8px; }
.imglinkblock-item .img_cont { width: 205px; height: 120px; overflow: hidden; position: relative; display: block; }
.imglinkblock-item .img_inside { display:block; width: 235px; height: 235px; position: relative; margin-top: -55px; margin-left: -15px; }
.imglinkblock-item .img_inside img{ width: 205px; height: auto; }
.imglinkblock-item .layer{ position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.2); }
.imglinkblock-item:hover .layer{ background-color: rgba(0,0,0,0.0); }
.imglinkblock-item:hover .img_inside img{ width: 100%; height: auto; }
.imglinkblock-item .title_cont { position: absolute; bottom: 0; left: 0; width: 100%; height: auto; padding: 25px 5px 5px; box-sizing: border-box; 
	background-image: -webkit-linear-gradient(top, rgba(0,0,0,0) 0px, rgba(0,0,0,0.7) 80%);
	background-image: linear-gradient(to bottom, rgba(0,0,0,0) 0px, rgba(0,0,0,0.7) 80%);
}
.imglinkblock-item .title_inside { color:#fff; }
.imglinkblock-item .videoplay { width: 50px; height: 50px; display: inline-block; background-color: rgba(0,0,0,0.6); text-align: center; border-radius: 100%; }
.imglinkblock-item .videoplay > span { font-size: 25px; line-height: 50px; color: #ccc; }
.imglinkblock-item .btns{ position: absolute; top: 0; right: 0; width:180px; text-align: right; }
.imglinkblock-item .btns > span{ background-color: rgba(0,0,0,0.2); color: #fff; padding: 3px; margin-bottom: 8px; }
.imglinkblock-item .btns > span.active{ background-color: rgba(0,127,0,1); }
.imglinkblock-item .btns > span:hover{ background-color: rgba(0,0,127,0.6); }

.center_media_previews { margin: 8px 0; text-align: center; }
.center_media_previews .imglinkblock-item { margin: 0 4px 6px; display: inline-block; }
.media_preview_simple {display: inline-block;}
/* END imglinkblock */
<?php endif;?>

/* BackgroundLayer */
<?php if(true):?>
.backgroundlayer { z-index: 0; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
background-size: cover; background-repeat: no-repeat; background-position: center center;
/* -webkit-filter: blur(3px); */
}
.backgroundlayer.braled {
	z-index: 0; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
	background-size: cover; background-repeat: no-repeat; background-position: center center;
}
.backgroundlayercontent { z-index: 1; /* min-height: 100vh; */ position: relative; }
<?php if($colors->isBgGradi):?>
.backgroundlayer.braled {
	z-index: 0; position: absolute; width: 100%; height: 450px; top: 0; left: 0;
	background-size: cover; background-repeat: no-repeat; background-position: center center;
}
.backgroundlayer.braled .gradi {
	position: absolute;  top: auto; bottom: 0px; left: 0; width: 100%; height: 150px; 
	background-image: -moz-linear-gradient(top, <?=$colors->colorConvert($colors->bg,0)?>, <?=$colors->colorConvert($colors->bg,1)?>);
	background-image: -webkit-linear-gradient(top, <?=$colors->colorConvert($colors->bg,0)?>, <?=$colors->colorConvert($colors->bg,1)?>);
	background-image: -o-linear-gradient(top, <?=$colors->colorConvert($colors->bg,0)?>, <?=$colors->colorConvert($colors->bg,1)?>);
	background-image: -ms-linear-gradient(top, <?=$colors->colorConvert($colors->bg,0)?>, <?=$colors->colorConvert($colors->bg,1)?>);
	background-image: linear-gradient(top, <?=$colors->colorConvert($colors->bg,0)?>, <?=$colors->colorConvert($colors->bg,1)?>);
}
<?php endif;?>
/* END BackgroundLayer */
<?php endif;?>

/* UserList */
<?php if(true):?>
.user-item{ display: inline-block; vertical-align: top; margin: 0 10px 15px 0; width: 112px; height: 132px; position: relative; text-align: left; }
.user-item .preview{ width: 100%; height: auto; display: block;}
.user-item .online{ position: absolute; top: 2px; left: 2px; width: 10px; height: 10px; display: block; background-color: <?=$colors->userOnline?>; border: 2px solid #fff; border-radius: 100%; }
.user-item .name{ display: block; width: 100%; height: 20px; max-height: 20px; line-height: 20px;}
.user_list_mini .user-item { width: 53px; height: 69px; margin: 0 6px 10px 0; }
.user_list_mini .user-item .name{ height: 16px; line-height: 16px; font-size: 14px; }

.online span { display: inline-block; background-color: <?=$colors->userOnline?>; height: 10px; width: 10px; border-radius: 5px; margin: 0 12px 0 6px; vertical-align: baseline;}
/* END UserList */
<?php endif;?>

/* TopMenu */
<?php if(true):?>
.topmenu { height: 50px; text-transform: uppercase; text-align: center; }
.topmenu .item {position: relative; display: inline-block; vertical-align: top; height: 50px; line-height: 50px; padding: 0 15px; background-color: <?=$colors->topMenuDefaultBg?>; color: <?=$colors->topMenuDefaultText?>; font-size: 13px; text-decoration: none; margin: 0 10px 0 0; cursor: pointer; }
.topmenu .item:hover { background-color: <?=$colors->topMenuHoverBg?>; color: <?=$colors->topMenuHoverText?>;  }
.topmenu .item.active { background-color: <?=$colors->topMenuActiveBg?>; color: <?=$colors->topMenuActiveText?>; }
.topmenu .subitems { display: none; width: 150px; position: absolute; top: 50px; left: 0; text-align: left; background-color: <?=$colors->topMenuSubBg?>;  color: <?=$colors->topMenuSubText?>}
.topmenu .item:hover .subitems {display: block; }
.topmenu .subitems .item{ display: block; margin: 0 0; padding: 0px 0 0px 10px; height: 30px; line-height: 30px;}
/* END TopMenu */
<?php endif;?>

/* Sortable */
<?php if(true):?>
.sortable-list {position:  relative;}
.sortable-list .sortable-item {position:  relative;}
.sortable-list .sortable-item.right .handle{ position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; cursor: move; text-align: center; line-height: 24px; background-color: #fff;}
/* END Sortable */
<?php endif;?>

/* Default site Elements */
<?php if(true):?>
.scoll_popup_container .close_popup_scroll{ top: -14px; right: -14px; width: 28px; height: 28px; line-height: 28px; text-align: center; background-color: <?=$colors->popupCloseBg?>; color: <?=$colors->popupCloseText?>; border-radius: 14px; box-shadow: 0px 3px 9px -2px #000; font-size: 18px;}
body .scoll_popup_container.thepopup_nopadd .popup_scroll_content { padding: 0; }

.progressbar {width: 100%; height: 15px; border:1px solid #ccc; margin: 10px 0 0;}
.progressbar .track{width: 0%; height: 15px; float: left; background-color: <?=$colors->progressBarTrack?>; }
.m_progressbar_container {width: 100%;}

.warning_line{ margin-bottom: 10px; padding: 10px 20px; font-size: 16px; line-height: 20px; background-color: #fcc;}

#body_cont {width: 100%; min-width:1000px;}
#container {  }

.centerwrap{ width:980px; height:100%; margin: 0 auto; position: relative; }
.centerwrap.stretch { width: auto; padding: 0 10px;}
.centerwrap.stretch.e404 { padding: 0 0px; margin: 0 0; }
.centerwrap.reduced {  width:750px; }
/* END Default site Elements */
<?php endif;?>

/* Messages */
<?php if(true):?>
.message_holder { padding: 15px 15px; background-color: #eee;}
.message_holder.error { background-color: #fee;}
.message_holder.success { background-color: #efe;}
/* END Messages */
<?php endif;?>

/* UserView */
<?php if(true):?>
.user_view { position: relative;}
.user_view .action_btns{  }
.user_view .action_btns .btn { width: 100%; margin: 0 0 8px; }
.user_view .action_btns .btnok { width: 100%; margin: 0 0 8px; }
.user_view .big_image{ width: 100%; min-height: 200px; margin-bottom: 5px; position: relative; }
.user_view .big_image img{ width: 100%; height: auto; }
.user_view .big_image .imghider{ width: 100%; height: 140px; position: absolute; bottom: 0; left: 0; padding: 40px 0 0 0; box-sizing: border-box; 
		background-image: -webkit-linear-gradient(top, rgba(255,255,255,0) 0px, rgba(255,255,255,1) 80%);
		background-image: linear-gradient(to bottom, rgba(255,255,255,0) 0px, rgba(255,255,255,1) 80%);
}
.user_view .big_image .imghider .onlinestatus {position: absolute; bottom: 4px; left: 0; width: 100%; height: auto; text-align: center; font-weight: bold;}
.user_view .uinfoline{ margin-bottom: 5px; text-align: center;}
.user_view .status { position: relative; font-size: 18px; font-style: italic; font-family: Georgia; }
.user_view .status .arr{ position: absolute; top: 10px; right: -20px; display: block; width: 0; height: 0; border-bottom: 25px solid transparent; border-left: 25px solid #fff; z-index: 1; }
.user_view .status .status_inner{ margin: 0 0 0 0px; min-height: 25px; background: #fff; padding: 10px; position: relative; z-index: 1; }
/* END UserView */
<?php endif;?>

/* filterTags */
<?php if(true):?>
.filter_tags {margin-bottom: 25px; }
.filter_tags .item { display: inline-block; height: 25px; line-height: 25px; background-color: #fefefe; box-shadow: 0px 2px 5px 0px #ccc; padding: 0 6px; }
.filter_tags .item .action{ text-decoration: none; }
/* END filterTags */
<?php endif;?>

/* Blog */
<?php if(true):?>
.page_content { padding: 0 10px 10px; font-size: 16px; position: relative; }
.page_content h1{ margin: 0 0 10px; font-size: 22px; }

.pageprefooter {padding: 5px 0 0 5px;}

.blog_preview { width: 100%; margin: 10px 10px 20px 0; background-color: #efefef; height: 150px; position:relative;}
.blog_preview .leftpart{ display:block; position:absolute; top: 0; left: 0; width: 150px;  height: 150px;}
.blog_preview .leftpart img{ width: 100%; height: auto; }
.blog_preview .rightpart{  margin-left: 160px; height: 150px; position: relative; overflow: hidden; }
.blog_preview .rightpart .title{ font-size: 20px; font-weight: bold; display: block; margin: 8px 0 10px; }
.blog_preview .rightpart .short_content{  }
.blog_preview .rightpart .btns{ position:absolute; background-color: #efefef; bottom: 0; left: 0px; width: 100%; height: 40px; line-height: 40px; font-size: 16px; padding: 0 10px; box-sizing: border-box; }

.media_preview_editable {display: block; position: relative; background-color: #fafafa; }
.media_preview_editable > a{display: block; float: left; width: 205px; }
.media_preview_editable .editable_place{margin-left: 220px; }
.media_view  { text-align: center; margin: 10px 0px 15px;}
.media_view .imglinkblock-item { }
.media_view .preview { max-width: 100%;  height: auto;}
.media_view .description { font-style: italic; }
/* END Blog */
<?php endif;?>

/* Header */
<?php if(true):?>
.header { 
	color: <?=$colors->text?>; position: relative; margin: 0px 0 30px;
	padding: 0 0px; width: 100%; min-height:106px; z-index: 2;
}
.header .wrap { display: block; position: relative; width:100%; background-color: <?=$colors->colorConvert($colors->headerBg,$colors->headerBgAlpha)?>; }
.header > .bg { display: none; height: 0px;}
.header .logo { display:block; padding: 5px 5px 10px 5px; text-align: center; }
.header .logo img{ max-width: 100%; vertical-align: top; }
.header .btn.auth { margin: 0 0 0 30px; }
.header .fline { font-size: 24px; font-weight: bold; margin-bottom:5px; }
.header .sline { font-size: 14px; }

.header.fixed .wrap { position: fixed; top: 0; left: 0;}
.header.fixed > .bg { display: block; height: 0px; /* Заменяется в js */ }
.header.fixed .logo { height: 0px; padding: 0; overflow: hidden; }

.header .mobilemenu {display:none; margin: 0; width: 50px; height: 40px; padding: 5px 0;  }
.header .mobilemenu .inner {  width: 40px; height: 40px; line-height: 40px; font-size: 20px; }
.header.menu-active .mobilemenu .inner { color: #f00;  }
/* END Header */
<?php endif;?>

/* HeaderProfile */
<?php if(true):?>
.header .profile { display: block; position: absolute; top: 0; right: 0; z-index: 15;  }
.header .profile .popshow{ position: relative; }
.header .profile .popshow .bg{ cursor:pointer; }
.header .profile .pop_cont{ display: none; text-align: left; }
.header .profile .pop_cont .popshadow{ z-index: 111; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.2);  }
.header .profile .pop_cont .arr{ z-index: 113; width: 0; height: 0; position: absolute; top: 33px; right: 5px; border-bottom: 10px solid #fff; border-left: 10px solid transparent; border-right: 10px solid transparent; }
.header .profile .pop_cont .popcontent{ z-index: 112; position: absolute; width: 250px; min-height: 30px; max-height: 300px; top: 42px; right: 0px; background-color: #fff; box-shadow: 0px 2px 5px 0px #ccc; box-shadow: 0px 4px 20px 0px #414141; overflow-y: auto; }
.header .profile .pop_cont .popcontent .popmenu{  }
.header .profile .pop_cont .popcontent .popmenu .item{ display: block; padding: 8px 10px; }
.header .profile .social_login { display: inline-block; width: 250px; height: 35px; margin: 8px 7px 0 0; text-align: right;}
.header .profile .social_login a {vertical-align: middle; }
.header .profile .profile_menu .pop_cont .popcontent{ width: 140px; }
.header .profile .profile_menu{ display: inline-block; width: 35px; height: 35px; margin: 8px 7px 0 0; text-align: right; }
.header .profile .profile_menu .bg{ width:100%; height: 100%; position: relative; }
.header .profile .profile_menu .bg img{ width:100%; height: 100%; border-radius: 100%;}
.header .profile .profile_notifications { display: inline-block; width:35px; height: 35px; margin:8px 7px 0 0; }
.header .profile .profile_notifications .bg{ width:100%; height: 100%; position: relative; }
.header .profile .profile_notifications .bg .cent{ position: absolute; width: 100%; height: 100%; line-height: 35px; text-align: center; left: 0; top: 0; }
.header .profile .profile_notifications .bg .cent.ficon{ z-index: 1; color: #f98e3c; color: #999; font-size: 28px; left: -2px; }
.header .profile .profile_notifications.active .bg .cent.ficon{ color: #f98e3c; }
.header .profile .profile_notifications .bg .cent.counter{ z-index: 2; color: #fff; font-weight: bold; font-size: 10px; }
/* END HeaderProfile */
<?php endif;?>

/* Notifications */
<?php if(true):?>
.notifications { padding: 10px 0;}
.notifications .item { position: relative; height: 65px;  }
.notifications .item .notification_content{ margin: 0 20px 0 50px; padding: 5px 0; font-size: 12px; max-height: 55px; overflow: hidden; }
.notifications .item.new { background-color: #efefef; }
.notifications .item.new .notification_content{ font-weight: bold; }
.notifications .item .pic{ position: absolute; top: 5px; left: 5px; height: 40px; width: 40px; overflow: hidden; }
.notifications .item .pic img{ width: 100%; }
.notifications .item .remove{ position: absolute; right: 5px; top:5px; cursor: pointer; width: 15px; height: 15px; line-height: 15px; font-size: 12px; }
/* END Notifications */
<?php endif;?>

/* Footer */
<?php if(true):?>
.footer {clear: both; padding: 0 0 0; text-align: center; margin: 0 auto 0; background-color: transparent; }
.footer a,.footer .likea {  }
.footer { margin: 20px 0 0px; padding: 15px 15px 10px;  }
.footer .btn{ color:<?=$colors->text?>; text-align: center;  }
.footer .contacts {text-align: left; font-size: 12px; color: #ccc}
.footer .contacts .line{ margin-bottom: 10px;}
.footer .information {text-align: center; font-size: 12px; color: #ccc}
.footer .information .line{ margin-bottom: 10px;}
.footer .support { text-align: left; font-size: 14px; color: #ccc }
.footer .support .line{ margin-bottom: 10px;}
.footer .support .line .dev{ position: relative; display: block; padding: 0 0 0 30px; }
.footer .support .line .dev > span{ position: absolute; top: 10px; left: 0; }
.footer .support a, .footer .support .btn { width: 100%;}
.footer .section { position: relative; display: inline-block; padding: 0 10px; font-size: 11px; /*border-left: 1px solid #999;*/ line-height: 15px; }
.footer .section:first-child {  border-left: none; }
/* END Footer */
<?php endif;?>

.medias_next { }

@media screen and (max-width: 1050px) { /* <= 1021 with scroll (17 or 21 scrooll) total: 1150px  */
	#body_cont {min-width: 320px; width: auto;}
	.centerwrap{ width:auto; margin: 0 20px; padding: 0 0px; }
}
@media screen and (max-width: 720px) { 
	.centerwrap{ width:auto; margin: 0 10px; padding: 0 0px; }
	.page_container .center_container { margin: 0 0px 0 0px;} 
	.page_container .left_container, .page_container .right_container { position: relative; top: auto; left: auto; width: 50%; float: left; }
	.social_login .socbtn {display: none;}
}
@media screen and (max-width: 550px) { 
	.centerwrap{ width:auto; margin: 0 5px; padding: 0 0px; }
	.page_container .left_container, .page_container .right_container { position: relative; top: auto; left: auto; width: 100%; float: none; }
	.register_form, .auth_form{ width: 100%; }
	
	.header .logo { display:block; float:right; padding: 0; margin-right: 100px; }
	.header .logo img{ max-width: 100%; vertical-align: top; height: 40px; }
	.header.fixed{ min-height: 50px; }
	.header.fixed .logo { height: auto; }
	.header .mobilemenu {display:block;;}
	
	.topmenu { display: none; }
	.header.menu-active .topmenu { display: block; }
	
	
	.topmenu { height: auto; text-align: left;  }
	.topmenu .item { display: block; width: 150px;  height: 50px; line-height: 50px; padding: 0 15px;  margin: 0 0px 0 0; }
	.topmenu .subitems { display: none; width: 160px; top: 0px; left: 150px; }

}
<?php if (false):?>
</style>
<?php endif;?>