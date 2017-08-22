<?php
$FORMS = array();
	
$FORMS['price_block'] = <<<END
%price-original%
%price-actual%
END;

$FORMS['price_original'] = <<<END
<span class="product__old-price">
    <strike>%prefix%&nbsp;%original%</strike>
</span>
END;

$FORMS['price_actual'] = <<<END
<span class="product__new-price">%prefix%&nbsp;%actual% &nbsp;%suffix%</span>
END;

?>