<?php

$FORMS = Array();

$FORMS['menu_block_level1'] = <<<END
<ul class="footer__list footer__list--small">
%lines%
</ul>

END;

$FORMS['menu_line_level1'] = <<<END
 <li class="footer__item">
<a href="%link%" class="footer__link">%text%</a>
</li>
END;

$FORMS['menu_line_level1_a'] = <<<END
 <li class="footer__item">
<a href="%link%" class="footer__link">%text%</a>
</li>

END;
?>