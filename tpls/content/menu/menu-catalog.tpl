<?php

$FORMS = Array();

$FORMS['menu_block_level1'] = <<<END
<nav class="header__navigate">
<div class="navigate">
<ul class="navigate__list">
%lines%
</ul>
</div>
</nav>

END;

$FORMS['menu_line_level1'] = <<<END
<li class="navigate__list-item js-dropdown">
<a href="%link%" class="navigate__link ">%text%</a>
 %sub_menu%
</li>
END;

$FORMS['menu_line_level1_a'] = <<<END
<li class="navigate__list-item js-dropdown">
<a href="%link%" class="navigate__link active">%text%</a>
 %sub_menu%
</li>
END;


$FORMS['menu_block_level2'] = <<<END

<div class="navigate__drop js-subnav">

 <ul class="navigate__sublist">
  %lines%
 </ul>
 
 </div>
END;

$FORMS['menu_line_level2'] = <<<END
<li class="navigate__subitem"><a href="%link%" class="navigate__sublink">%text%</a></li>
END;

$FORMS['menu_line_level2_a'] = <<<END
<li class="navigate__subitem"><a href="%link%" class="navigate__sublink">%text%</a></li>
 %sub_menu%
END;



?>