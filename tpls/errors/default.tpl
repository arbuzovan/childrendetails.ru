<?php
$FORMS = Array();

$FORMS['errors_block'] = <<<END
<section class="error__wrapper">
    %items%
</section>
END;

$FORMS['errors_block_line'] = <<<END
    <div class="error">
        %message%
    </div>
END;

?>