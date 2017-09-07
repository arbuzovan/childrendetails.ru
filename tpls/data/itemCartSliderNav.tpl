<?php
$FORMS = Array();

$FORMS['img_file'] = <<<END

    <div class="product__slider-navitem">
        %custom makeThumbnail(%src%, auto, 170,'default', 0, 0)%
    </div>
    
<!--
[Image File], %title% (%name%)<br />
Filename: %filename%;<br />
Filepath: %filepath%;<br />
Filepath: %src%;<br />
Size: %size%<br />
Extension: %ext%<br />
%width% %height%<br />
<img src="%src%" width="%width%" height="%height%" />
-->
END;



?>