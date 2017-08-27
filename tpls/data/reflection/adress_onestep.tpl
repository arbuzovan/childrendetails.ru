<?php

$FORMS = Array();

$FORMS['reflection_block'] = <<<END
%groups%
END;

$FORMS['reflection_group'] = <<<END
    <span class="form__item">
        <input id="delivery-address-new" name="delivery-address" type="radio" value="new">
        <label for="delivery-address-new">Новый адрес доставки</label>
    </span>
    <div class="form__inner">
        <div class="form__large">
            <input type="text" name="data[new][city]" class="input form__input" placeholder="Город" rel="new_adress">
        </div>
        <div class="form__small">
            <input type="text" name=data[new][index]" class="input form__input" placeholder="Индекс" rel="new_adress">
        </div>
    </div>
    <div class="form__inner">
        <div class="form__large">
            <input type="text" name="data[new][street]" class="input form__input" placeholder="Улица" rel="new_adress">
        </div>
        <div class="form__small">
            <input type="text" name="data[new][house]" class="input form__input" placeholder="Дом/квартира" rel="new_adress">
        </div>
    </div>
    <div class="ordering__form-note">
        <span class="ordering__form-title">Примечание к заказу</span>
        <textarea name="data[new][order_comments]" id="" cols="30" rows="10" class="textarea form__textarea ordering__textarea" placeholder="Ваш коментарий"></textarea>
    </div>
END;


$FORMS['reflection_field_string'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="text" name="%input_name%" value="%value%" size="50" />
		</td>
	</tr>

END;


$FORMS['reflection_field_text'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<textarea name="%input_name%" style="width: 200px">%value%</textarea>
		</td>
	</tr>

END;


$FORMS['reflection_field_wysiwyg'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<textarea name="%input_name%" style="width: 200px">%value%</textarea>
		</td>
	</tr>

END;


$FORMS['reflection_field_int'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="text" name="%input_name%" value="%value%" size="15" />
		</td>
	</tr>

END;


$FORMS['reflection_field_boolean'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="hidden" id="%input_name%" name="%input_name%" value="%value%" />
			<input onclick="javascript:document.getElementById('%input_name%').value = this.checked;" type="checkbox" %checked% value="1" />
		</td>
	</tr>

END;


$FORMS['reflection_field_password'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<input type="password" name="%input_name%" value="" size="50" />
		</td>
	</tr>

	<tr>
		<td>
			Подтверждение:
		</td>

		<td>
			<input type="password" name="%input_name%" value="" size="50" />
		</td>
	</tr>

END;


$FORMS['reflection_field_relation'] = <<<END
	<tr>
		<td>
			%title%:
		</td>

		<td>
			<select name="%input_name%">
				<option />
				%options%
			</select>
		</td>
	</tr>

END;

$FORMS['reflection_field_img_file'] = <<<END

	<tr>
		<td>
			%title%:
		</td>

		<td>
			<table width="100%">
				<tr>
					<td>
						<input type="file" name="%input_name%" />
					</td>

					<td>
						%data getPropertyOfObject(%object_id%, '%name%', 'avatar')%
					</td>
				</tr>
			</table>
		</td>
	</tr>


END;

?>