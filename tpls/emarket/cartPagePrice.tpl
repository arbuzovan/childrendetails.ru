<?php
$FORMS = array();
	
$FORMS['price_block'] = <<<END
    %price-original%
    %price-actual%
END;

$FORMS['price_original'] = <<<END
    <div>%prefix%&nbsp;%original%&nbsp;%suffix%</div>
END;

$FORMS['price_actual'] = <<<END
<div>%prefix%&nbsp;%actual% &nbsp;%suffix%</div>
END;

?>