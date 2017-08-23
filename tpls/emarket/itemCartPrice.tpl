<?php
$FORMS = array();
	
$FORMS['price_block'] = <<<END
<span class="slider-four__price">
    %price-original%
    %price-actual%
</span>
END;

$FORMS['price_original'] = <<<END
    <span class="slider-four__price-old">%prefix%&nbsp;%original%&nbsp;%suffix%</span>
END;

$FORMS['price_actual'] = <<<END
<span class="slider-four__price-now">%prefix%&nbsp;%actual% &nbsp;%suffix%</span>
END;

?>