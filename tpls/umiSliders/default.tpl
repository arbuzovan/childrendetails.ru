<?php

$FORMS = Array();

$FORMS['slider'] = <<<END
%slides%
END;

$FORMS['slides'] = <<<END
%umiSlider SliderDataForTemplate(%id%)%
%scope%
END;

$FORMS['slide'] = <<<END
<u>Previous</u>
END;



$FORMS['next'] = <<<END
<a href="%link%" title="%h1%">Next</a>
END;

$FORMS['next_last'] = <<<END
<u>Next</u>
END;

?>