<?php
$FORMS = Array();

$FORMS['img_file'] = <<<END

    <div class="product__slider-item">
        <a href="%src%" class="product__slider-link">
            %custom makeThumbnail(%src%, 570, 570,'default', 0, 0, '%h1%')%
        </a>
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