<?php

$FORMS = Array();

$FORMS['block'] = <<<END
%core navibar('default',0)%
<div class="error-page">
   <div class="error-page__img">
      <img src="/img/404.png" alt="404">
   </div>
   <div class="error-page__bottom">
      <div class="error-page__left">
         <a href="javascript:window.history.back()" class="btn btn__transparent error-page__btn bold">
            <svg class="btn__transparent-icon btn__transparent-icon--left">
               <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow_left"></use>
            </svg>
            <span class="btn__transparent-text">Вернуться назад</span>
         </a>
      </div>
      <div class="error-page__right">
         <a href="/" class="btn btn__black error-page__btn error-page__btn--black bold">перейти на главную</a>
      </div>
   </div>
</div>
END;

?>