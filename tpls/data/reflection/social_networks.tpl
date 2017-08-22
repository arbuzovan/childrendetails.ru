<?php

$FORMS = Array();

$FORMS['sc_networks_block']  = <<<END
<div class="social">
    <ul class="social__list">
        %items%
    </ul>
</div>
END;

$FORMS['sc_networks_item']  = <<<END
    <li class="social__item">
        <a href="%value%" class="social__link social__link--white">
            <svg class="social__icon social__icon--white social__icon--facebook">
                <use xlink:href="#%name%"></use>
            </svg>
        </a>
    </li>
END;

?>