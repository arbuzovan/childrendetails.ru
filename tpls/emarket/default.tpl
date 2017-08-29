<?php
$FORMS = array();
	
$FORMS['price_block'] = <<<END
<span class="slider-four__price">
    %price-original%
    %price-actual%
</span>
END;

$FORMS['price_original'] = <<<END
    <span class="slider-four__price-old">%prefix%&nbsp;%original%&nbsp;%suffix%</span>
END;

$FORMS['price_actual'] = <<<END
<span class="slider-four__price-now">%prefix%&nbsp;%actual% &nbsp;%suffix%</span>
END;


$FORMS['order_block'] = <<<END
<div class="cart">
   <table class="cart__table">
      <thead class="cart__thead">
         <tr class="cart__row">
            <th class="cart__cell cart__cell--head"></th>
            <th class="cart__cell cart__cell--head">Название товара</th>
            <th class="cart__cell cart__cell--head">Характеристики</th>
            <th class="cart__cell cart__cell--head">Цена</th>
            <th class="cart__cell cart__cell--head">Количество</th>
            <th class="cart__cell cart__cell--head">Стоимость</th>
            <th class="cart__cell cart__cell--head"></th>
         </tr>
      </thead>
      <tbody class="cart__tbody">
            %items%
      </tbody>
   </table>
   <div class="cart-bottom">
        <div class="cart-bottom__right">
            <span class="cart-bottom__prod">Вы выбрали %total-amount% товаров</span>
            <span class="cart-bottom__text">Общая стоимость: </span>
            <span class="cart-bottom__price">%total-price%</span>
            <span class="cart-bottom__btn">
                <a href="/emarket/one_step_order/" class="btn btn__black">Оформить заказ</a>
            </span>
        </div>
   </div>
</div>

<!-- персональные реккомендации.

<div class="slider-four">
   <span class="title"><span class="title__pos">Персональные рекомендации</span></span>
   <div class="slider-four__list js-slider">
      <div class="slider-four__item">
         <div class="slider-four__holder">
            <a href="pic/slider_four_scroll/img09.png" class="slider-four__img">
            <img src="pic/slider_four_scroll/img09.png" alt="img09">
            </a>
            <a href="#" class="slider-four__title js-height">Комплект Babystyle Защитный оранжевый</a>
            <a href="#" class="slider-four__category ">Elodie Details</a>
            <span class="slider-four__price">
            <span class="slider-four__price-now">8660 РУБ</span>
            </span>
         </div>
      </div>

      </div>
   </div>
</div>

-->
END;

$FORMS['order_item'] = <<<END
    <tr class="cart__row">
       <td class="cart__cell">
          <div class="cart__cell-img">
            <a href="#" class="cart__link-img">
                %custom makeThumbnail(%data getProperty(%element_id%, 'izobrazhenie_1', 'izobrazhenie')%, 150, 150,'default', 0, 0)%
            </a>
          </div>
       </td>
       <td class="cart__cell">
          <a href="%link%" class="cart__name">%name%</a>
          <span class="cart__article">
           %data getProperty(%element_id%,'sku')%
          </span>
       </td>
       <td class="cart__cell">
            %options%
       </td>
       <td class="cart__cell">
          <span class="cart__price" data-text="Цена">%emarket price(%element_id%)%</span>
       </td>
       <td class="cart__cell">
          <div class="spinner js-spinner" data-min="1" data-max="100">
             <a href="#" title="" class="spinner__trigger spinner__trigger--minus js-spinner-minus">
                <svg class="icon-minus spinner__trigger-icon">
                   <use xlink:href="#button_minus"></use>
                </svg>
             </a>
             <input type="text" class="spinner__input js-spinner-input" value="%amount%">
             <a href="#" title="" class="spinner__trigger spinner__trigger--plus js-spinner-plus">
                <svg class="icon-plus spinner__trigger-icon">
                   <use xlink:href="#button_plus"></use>
                </svg>
             </a>
          </div>
       </td>
        <td class="cart__cell">
           <span class="cart__total" data-text="Итого">%total-price%</span>
        </td>
        <td class="cart__cell">
             <a href="%pre_lang%/emarket/basket/remove/item/%id%/" class="cart__remove">
                 <svg class="cart__remove-ico error-ico">
                     <use xlink:href="#close"></use>
                 </svg>
             </a>
        </td>
    </tr>
    <tr class="cart__line"></tr>
END;

$FORMS['options_block'] = <<<END
	%items%
END;

$FORMS['options_block_empty'] = "---";

$FORMS['options_item'] = <<<END
%name% +%price%%list-comma%
END;

$FORMS['order_block_empty'] = <<<END
<p>Корзина пуста</p>
END;


$FORMS['purchase'] = <<<END
%purchasing%

%emarket ordersList()%
END;


$FORMS['orders_block'] = <<<END
<p>Список ваших заказов:</p>
<ul>
	%items%
</ul>
END;

$FORMS['orders_block_empty'] = <<<END
<p>Заказов нет</p>
END;

$FORMS['orders_item'] = <<<END
	<li>%name% (%id%)</li>
END;

$FORMS['purchase_successful'] = <<<END
<p>Заказ успешно добавлен</p>
END;

$FORMS['purchase_failed'] = <<<END
<p>Не удалось добавить заказ</p>
END;


$FORMS['personal'] = <<<END
	%emarket ordersList()%
END;

?>