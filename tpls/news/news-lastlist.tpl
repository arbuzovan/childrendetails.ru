<?php

$FORMS = Array();

$FORMS['lastlist_block'] = <<<END
<div style="clear:both">
<div class="slider">
<div class="slider__list js-slider">
%items%
</div>
</div>

END;

$FORMS['lastlist_item'] = <<<END
      <div class="slider__item">
        <a href="%link%">
            %custom makeThumbnail(%anons_pic%, 250, 250,'default', 0, 0, %header%)%
        </a>
</div>

END;

$FORMS['view'] = <<<END
          <div class="static-content">
                <h1 class="title"><span class="title__pos">%h1%</span></h1>
                <div style="text-align: justify;">
                    <img src="%publish_pic%" alt="img" style="float: left; margin-top: -13px;">
                    <span class="date">%system convertDate(%publish_time%, 'd.m.Y')%</span>
              %content%
                </div>
        
        
                %news lastlist('39','default',10,0)%
        
        
                <div class="slider">
                    <div class="slider__list js-slider">
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img01.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img02.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img03.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img04.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img01.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img02.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img03.jpg)"></a>
                        </div>
                        <div class="slider__item">
                            <a href="#" class="slider__link" style="background-image: url(/pic/slider_four_scroll/img04.jpg)"></a>
                        </div>
                    </div>
        
        
                </div>
            </div>
        


%news related_links(%id%)%



END;

$FORMS['related_block'] = <<<END
<p>Похожие новости:</p>
<ul>
	%related_links%
</ul>

END;

$FORMS['related_line'] = <<<END
<li>
	<a href="%link%"><b>%name%</b> (%system convertDate(%publish_time%, 'Y-m-d')%)</a>
</li>
END;



$FORMS['listlents_block'] = <<<END
<p>Рубрики новостей:</p>
<ul>
	%items%
</ul>

END;

$FORMS['listlents_item'] = <<<END
<li>
	<a href="%link%">%header%</a>
</li>

END;

$FORMS['listlents_block_empty'] = "";
?>