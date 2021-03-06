<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>%title%</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- @if NODE_ENV='production' -->
<link rel="stylesheet" href="/css/main.css">
<link rel="shortcut icon" href="/pic/favicon_16.jpg" sizes="16x16" type="image/x-icon">
<link rel="shortcut icon" href="/pic/favicon_164.jpg" sizes="164x164" type="image/x-icon">
<meta name="robots" content="noindex, nofollow"/>
<!-- @endif -->
</head>
<body>
<div style="height: 0; width: 0; position: absolute;overflow: hidden;" id="svg-sprite-container">

</div>


<header class="header sticky-wrapper">
<div class="header__top">
<div class="container">
<div class="header__top-left">

<nav class="header__nav">
    <div class="nav">
    %content menu('menu-top')%

</div>
</nav>

</div>
<div class="header__top-right">
    <div class="header__top-row">
        %users auth('header')%
        <div class="header__top-tel">
            <a href="tel:%custom getSiteSetting('Шапка','phone_1', true)%" class="header__top-link">
                <svg class="header__top-icon">
                    <use xlink:href="#phone"></use>
                </svg>
                <span class="header__tel">
                    %custom getSiteSetting('Шапка','phone_1')%</span>
            </a>
        </div>
    </div>
</div>
</div>
</div>
<div class="header__middle">
    <div class="container">
        <div class="header__middle-holder">
            <div class="header__middle-left">
                <a href="/" class="header__logo">
                    %custom makeThumbnail(%custom getSiteSetting('Шапка','logo')%, 200, auto,'default', 0, 0, %header%)%
                </a>
            </div>
            <div class="header__search">
                %search insert_form()%
            </div>
            <div class="header__middle-right">
                %custom getSiteSocialsNeworkSetting('Шапка','socials_net','social_networks_header')%
                <div class="header__cart">
                    %emarket cart('basket')%
                </div>
            </div>
        </div>
    </div>
</div>
<div class="header__bottom js-sticky-header">
    <div class="container">
        <div class="header__bottom-left">
            <a href="#" class="header__bottom-logo">
                <img src="%custom getSiteSetting('Шапка','small_logo')%" alt="%header%">
            </a>
        </div>
        <div class="header__bottom-center">

        %content menu('menu-catalog', 3, 2)%

        </div>
        <div class="header__bottom-right">
            <a href="tel:%custom getSiteSetting('Шапка','phone_1', true)%" class="header__bottom-link">%custom getSiteSetting('Шапка','phone_1')%</a>
        </div>
    </div>
</div>
</header>
