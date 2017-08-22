<?php

$FORMS = Array();

$FORMS['menu_block_level1'] = <<<END

<ul class="footer__list">
%lines%
</ul>


END;

$FORMS['menu_line_level1'] = <<<END
 <li class="footer__item">
<a href="%link%" class="footer__link">%text%</a>
 %sub_menu%
</li>
END;

$FORMS['menu_line_level1_a'] = <<<END
 <li class="footer__item">
<a href="%link%" class="footer__link">%text%</a>
 %sub_menu%
</li>
END;


$FORMS['menu_block_level2'] = <<<END

<ul class="footer__sublist">
  %lines%
 </ul>

END;

$FORMS['menu_line_level2'] = <<<END
 <li class="footer__subitem"><a href="%link%" class="footer__sublink">%text%</a></li>
END;

$FORMS['menu_line_level2_a'] = <<<END
 <li class="footer__subitem"><a href="%link%" class="footer__sublink">%text%</a></li>

END;
?>