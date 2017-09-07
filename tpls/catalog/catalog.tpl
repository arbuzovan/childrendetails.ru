<?php

$FORMS = Array();

$FORMS['category'] = <<<END
%catalog getCategoryList('default', '%category_id%', 100, 1)%
END;


$FORMS['category_block'] = <<<END

%lines%

END;

$FORMS['category_block_line'] = <<<END

<div class="catalog__item js-grid-item">
    <div class="catalog__item-holder">
        <a href="%link%" class="catalog__image catalog__image--large">
            %custom makeThumbnail(%data getProperty(%id%, 'header_pic', 'izobrazhenie')%, 220, auto,'default', 0, 0, '%h1%')%
        </a>
        <div class="catalog__info">
                <a href="%link%" class="catalog__title">%h1%</a>
                %catalog getCategoryList('subcatalog_ul', '%id%', 100, 1)%
        </div>
    </div>
</div>

END;

$FORMS['category_block_empty'] = <<<END

END;

?>