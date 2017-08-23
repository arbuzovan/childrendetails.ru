<?php
$FORMS = array();
	
$FORMS['price_block'] = <<<END
<span class="product__old-price">%price-original%</span>
<span class="product__new-price">%price-actual%</span>
END;

$FORMS['price_original'] = <<<END
    %prefix%&nbsp;%original%&nbsp;%suffix%
END;

$FORMS['price_actual'] = <<<END
    %prefix%&nbsp;%actual% &nbsp;%suffix%
END;

?>