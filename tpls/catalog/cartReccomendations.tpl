<?php

$FORMS = Array();

$FORMS['category'] = <<<END
%catalog getObjectsList('default', '%category_id%')%
END;


$FORMS['category_block'] = <<<END

%lines%

END;

$FORMS['objects_block'] = <<<END
<div class="slider-four__list js-slider">
    %lines%
</div>
END;


$FORMS['objects_block_line'] = <<<END
   <div class="slider-four__item">
      <div class="slider-four__holder">
        <a href="%link%" class="slider-four__img">
            %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 270, 270,'default', 0, 0, %h1%)%
        </a>
        <a href="%link%" class="slider-four__title js-height">%h1%</a>
        <a href="%link%" class="slider-four__category ">%title%</a>
        <span class="slider-four__price">
            <span class="slider-four__price-now">%emarket price(%id%)%</span>
        </span>
      </div>
   </div>
END;

?>