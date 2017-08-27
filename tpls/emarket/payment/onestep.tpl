<?php
$FORMS = Array();

$FORMS['payment_block'] = <<<END
<div class="ordering__form-pay">
    <span class="ordering__form-title">Способ оплаты</span>
    %items%
</div>
END;

$FORMS['payment_item'] = <<<END
    <span class="form__item">
        <input type="radio" name="payment-id" id="%id%" value="%id%">
        <label for="%id%">%name%</label>
    </span>
    
END;

$FORMS['bonus_block'] = <<<END
    <form id="bonus_payment" method="post" action="%pre_lang%/emarket/purchase/payment/bonus/do/">
            <p>Вы можете оплатить ваш заказ накопленными бонусами. Доступно бонусов на %available_bonus%.</p>
            <p>Вы собираетесь оплатить заказ на сумму %actual_total_price%.</p>
            <label><input type="text" name="bonus"/>Количество бонусов</label>
            <input type="submit" value="Продолжить" />
    </form>

END;

?>