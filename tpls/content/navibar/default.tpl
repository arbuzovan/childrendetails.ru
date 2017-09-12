<?php

$FORMS = Array();

$FORMS['navibar'] = <<<NAVIBAR
      <div class="breadcrumbs ">
  <ul class="breadcrumbs__list">
  
          	
	    <li class="breadcrumbs__item">
	      <a href="/" class="breadcrumbs__link">Главная</a>
	    </li>
  
	%elements%
        %custom getCorePage()%
  </ul>
</div>
				
NAVIBAR;

$FORMS['navibar_empty'] = <<<NAVIBAR

NAVIBAR;

$FORMS['element'] = '<li class="breadcrumbs__item"><a href="%pre_lang%%link%" class="breadcrumbs__link">%text%</a> &nbsp;</li>';

$FORMS['element_active'] = '<li class="breadcrumbs__item"><span class="breadcrumbs__text">%text%</span></li>';

$FORMS['quantificator'] = " &nbsp;";

?>