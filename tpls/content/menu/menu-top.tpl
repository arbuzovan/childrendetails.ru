<?php

$FORMS = Array();

$FORMS['menu_block_level1'] = <<<END
<ul class="nav__list">
%lines%
</ul>

END;

$FORMS['menu_line_level1'] = <<<END
<li class="nav__list-item">
<a href="%link%" class="nav__link ">%text%</a>
</li>
END;

$FORMS['menu_line_level1_a'] = <<<END
<li class="nav__list-item">
<a href="%link%" class="nav__link ">%text%</a>
</li>

END;


?>