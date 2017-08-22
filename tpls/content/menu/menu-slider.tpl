<?php

$FORMS = Array();


$FORMS['menu_block_level1'] = <<<END

%lines%

END;

$FORMS['menu_line_level1'] = <<<END

<div class="full-slider__item">
<a href="%data getProperty(%id%, 'ssylka', 'ssylka')%" class="full-slider__link">
<img src="%header_pic%" alt="%text%">
<div class="full-slider__text">
   %content%
</div>
</a>
</div>

END;

$FORMS['menu_line_level1_a'] = <<<END
<div class="full-slider__item">
<a href="%data getProperty(%id%, 'ssylka', 'ssylka')%" class="full-slider__link">
<img src="%header_pic%" alt="%text%">
<div class="full-slider__text">
   %content%
</div>
</a>
</div>

END;

?>