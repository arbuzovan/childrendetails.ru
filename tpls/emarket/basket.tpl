<?php
$FORMS = array();

$FORMS['order_block'] = <<<END
<a href="/emarket/cart/" class="btn btn-cart">
                        <svg class="btn-cart-icon">
                            <use xlink:href="#cart"></use>
                        </svg>
                        <span class="btn-cart-text"><span class="totalAmount">%total-amount%</span> ТОВАРА</span>
                        <span class="btn-cart-price"><span class="totalCost">%total-price%</span></span>
</a>
<!-- на базе этого можно замутить "всплывашку"
div id="rubricator" style="display:none" class="block">
    <ul>
        %items%
    </ul>
</div-->
END;

$FORMS['order_item'] = <<<END
    <li>
        <a href="%link%">%name%</a> x %amount%
        <br />
        %total-price%
        <a href="%pre_lang%/emarket/basket/remove/item/%id%/">(X)</a>
    </li>
END;


$FORMS['price_original'] = <<<END
<strike>%prefix%&nbsp;%original%&nbsp;%suffix%</strike>
END;

$FORMS['price_actual'] = <<<END
%prefix%&nbsp;%actual%&nbsp;%suffix%
END;

$FORMS['order_block_empty'] = <<<END
<a nohref class="btn btn-cart">
    <svg class="btn-cart-icon">
        <use xlink:href="#cart"></use>
    </svg>
    <span class="btn-cart-text"><span class="totalAmount">0</span> ТОВАРОВ</span>
    <span class="btn-cart-price"><span class="totalCost"></span></span>
</a>
END;

?>