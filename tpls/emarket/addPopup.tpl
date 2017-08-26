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
    <span class="popup__info-cart">
    <a href="/emarket/cart/" class="popup__info-link">%total_amount% ТОВАРА</a> на сумму %total_price% РУБ</span>
    <div class="popup__product">
        <div class="popup__product-list">
            <div class="order__cart">
                <div class="order__cart-list">
                    %items%
                </div>
            </div>
        </div>
    </div>
    <div class="popup__bottom">
        <span class="popup__col">
            <a href="#" class="btn btn__transparent btn__transparent--black bold js-popups-close">
                <svg class="btn__transparent-icon btn__transparent-icon--left">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow_left"></use>
                </svg>
                <span class="btn__transparent-text">Продолжить покупки</span>
            </a>
        </span>
        <span class="popup__col">
            <a href="/emarket/cart/" class="btn btn__black  bold">Перейти в корзину</a>
        </span>
    </div>
END;

$FORMS['order_item'] = <<<END
<a href="%link%" class="order__cart-item">
    <span class="order__cart-img">
        %custom makeThumbnail(%data getProperty(%element_id%, 'izobrazhenie_1', 'izobrazhenie')%, 120, 120,'default', 0, 0)%
    </span>
    <span class="order__cart-right">
        <span class="order__cart-name">%name%</span>
        <span class="order__cart-price">%price%</span>
    </span>
</a>
END;

$FORMS['options_block'] = <<<END
	%items%
END;

$FORMS['options_block_empty'] = "";

$FORMS['options_item'] = <<<END
%name% +%price%%list-comma%
END;

$FORMS['order_block_empty'] = <<<END
END;


?>