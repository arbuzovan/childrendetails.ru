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
            <span class="popup__info-cart"><a href="#" class="popup__info-link">2 ТОВАРА</a> на сумму 1600 РУБ</span>
            <div class="popup__product">
                <div class="popup__product-list">
                    <div class="order__cart">
                        <div class="order__cart-list">
                            <a href="#" class="order__cart-item">
                                <span class="order__cart-img">
                                    <img src="/pic/cart/img01.jpg" alt="img01">
                                </span>
                                <span class="order__cart-right">
                                    <span class="order__cart-name">Комплект Babystyle Защитный оранжевый</span>
                                    <span class="order__cart-price">8660 РУБ</span>
                                </span>
                            </a>
                            
                            <a href="#" class="order__cart-item">
                                <span class="order__cart-img">
                                    <img src="/pic/cart/img02.jpg" alt="img02">
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
          <span class="select">
             <select class="option" name="" id="">
                <option value="">68 СМ</option>
                <option value="">78 СМ</option>
                <option value="">88 СМ</option>
             </select>
          </span>
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