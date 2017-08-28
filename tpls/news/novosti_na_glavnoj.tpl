<?php

$FORMS = Array();

$FORMS['lastlist_block'] = <<<END
<div class="news-prev">
    <span class="title"><span class="title__pos">Новости</span></span>
    <div class="news-prev__list">
    
        %items%
    
        <div class="news-prev__item">
            %custom getSiteSetting('Шапка','instagram_widget')%
        </div>
        
    </div>
    <a href="/news/" class="btn btn--grey news-prev__btn" class="btn btn--grey news-prev__btn">Все новости</a>
</div>
END;

$FORMS['lastlist_item'] = <<<END
    <div class="news-prev__item">
        <a href="%link%" class="news-prev__link">
            <span class="news-prev__top">
                %custom makeThumbnail(%anons_pic%, 270, 235,'default', 0, 0, %header%)%
            </span>
            <span class="news-prev__bottom">
                <span class="news-prev__date">%system convertDate(%publish_time%, 'd.m.Y')%</span>
                <span class="news-prev__title js-height">%header%</span>
            </span>
        </a>
    </div>
END;


$FORMS['listlents_block_empty'] = "";
?>