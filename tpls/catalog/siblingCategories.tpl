<?php

/* Шаблон вывода всех одноуровневых категорий */

$FORMS = Array();

$FORMS['category_block'] = <<<END
<ul class="subsection-filter__list">
    %lines%
</ul>
END;

$FORMS['category_block_line'] = <<<END
<li class="subsection-filter__item">
    <a href="%category_link%" class="subsection-filter__link">%categry_name%</a>
</li>
END;

?>