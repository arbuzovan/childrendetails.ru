<?php

$FORMS = Array();

$FORMS['category'] = <<<END
%catalog getCategoryList('default', '%category_id%', 100, 1)%
%catalog getObjectsList('default', '%category_id%')%

END;


$FORMS['category_block'] = <<<END

%lines%

END;


$FORMS['category_block_empty'] = <<<END

END;


$FORMS['category_block_line'] = <<<END
 <div class="rubric__item">
  <a href="%link%" class="rubric__item-holder">
        <span class="rubric__top">
            <img src="%header_pic%" alt="%text%">
            %custom makeThumbnail(%data getProperty(%id%, '%header_pic%', 'izobrazhenie')%, 270, 235,'default', 0, 0, '%h1%')%
        </span>
                <span class="rubric__bottom">
            <span class="rubric__title">
                <span class="rubric__title-pos js-height">%text%</span>
            </span>
            <span class="rubric__calc">
                <span class="rubric__calc-name">%catalog getCountItems(%id%)% наименова%catalog getCountItemsPostfix(%catalog getCountItems(%id%)%)%</span>
                <span class="rubric__calc-product">%catalog getCountSubCategries(%id%)% видов продукции</span>
            </span>
        </span>
</a>
</div>
END;




$FORMS['objects_block'] = <<<END
%system numpages(%total%, %per_page%)%
%lines%
%system numpages(%total%, %per_page%)%

END;


$FORMS['objects_block_search_empty'] = <<<END
%catalog search('%category_id%')%

<p>По Вашему запросу ничего не найдено.</p>

END;


$FORMS['objects_block_line'] = <<<END
<div class="catalog-subsection__item">
   <div class="slider-four__item">
      <div class="slider-four__holder">
        <a href="%link%" class="slider-four__img">
            %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 270, 270,'default', 0, 0, %h1%)%
        </a>
        <a href="%link%" class="slider-four__title js-height">%h1%</a>
        <a href="%link%" class="slider-four__category ">%title%</a>
        <span class="slider-four__price">
            <span class="slider-four__price-now">%emarket price(%id%)%</span>
        </span>
      </div>
   </div>
</div>
END;



$FORMS['view_block'] = <<<END
<div class="product__row">
   <div class="product__col">
      
<div class="product__slider js-product-slider-container">
    <div class="product__slider-list js-product-slider">
        
            <div class="product__slider-item">
                <a href="%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%" class="product__slider-link">
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 570, 570,'default', 0, 0, '%h1%')%
                </a>
            </div>
        
            <div class="product__slider-item">
                <a href="%data getProperty(%id%, 'izobrazhenie_2', 'izobrazhenie')%" class="product__slider-link">
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_2', 'izobrazhenie')%, 570, 570,'default', 0, 0, '%h1%')%
                </a>
            </div>
        
            <div class="product__slider-item">
                <a href="%data getProperty(%id%, 'izobrazhenie_3', 'izobrazhenie')%" class="product__slider-link">
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_3', 'izobrazhenie')%, 570, 570,'default', 0, 0, '%h1%')%
                </a>
            </div>
        
            <div class="product__slider-item">
                <a href="%data getProperty(%id%, 'izobrazhenie_4', 'izobrazhenie')%" class="product__slider-link">
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_4', 'izobrazhenie')%, 570, 570,'default', 0, 0, '%h1%')%
                </a>
            </div>

        
    </div>
    <div class="product__slider-navlist js-product-slider-nav">
        
            <div class="product__slider-navitem">
                <img src="%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%" alt="img01">
            </div>
            
            <div class="product__slider-navitem">
                <img src="%data getProperty(%id%, 'izobrazhenie_2', 'izobrazhenie')%" alt="img01">
            </div>
            
            <div class="product__slider-navitem">
                <img src="%data getProperty(%id%, 'izobrazhenie_3', 'izobrazhenie')%" alt="img01">
            </div>
        
            <div class="product__slider-navitem">
                <img src="%data getProperty(%id%, 'izobrazhenie_4', 'izobrazhenie')%" alt="img01">
            </div>
        
    </div>
