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
        <div class="header__top-tel">
            <a href="tel:%custom getSiteSetting('Шапка','phone_1')%" class="header__top-link">
                <svg class="header__top-icon">
                    <use xlink:href="#phone"></use>
                </svg>
                <span class="header__tel">
                    %custom getSiteSetting('Шапка','phone_1')%</span>
            </a>
        </div>
        <div class="header__top-user">
            <ul class="header__user-list">
                <li class="header__user-item">
                    <a href="#" class="header__user-link">Вход</a>
                </li>
                <li class="header__user-item">
                    <a href="#" class="header__user-link">Регистрация</a>
                </li>
            </ul>
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
                <form action="" class="form form--search js-placeholder-container">
                    <span class="form__search">
                        <input type="text" class="input form__search-input js-search" placeholder="">
                        <div class="placeholderDiv js-placeholderDiv">
                            %custom getPlaceholderFromSettings()%
                        </div>
                        <button type="submit" class="form__btn-icon">
                            <svg class="form__icon">
                            <use xlink:href="#search"></use>
                            </svg>
                        </button>
                    </span>
                </form>
            </div>
            <div class="header__middle-right">
                %custom getSiteSocialsNeworkSetting('Шапка','socials_net','social_networks_header')%
                <div class="header__cart">
                    <a href="/emarket/cart/" class="btn btn-cart">
                        <svg class="btn-cart-icon">
                            <use xlink:href="#cart"></use>
                        </svg>
                        <span class="btn-cart-text">2 ТОВАРА</span>
                        <span class="btn-cart-price">16 000 руб</span>
                    </a>
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
            <a href="tel:%custom getSiteSetting('Шапка','phone_1')%" class="header__bottom-link">%custom getSiteSetting('Шапка','phone_1')%</a>
        </div>
    </div>
</div>
</header>