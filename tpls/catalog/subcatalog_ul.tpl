<?php

$FORMS = Array();

$FORMS['category'] = <<<END
%catalog getCategoryList('default', '%category_id%', 100, 1)%
END;


$FORMS['category_block'] = <<<END
<ul class="catalog__info-list">
    %lines%
<ul>
END;

$FORMS['category_block_line'] = <<<END
<li class="catalog__info-item">
    <a href="%link%" class="catalog__info-link">%h1%</a>
</li>
END;

$FORMS['category_block_empty'] = <<<END
    
END;

?>