</div>
      
      
      
   </div>
   
   <!-- информация от продукте -->	
   <div class="product__col">
      <div class="product__prev">
         <div class="product__prev-nav">
            <span class="product__prev-art">Артикул: 140211-959-3899</span>
            <div class="product__color">
               <span class="product__title">Цветовая схема</span>
               <ul class="product__color-list">
                  <li class="product__color-item">
                     <a href="#" class="product__color-link active">
                     <img src="/pic/product-color/img01.jpg" alt="img01">
                     </a>
                  </li>
                  <li class="product__color-item">
                     <a href="#" class="product__color-link ">
                     <img src="/pic/product-color/img02.jpg" alt="img02">
                     </a>
                  </li>
                  <li class="product__color-item">
                     <a href="#" class="product__color-link ">
                     <img src="/pic/product-color/img03.jpg" alt="img03">
                     </a>
                  </li>
                  <li class="product__color-item">
                     <a href="#" class="product__color-link ">
                     <img src="/pic/product-color/img04.jpg" alt="img04">
                     </a>
                  </li>
                  <li class="product__color-item">
                     <a href="#" class="product__color-link ">
                     <img src="/pic/product-color/img05.jpg" alt="img05">
                     </a>
                  </li>
                  <li class="product__color-item">
                     <a href="#" class="product__color-link ">
                     <img src="/pic/product-color/img06.jpg" alt="img06">
                     </a>
                  </li>
               </ul>
            </div>
            <div class="product__size">
               <span class="product__title">Размер</span>
               <select name="" id="" class="js-select-size">
                  <option label="Выберите размер"></option>
                  <option value="62 СМ">62 СМ</option>
                  <option value="68 СМ">68 СМ</option>
                  <option value="74 СМ">74 СМ</option>
                  <option value="80 СМ">80 СМ</option>
                  <option value="86 СМ">86 СМ</option>
               </select>
            </div>
            <div class="product__age">
               <span class="product__title">Возраст</span>
               <select name="" id="" class="js-select-age">
                  <option label="Выберите Возраст"></option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
               </select>
            </div>
         </div>
         <div class="product__price">
            %emarket price(%id%,'itemCartPrice)%
            <div class="spinner js-spinner" data-min="1" data-max="100">
               <a href="#" title="" class="spinner__trigger spinner__trigger--minus js-spinner-minus">
                  <svg class="icon-minus spinner__trigger-icon">
                     <use xlink:href="#button_minus"></use>
                  </svg>
               </a>
               <input type="text" class="spinner__input js-spinner-input">
               <a href="#" title="" class="spinner__trigger spinner__trigger--plus js-spinner-plus">
                  <svg class="icon-plus spinner__trigger-icon">
                     <use xlink:href="#button_plus"></use>
                  </svg>
               </a>
            </div>
            <span class="product__btn">
               <a href="#carts" class="btn btn__product-cart js-popup">
                  <svg class="btn__product-cart-icon">
                     <use xlink:href="#cart"></use>
                  </svg>
                  <span class="btn__product-cart-text">В корзину</span>
               </a>
            </span>
            <span class="product__btn">
            <a href="#oneclick" class="btn btn--grey js-popup">Купить в 1 клик</a>
            </span>
         </div>
         <div class="product__info">
            <span class="product__title">ОПИСАНИЕ</span>
            %opisanie%
         </div>
      </div>
   </div>
   <!-- информация от продукте -->	
   
</div>
%data getProperty(%id%, 's_etim_tovarom_pokupayut', 's_etim_tovarom_pokupayut')%

END;

$FORMS['search_block'] = <<<END

<form method="get" action="%content get_page_url(%category_id%)%">
<h3>Фильтр по товарам</h3>
%lines%
<p>
	<input type="submit" value="Подобрать" /><br />
	<input type="button" onclick="javascript: window.location = '%content get_page_url(%category_id%)%';" value="Сбросить" />
</p>
</form>


END;


$FORMS['search_block_line'] = <<<END
	<p>
		%selector%
	</p>
END;



$FORMS['search_block_line_relation'] = <<<END
%title% <select name="fields_filter[%name%]"><option />%items%</select>

END;

$FORMS['template_block_line_symlink'] = <<<END
%title% <select name="fields_filter[%name%]"><option />%items%</select>

END;

$FORMS['search_block_line_text'] = <<<END
%title% <input type="text" name="fields_filter[%name%]" class="textinputs" value="%value%" />

END;

$FORMS['search_block_line_price'] = <<<END
%title% от &nbsp;до 
<input type="text" name="fields_filter[%name%][0]" value="%value_from%" />
<input type="text" name="fields_filter[%name%][1]" value="%value_to%" />

END;

$FORMS['search_block_line_boolean'] = <<<END
%title% <input type="checkbox" name="fields_filter[%name%]" %checked% value="1" /> 

END;

?>