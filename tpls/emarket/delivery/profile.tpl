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
<form action="%pre_lang%/emarket/purchase/delivery/address/do/" method="post">
        <span class="ordering__form-title">Адрес доставки</span>
        %items%
        %data getCreateForm(%type_id%,'profile')%

	<p>
            <input type="submit" />
	</p>
</form>
END;

$FORMS['delivery_address_item'] = <<<END
    <span class="form__item" rel="%id%">
        <input class="profile__page_adress" type="radio" name="delivery-address" id="%id%" value="%id%" />
        <label for="%id%">%index%, %city%, %street%, %house%</label>
    </span>
END;


?>