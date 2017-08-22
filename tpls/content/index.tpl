%system getOuterContent('./tpls/content/header.tpl')%



<div class="wrapper">

<div class="full-slider">
<div class="full-slider__list js-full-slider">

        %content menu('menu-slider',2 , 1)%

</div>
</div>
    

    
    

<div class="container">

<div class="working">
<div class="working__list">

<div class="working__item">
<div class="working__item-holder">
    <div class="working__circle">
        <svg class="working__icon wirking__icon--bicon_1">
        <use xlink:href="#bicon_1"></use>
        </svg>
    </div>
    <div class="working__text">
        <span class="working__title">Работаем</span>
        <span class="working__subtitle">без выходных</span>
    </div>
</div>
</div>

<div class="working__item">
<div class="working__item-holder">
    <div class="working__circle">
        <svg class="working__icon wirking__icon--bicon_2">
        <use xlink:href="#bicon_2"></use>
        </svg>
    </div>
    <div class="working__text">
        <span class="working__title">Доставка</span>
        <span class="working__subtitle">по всей России</span>
    </div>
</div>
</div>

<div class="working__item">
<div class="working__item-holder">
    <div class="working__circle">
        <svg class="working__icon wirking__icon--bicon_3">
        <use xlink:href="#bicon_3"></use>
        </svg>
    </div>
    <div class="working__text">
        <span class="working__title">Скидки</span>
        <span class="working__subtitle">постоянным клиентам</span>
    </div>
</div>
</div>

<div class="working__item">
<div class="working__item-holder">
    <div class="working__circle">
        <svg class="working__icon wirking__icon--bicon_4">
        <use xlink:href="#bicon_4"></use>
        </svg>
    </div>
    <div class="working__text">
        <span class="working__title">Частое</span>
        <span class="working__subtitle">обновление коллекций</span>
    </div>
</div>
</div>

</div>
</div>

<section class="category">
<div class="row">
<div class="col col--33">
<div class="category-list">
    <a href=" %content get_page_url(4)%" class="category-list__item">
        <span class="category-list__top">
            <img src="/pic/category-list/img01.jpg" alt="img01">
        </span>
        <span class="category-list__bottom">
            <span class="title category-list__title"><span class="title__pos">Для мальчиков</span></span>
        </span>
    </a>
</div>
</div>
<div class="col col--33">
<div class="category-list">
    <a href="%content get_page_url(3)%" class="category-list__item">
        <span class="category-list__top">
            <img src="/pic/category-list/img02.jpg" alt="img02">
        </span>
        <span class="category-list__bottom">
            <span class="title category-list__title"><span class="title__pos">Для малышей</span></span>
        </span>
    </a>
</div>
</div>
<div class="col col--33">
<div class="category-list">
    <a href="%content get_page_url(5)%" class="category-list__item">
        <span class="category-list__top">
            <img src="/pic/category-list/img03.jpg" alt="img03">
        </span>
        <span class="category-list__bottom">
            <span class="title category-list__title"><span class="title__pos">Для девочек</span></span>
        </span>
    </a>
</div>
</div>
</div>
</section>
<section class="category category--height">
<div class="row">
<div class="col col--50">
<div class="category-box">
    <a href="%content get_page_url(9)%" class=" category-box__inner category-box__inner--530" style="background-image: url(/pic/category/img01.jpg)">
        <span class="category-box__text">
            <span class="category-box__title">Коляски и аксессуары</span>
            <span class="btn btn__transparent btn__transparent--white">подробнее</span>
        </span>
    </a>
</div>
</div>
<div class="col col--50">
<div class="category-box">
    <a href="%content get_page_url(6)%" class=" category-box__inner category-box__inner--250" style="background-image: url(/pic/category/img02.jpg)">
        <span class="category-box__text">
            <span class="category-box__title">Сумки и рюкзаки</span>
            <span class="btn btn__transparent btn__transparent--white">подробнее</span>
        </span>
    </a>
</div>
<div class="category-box">
    <a href="%content get_page_url(8)%" class=" category-box__inner category-box__inner--250" style="background-image: url(/pic/category/img03.jpg)">
        <span class="category-box__text">
            <span class="category-box__title">Игрушки</span>
            <span class="btn btn__transparent btn__transparent--white">подробнее</span>
        </span>
    </a>
</div>
</div>
<div class="col col--100">
<div class="category-box">
    <a href="%content get_page_url(7)%" class=" category-box__inner category-box__inner--250" style="background-image: url(/pic/category/img04.jpg)">
        <span class="category-box__text">
            <span class="category-box__title">Домашняя коллекция</span>
            <span class="btn btn__transparent btn__transparent--white">подробнее</span>
        </span>
    </a>
</div>
</div>
</div>
</section>

    
    
    
    
<div class="slider-four">
<span class="title"><span class="title__pos">Вам может понравиться</span></span>
<div class="slider-four__list js-slider">
  
