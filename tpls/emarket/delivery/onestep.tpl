<?php
$FORMS = Array();

$FORMS['delivery_block'] = <<<END
<form action="" method="post">
            <div class="ordering__form-delivery">
                <span class="ordering__form-title">Способ доставки</span>
                %items%
            </div>
</form>
END;

$FORMS['delivery_item_free'] = <<<END
    <span class="form__item">
        <input type="radio" name="radio_%id%" id="%id%">
        <label for="radio_%id%">%name% - бесплатно</label>
    </span>
END;

$FORMS['delivery_item_priced'] = <<<END
    <span class="form__item">
        <input type="radio" name="radio_%id%" id="%id%">
        <label for="radio_%id%">%name% - %price%</label>
    </span>
END;


$FORMS['delivery_address_block'] = <<<END
<form action="%pre_lang%/emarket/purchase/delivery/address/do/" method="post">
	Выберите подходящий вам адрес доставки:
	<ul>
		%items%
		<li>
			<input type="radio" name="delivery-address" value="new" />Новый адрес
			<div>
			%data getCreateForm(%type_id%)%
			</div>
		</li>
	</ul>

	<p>
		<input type="submit" />
	</p>
</form>
END;

$FORMS['delivery_address_item'] = <<<END
	<li><input type="radio" name="delivery-address" value="%id%" />%index%, %city%, %street%, д. %house%, кв. %flat%</li>
END;


?>