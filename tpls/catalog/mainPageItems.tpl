<?php

$FORMS = Array();



$FORMS['objects_block'] = <<<END
%lines%
END;


$FORMS['objects_block_line'] = <<<END

<div class="catalog-subsection__item">
   <div class="slider-four__item">
      <div class="slider-four__holder">
        <a href="%link%" class="slider-four__img">
            %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 270, 270,'default', 0, 0, %h1%)%
        </a>
        <a href="%link%" class="slider-four__title js-height">%h1%</a>
        <a href="%category_link%" class="slider-four__category ">%categry_name%</a>
        <span class="slider-four__price">
            <span class="slider-four__price-now">%emarket price(%id%)%</span>
        </span>
      </div>
   </div>
</div>
END;

?>