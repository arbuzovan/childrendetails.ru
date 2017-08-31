<?php
$FORMS = Array();

$FORMS['delivery_block'] = <<<END
<form action="%pre_lang%/emarket/purchase/delivery/choose/do/" method="post">
	Выберите подходящий вам способ доставки:
	<ul>
		%items%
	</ul>

	<p>
		<input type="submit" />
	</p>
</form>
END;

$FORMS['delivery_item_free'] = <<<END
	<li><input type="radio" name="delivery-id" value="%id%" checked="checked" /> %name% - бесплатно</li>
END;

$FORMS['delivery_item_priced'] = <<<END
	<li><input type="radio" name="delivery-id" value="%id%" /> %name% - %price%</li>
END;


$FORMS['delivery_address_block'] = <<<END
<form name="profile_adress_form" action="" method="post" rel="">
        <span class="ordering__form-title">Адрес доставки</span>
        %items%
        <span class="form__item" rel="new-address">
            <input class="profile__page_adress" name="delivery-address" id="new-address" value="new-address" type="radio">
            <label for="new-address">Новый адрес</label>
            </span>
        %data getCreateForm(%type_id%,'profile')%

            <!--input type="submit" /-->
            <div class="form__button">
                <button class="btn btn__black personal-address__form-btn profile_adress_btn" type="submit">
                    <svg class="btn__icon">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#success"></use>
                    </svg>
                    <span class="btn__text">Сохранить</span>
                </button>
            </div>
</form>
END;

$FORMS['delivery_address_item'] = <<<END
    <span class="form__item" rel="%id%">
        <input class="profile__page_adress" type="radio" name="delivery-address" id="%id%" value="%id%" />
        <label for="%id%">%index%, %city%, %street%, %house%</label>
    </span>
END;


?>