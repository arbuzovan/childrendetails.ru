<?php

$FORMS = Array();

$FORMS['lastlist_block'] = <<<END
       <div class="news">
             <h1 class="title"><span class="title__pos">%h1%</span></h1>

%items%
            
        </div>
%system numpages(%total%, %per_page%)%

END;

$FORMS['lastlist_item'] = <<<END
    <div class="news__item">
            <div class="news__image">
        <a href="%link%">
            %custom makeThumbnail(%anons_pic%, 470, 270,'default', 0, 0, %header%)%
        </a>
    </div>
            <div class="news__inf">
        <span class="news__date">%system convertDate(%publish_time%, 'd.m.Y')%</span>
        <span class="news__title">%h1%</span>
    %anons%
        <a href="%link%" class="news__link">
            <span class="news__link-text">подробнее</span>
            <svg class="news__icon news__icon--right">
                <use xlink:href="#arrow_right"></use>
            </svg>
        </a>
    </div>
</div>

END;

$FORMS['view'] = <<<END
          <div class="static-content">
                <h1 class="title"><span class="title__pos">%h1%</span></h1>
                <div  style="text-align: justify;">
                    <div class="news__image--left">
                        %custom makeThumbnail(%publish_pic%, 300, 300,'default', 0, 0, %header%)%
                    </div>
                    <span class="date">%system convertDate(%publish_time%, 'd.m.Y')%</span>
                    %content%
                </div>
                %news lastlist('39','news-lastlist',10,0)%
            </div>

 %data getProperty(%id%, 'ssylka_na_tovar', 'ssylka_na_tovar')%

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