%data getProperty(%id%, 'tovary_na_glavnoj', 'tovary_na_glavnoj')%
    
</div>
</div>
    
    
    
    
    
    

<div class="news-prev">
<span class="title"><span class="title__pos">Новости</span></span>
<div class="news-prev__list">

    %data getProperty(%id%, 'novosti_na_glavnoj', 'novosti_na_glavnoj')%
     
    


<div class="news-prev__item">
<iframe src="//widget.instagramm.ru/?imageW=2&imageH=1&thumbnail_size=124&type=0&typetext=mtsite.ru&head_show=1&profile_show=1&shadow_show=0&bg=255,255,255,1&opacity=true&head_bg=1a1819&subscribe_bg=91bdc0&border_color=f0f1f3&head_title=" allowtransparency="true" frameborder="0" scrolling="no" style="border:none;overflow:hidden;width:274px;height:296px;"></iframe>
</div>
</div>
<a href="%content get_page_url(39)%" class="btn btn--grey news-prev__btn">Все новости</a>
</div>

<section class="info-text">
    
    %content%

</section>

</div>

</div>

<div id="question" class="popup" style="display: none">
<div class="popup__wrapper popup__wrapper-question">

<span class="title"><span class="title__pos">Возникли вопросы?</span></span>
<span class="popup__info">Мы перезвоним вам в ближайшее время</span>
<span class="popup__subinfo">График работы понедельник - пятница с 09:00 - 18:00</span>
<div class="popup__form">
<form action="" class="form">
<div class="form__item">
<input type="text" class="input form__input" placeholder="Ваше имя">
</div>
<div class="form__item">
<input type="tel" class="input form__input" placeholder="Ваш телефон">
</div>
<div class="form__item">
<textarea name="" id="" cols="30" rows="10" class="textarea form__textarea" placeholder="Ваш комментарий"></textarea>
</div>
<a href="#" class="btn btn__black js-popups-true">Отправить</a>
</form>
</div>




</div>
</div>

<div id="oneclick" class="popup" style="display: none">
<div class="popup__wrapper popup__wrapper-oneclick">


<span class="title"><span class="title__pos">Купить в  1 клик</span></span>
<span class="popup__info">Мы перезвоним вам в ближайшее время</span>
<span class="popup__subinfo">Для совершения заказа достаточно ввести <br>ваше имя и контактный телефон</span>
<div class="popups__form">
<form action="" class="form">
<div class="form__item">
<input type="text" class="input form__input" placeholder="Ваше имя">
</div>
<div class="form__item">
<input type="tel" class="input form__input" placeholder="Ваш телефон">
</div>
<a href="#" class="btn btn__black js-popups-true">Отправить</a>
</form>
</div>



</div>
</div>

<div id="carts" class="popup" style="display: none">
<div class="popup__wrapper popup__wrapper-cart">



<span class="title"><span class="title__pos">Товар добавлен в корзину</span></span>
<span class="popup__info">Теперь в Вашей корзине</span>
<span class="popup__info-cart"><a href="#" class="popup__info-link">2 ТОВАРА</a> на сумму 1600 РУБ</span>
<div class="popup__product">
<div class="popup__product-list">
<div class="order__cart">
<div class="order__cart-list">

    <a href="#" class="order__cart-item">
        <span class="order__cart-img">
            <img src="pic/cart/img01.jpg" alt="img01">
        </span>
        <span class="order__cart-right">
            <span class="order__cart-name">Комплект Babystyle Защитный оранжевый</span>
            <span class="order__cart-price">8660 РУБ</span>
        </span>
    </a>

    <a href="#" class="order__cart-item">
        <span class="order__cart-img">
            <img src="pic/cart/img02.jpg" alt="img02">
        </span>
        <span class="order__cart-right">
            <span class="order__cart-name">Пустышка - Розовая пудра</span>
            <span class="order__cart-price">1835 РУБ</span>
        </span>
    </a>

</div>
</div>
</div>
</div>
<div class="popup__bottom">
<span class="popup__col">
<a href="#" class="btn btn__transparent btn__transparent--black bold">
<svg class="btn__transparent-icon btn__transparent-icon--left">
<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow_left"></use>
</svg>
<span class="btn__transparent-text">Продолжить покупки</span>
</a>
</span>
<span class="popup__col">
<a href="#" class="btn btn__black  bold">Перейти в корзину</a>
</span>
</div>


</div>
</div>

<div id="thank" class="popup" style="display: none">
<div class="popup__wrapper popup__wrapper-thank">




<span class="title"><span class="title__pos">Спасибо!</span></span>
<span class="popup__info">Ваш запрос успешно отправлен</span>
<span class="popup__subinfo">Мы перезвоним Вам в ближайшее время</span>

</div>
</div>

%system getOuterContent('./tpls/content/footer.tpl')%