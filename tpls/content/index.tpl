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
        %custom getSiteSetting('Шапка','advantage_1')%
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
        %custom getSiteSetting('Шапка','advantage_2')%
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
        %custom getSiteSetting('Шапка','advantage_3')%
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
        %custom getSiteSetting('Шапка','advantage_4')%
    </div>
</div>
</div>

</div>
</div>

<section class="category">
    <div class="row">
        <div class="col col--33">
            <div class="category-list">
                <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_1',0,1)%)%" class="category-list__item">
                    <span class="category-list__top">
                        <img src="%custom getSiteSetting('Шапка','category_1_image')%" alt="img01">
                    </span>
                    <span class="category-list__bottom">
                        <span class="title category-list__title"><span class="title__pos">Для мальчиков</span></span>
                    </span>
                </a>
            </div>
        </div>
        <div class="col col--33">
            <div class="category-list">
                <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_2',0,1)%)%" class="category-list__item">
                    <span class="category-list__top">
                        <img src="%custom getSiteSetting('Шапка','category_2_image')%" alt="img02">
                    </span>
                    <span class="category-list__bottom">
                        <span class="title category-list__title"><span class="title__pos">Для малышей</span></span>
                    </span>
                </a>
            </div>
        </div>
        <div class="col col--33">
            <div class="category-list">
                <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_3',0,1)%)%" class="category-list__item">
                    <span class="category-list__top">
                        <img src="%custom getSiteSetting('Шапка','category_3_image')%" alt="img03">
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
                <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_4',0,1)%)%" class=" category-box__inner category-box__inner--530" style="background-image: url(%custom getSiteSetting('Шапка','category_4_image')%)">
                    <span class="category-box__text">
                        <span class="category-box__title">Коляски и аксессуары</span>
                        <span class="btn btn__transparent btn__transparent--white">подробнее</span>
                    </span>
                </a>
            </div>
        </div>
<div class="col col--50">
    <div class="category-box">
        <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_5',0,1)%)%" class=" category-box__inner category-box__inner--250" style="background-image: url(%custom getSiteSetting('Шапка','category_5_image')%)">
            <span class="category-box__text">
                <span class="category-box__title">Сумки и рюкзаки</span>
                <span class="btn btn__transparent btn__transparent--white">подробнее</span>
            </span>
        </a>
    </div>
    <div class="category-box">
        <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_6',0,1)%)%" class=" category-box__inner category-box__inner--250" style="background-image: url(%custom getSiteSetting('Шапка','category_6_image')%)">
            <span class="category-box__text">
                <span class="category-box__title">Игрушки</span>
                <span class="btn btn__transparent btn__transparent--white">подробнее</span>
            </span>
        </a>
    </div>
</div>
<div class="col col--100">
    <div class="category-box">
        <a href="%content get_page_url(%custom getSiteSetting('Шапка','category_7',0,1)%)%" class=" category-box__inner category-box__inner--250" style="background-image: url(%custom getSiteSetting('Шапка','category_7_image')%)">
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
    <span class="title">
        <span class="title__pos">%custom getSiteSetting('Шапка','main_page_items_caption')%</span>
    </span>
    <div class="slider-four__list js-slider">
        %custom getMainPageItemsFromSettings()%
    </div>
</div>
    
%news lastlist('news','novosti_na_glavnoj',3)%    
    
<section class="info-text">
    <h1>%header%</h1>
    %content%

</section>

</div>

</div>

%system getOuterContent('./tpls/content/footer.tpl')%