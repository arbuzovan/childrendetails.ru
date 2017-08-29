<?php

$FORMS = Array();

$FORMS['reflection_block'] = <<<END
%groups%
END;

$FORMS['reflection_group'] = <<<END
    <input class="profile__page_adress" id="delivery-address-new" name="delivery-address" type="hidden" value="new">
    <div class="form__inner">
        <div class="form__large">
            <input type="text" id="city" name="data[new][city]" class="input form__input" placeholder="Город" rel="">
        </div>
        <div class="form__small">
            <input type="text" id="index" name=data[new][index]" class="input form__input" placeholder="Индекс" rel="">
        </div>
    </div>
    <div class="form__inner">
        <div class="form__large">
            <input type="text" id="street" name="data[new][street]" class="input form__input" placeholder="Улица" rel="">
        </div>
        <div class="form__small">
            <input type="text" id="house" name="data[new][house]" class="input form__input" placeholder="Дом/квартира" rel="">
        </div>
    </div>
END;

?>