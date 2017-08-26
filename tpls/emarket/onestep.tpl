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
    <div class="order__cart">
        <div class="order__cart-list">
            %items%
        </div>
        <div class="order__cart-cost">
            <span class="order__cart-total">Общая стоимость:</span>
            <span class="order__cart-totalcost">16000 РУБ</span>
            <span class="order__cart-total">Итог: </span>
            <span class="order__cart-totalprice">16800 РУБ</span>
        </div>
    </div>
END;

$FORMS['order_item'] = <<<END
        <a href="#" class="order__cart-item">
            <span class="order__cart-img">
                %custom makeThumbnail(%data getProperty(%element_id%, 'izobrazhenie_1', 'izobrazhenie')%, 70, 70,'default', 0, 0)%
            </span>
            <span class="order__cart-right">
                <span class="order__cart-name">%name%</span>
                <span class="order__cart-price">1835 РУБ</span>
            </span>
        </a>
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