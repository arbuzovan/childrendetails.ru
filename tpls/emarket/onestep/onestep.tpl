<?php

/* Страница оформления заказа в один шаг */

$FORMS = array();

$FORMS['purchasing_one_step'] = <<<END
<form action="%pre_lang%/emarket/saveInfo/" method="post">
    <div class="ordering">
        <div class="ordering__wrapper">
            <div class="ordering__right">
                %emarket cart('onestep')%
            </div>
            <div class="ordering__left">
                %system listErrorMessages()%
                <div class="ordering__form">
                    %onestep%
                </div>
            </div>
            <div class="ordering__bottom">
                <span class="ordering__btn-back">
                    <a href="#" class="btn btn__transparent btn__transparent--black bold">
                        <svg class="btn__transparent-icon btn__transparent-icon--left">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow_left"></use>
                        </svg>
                        <span class="btn__transparent-text">Вернуться в корзину</span>
                    </a>
                </span>
                <span class="ordering__btn-true">
                    <input type="submit" class="btn btn__black  bold" value="Оформить заказ">
                </span>
            </div>
        </div>
    </div>
</form>
END;

?>