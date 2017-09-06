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
    <noindex>%custom getSiteSetting('Шапка','empty_categroy_text')%</noindex>
END;


$FORMS['category_block_line'] = <<<END
 <div class="rubric__item">
  <a href="%link%" class="rubric__item-holder">
        <span class="rubric__top">
            %custom makeThumbnail(%data getProperty(%id%, 'header_pic', 'izobrazhenie')%, 270, 235,'default', 0, 0, '%h1%')%
        </span>
                <span class="rubric__bottom">
            <span class="rubric__title">
                <span class="rubric__title-pos js-height">%text%</span>
            </span>
            <span class="rubric__calc">
                <span class="rubric__calc-name">%catalog getCountItems(%id%)%</span>
                <span class="rubric__calc-product">%catalog getCountSubCategries(%id%)%</span>
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
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, auto, 170,'default', 0, 0)%
                    <!--img src="%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%" alt=""-->
                </div>

                <div class="product__slider-navitem">
                    <!img src="%data getProperty(%id%, 'izobrazhenie_2', 'izobrazhenie')%" alt=""-->
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_2', 'izobrazhenie')%, auto, 170,'default', 0, 0)%
                </div>

                <div class="product__slider-navitem">
                    <!img src="%data getProperty(%id%, 'izobrazhenie_3', 'izobrazhenie')%" alt=""-->
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_3', 'izobrazhenie')%, auto, 170,'default', 0, 0)%
                </div>

                <div class="product__slider-navitem">
                    <!--img src="%data getProperty(%id%, 'izobrazhenie_4', 'izobrazhenie')%" alt=""-->
                    %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_4', 'izobrazhenie')%, auto, 170,'default', 0, 0)%
                </div>

        </div>
    </div>
      
      
      
   </div>
   
   <!-- информация от продукте -->	
   <div class="product__col">
      <div class="product__prev">
         <div class="product__prev-nav">
            <span class="product__prev-art">%data getProperty(%id%, 'sku')%</span>
            <div class="product__color">
               %data getProperty(%id%,'colors','colors')%
            </div>
            
            <div class="product__size">
                %data getProperty(%id%,'size','optionsFld')%
            </div>
            <div class="product__age">
                %data getProperty(%id%,'age','optionsFld')%
            </div>
         </div>
         <div class="product__price">
            %emarket price(%id%,'itemCartPrice')%
            <div class="spinner js-spinner" data-min="1" data-max="100">
               <a href="#" title="" class="spinner__trigger spinner__trigger--minus js-spinner-minus">
                  <svg class="icon-minus spinner__trigger-icon">
                     <use xlink:href="#button_minus"></use>
                  </svg>
               </a>
               <input id="amount_%id%" rel="%id%" type="text" class="spinner__input js-spinner-input" value="1">
               <a href="#" title="" class="spinner__trigger spinner__trigger--plus js-spinner-plus">
                  <svg class="icon-plus spinner__trigger-icon">
                     <use xlink:href="#button_plus"></use>
                  </svg>
               </a>
            </div>
            <span class="product__btn">
               <a href="#carts" rel="%id%" class="btn btn__product-cart js-popup-carts">
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