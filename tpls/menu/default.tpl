<?php
$FORMS = Array();

$FORMS['menu_block_level1'] = <<<END
<div class="bar__inner">
    <span class="bar__inner-title">%id%</span>
    <ul class="bar__inner-list">
        %lines%
    </ul>
</div>
END;

$FORMS['menu_line_level1'] = <<<END

<li class="bar__inner-item"><a class="bar__inner-link" href="%link%"  umi:field-name="name">%text%</a></li>
END;

$FORMS['menu_line_level1_a'] = <<<END
<li class="bar__inner-item"><a class="bar__inner-link active" href="%link%"  umi:field-name="name">%text%</a></li>
END;

$FORMS['menu_block_level2'] = <<<END
<ul id="menu" >%lines%</ul>	
END;

$FORMS['menu_line_level2'] = <<<END
<li><a href="%link%"  umi:field-name="name">%text%</a>%separator%%sub_menu%</li>
END;

$FORMS['menu_line_level2_a'] = <<<END
<li class="active"><a href="%link%"  umi:field-name="name">%text%</a>%separator%%sub_menu%</li>
END;

$FORMS['menu_block_level3'] = <<<END
<ul id="menu" >%lines%</ul>		
END;

$FORMS['menu_line_level3'] = <<<END
<li><a href="%link%"  umi:field-name="name">%num% %text%</a>%sub_menu%</li>
END;

$FORMS['menu_line_level3_a'] = <<<END
<li class="active"><a href="%link%"  umi:field-name="name">%num% %text%</a>%sub_menu%</li>
END;

$FORMS['separator_level1'] = <<<END
END;

$FORMS['separator_last_level1'] = <<<END
 (последний разделитель первого уровня)
END;

$FORMS['separator_level2'] = <<<END
 (разделитель второго уровня)
END;

$FORMS['separator_last_level2'] = <<<END
 (последний разделитель второго уровня)
END;

$FORMS['menu_class_level1'] = <<<END
level1
END;

$FORMS['menu_class_level1_last'] = <<<END
level1_last
END;
?>