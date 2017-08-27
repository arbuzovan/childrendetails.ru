<?php

$FORMS = Array();

$FORMS['reflection_block'] = <<<END
    %groups%
END;

$FORMS['reflection_group'] = <<<END
<div class="ordering__form-contact">
    <span class="ordering__form-title">%title%</span>
    %fields%
</div>
END;


$FORMS['reflection_field_string'] = <<<END
    <span class="form__item">
        <input class="input form__input" name="%input_name%" value="%value%" placeholder="%title%">
    </span>
END;

?>