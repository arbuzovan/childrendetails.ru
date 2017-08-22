<?php
$FORMS = Array();
/* Multiple property blocks */

$FORMS['relation_mul_block'] = <<<END
[Relation multiple], %title% (%name%): %items%
END;

/* Multiple property item */

$FORMS['relation_mul_item'] = <<<END
%value%(%object_id%)%quant%
END;

/* Multiple property quant */
$FORMS['symlink_block'] = <<<END
<span class="product__title">Цветовая схема</span>
<ul class="product__color-list">
    %items%
</ul>
END;

$FORMS['symlink_item'] = <<<END
<li class="product__color-item">
    <!--a href="%link%">%value%(%id%, %object_id%)</a-->
    <a href="%link%" class="product__color-link active">
        %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 52, 52,'default', 0, 0, '%h1%')%
    </a>
</li>
END;

$FORMS['symlink_quant'] = <<<END
, 
END;


$FORMS['guide_block'] = <<<END
<select name="guide_%guide_id%">
%items%
</select>
END;

$FORMS['guide_block_empty'] = <<<END

END;

$FORMS['guide_block_line'] = <<<END
<option value="%id%">%text%</option>
END;


?>