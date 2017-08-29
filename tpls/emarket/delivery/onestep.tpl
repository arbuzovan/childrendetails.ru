<?php
$FORMS = Array();

$FORMS['delivery_block'] = <<<END
    <div class="ordering__form-delivery">
        <span class="ordering__form-title">Способ доставки</span>
        %items%
    </div>
END;

$FORMS['delivery_item_free'] = <<<END
    <span class="form__item">
        <input class = "delivery-id" type="radio" name="delivery-id" id="%id%" value="%id%" checked="checked" rel="free">
        <label for="%id%">%name% - бесплатно</label>
    </span>
END;

$FORMS['delivery_item_priced'] = <<<END
    <span class="form__item">
        <input class = "delivery-id" type="radio" name="delivery-id" id="%id%" value="%id%" rel="%id%">
        <label for="%id%">%name%</label>
    </span>
END;


$FORMS['delivery_address_block'] = <<<END
    <span class="ordering__form-title">Адрес доставки</span>
        %items%
        %data getCreateForm(%type_id%,'adress_onestep')%
END;

$FORMS['delivery_address_item'] = <<<END
    <span class="form__item">
        <input type="radio" name="delivery-address" id="%id%" value="%id%" />
        <label for="%id%">%index%, %city%, %street%, %house%</label>
    </span>
END;


?>