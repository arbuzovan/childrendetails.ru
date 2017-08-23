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
    
    
    
%news lastlist('news','novosti_na_glavnoj',3)%    
    
    

<!--div class="news-prev">
<span class="title"><span class="title__pos">Новости</span></span>
<div class="news-prev__list">

    %data getProperty(%id%, 'novosti_na_glavnoj', 'novosti_na_glavnoj')%
     


</div>
<a href="%content get_page_url(39)%" class="btn btn--grey news-prev__btn">Все новости</a>
</div-->

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

</div>
</div>


%system getOuterContent('./tpls/content/footer.tpl')%