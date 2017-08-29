<?php
$FORMS = Array();

$FORMS['search_block'] = <<<END

<!--p>
	%search_founded_total1% %total% %search_founded_total%.
</p-->

<!--ul>
%lines%
</ul-->
%lines%

%system numpages(%total%, %per_page%)%

END;

$FORMS['search_block_line'] = <<<END
<div class="catalog-subsection__item">
   <div class="slider-four__item">
      <div class="slider-four__holder">
        <a href="%link%" class="slider-four__img">
            %custom makeThumbnail(%data getProperty(%id%, 'izobrazhenie_1', 'izobrazhenie')%, 270, 270,'default', 0, 0, %h1%)%
        </a>
        <a href="%link%" class="slider-four__title js-height">%h1%</a>
        <a href="%content get_page_url(%catalog getParentCategoryId(%id%)%)%" class="slider-four__category ">%data getProperty(%catalog getParentCategoryId(%id%)%,'h1')%</a>
        <span class="slider-four__price">
            <span class="slider-four__price-now">%emarket price(%id%)%</span>
        </span>
      </div>
   </div>
</div>

END;

$FORMS['search_empty_result'] = <<<END
%search insert_form()%
<p>
	Извините. По данному запросу ничего не найдено.
</p>

END;

$FORMS['search_form'] = <<<END
<form method="get" action="%pre_lang%/search/search_do/" class="form form--search js-placeholder-container">
    <span class="form__search">
        <input type="text" class="input form__search-input js-search" name="search_string" />
        <input type="hidden" name="search_types" value="52" />
        <div class="placeholderDiv js-placeholderDiv">
            %custom getPlaceholderFromSettings()%
        </div>
        <button type="submit" class="form__btn-icon">
            <svg class="form__icon">
            <use xlink:href="#search"></use>
            </svg>
        </button>
    </span>
</form>

END;

?>