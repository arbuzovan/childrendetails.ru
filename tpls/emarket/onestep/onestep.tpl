<?php

/* Страница оформления заказа в один шаг */

$FORMS = array();

$FORMS['purchasing_one_step'] = <<<END

%onestep%

<div class="ordering">
    <div class="ordering__wrapper">
        <div class="ordering__right">
            %emarket cart('onestep')%
        </div>
        <div class="ordering__left">
            <div class="ordering__form">
                <form action="" class="form">
                    <div class="ordering__form-contact">
                        <span class="ordering__form-title">Покупатель</span>
                        <span class="form__item">
                            <input type="text" class="input form__input" placeholder="Ваше имя">
                        </span>
                        <span class="form__item">
                            <input type="email" class="input form__input" placeholder="Ваш email">
                        </span>
                        <span class="form__item">
                            <input type="tel" class="input form__input" placeholder="Ваш телефон">
                        </span>
                    </div> 

                    <div class="ordering__form-address">
                        <span class="ordering__form-title">Адрес доставки</span>
                        <div class="form__inner">
                            <div class="form__large">
                                <input type="text" class="input form__input" placeholder="Город">
                            </div>
                            <div class="form__small">
                                <input type="text" class="input form__input" placeholder="Индекс">
                            </div>
                        </div>
                        <div class="form__inner">
                            <div class="form__large">
                                <input type="text" class="input form__input" placeholder="Улица">
                            </div>
                            <div class="form__small">
                                <input type="text" class="input form__input" placeholder="Дом/квартира">
                            </div>
                        </div>
                    </div>
                    <div class="ordering__form-note">
                        <span class="ordering__form-title">Примечание к заказу</span>
                        <textarea name="" id="" cols="30" rows="10" class="textarea form__textarea ordering__textarea" placeholder="Ваш коментарий"></textarea>
                    </div>
                    <div class="ordering__form-pay">
                        <span class="ordering__form-title">Способ оплаты</span>
                        <span class="form__item">
                            <input type="radio" name="radio" id="nal">
                            <label for="nal">Наличными при получении</label>
                        </span>
                        <span class="form__item">
                            <input type="radio" name="radio" id="visa">
                            <label for="visa">VISA / Mastercard</label>
                        </span>
                    </div>
                </form>
            </div>
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
            <a href="#" class="btn btn__black  bold">Оформить заказ</a>
        </span>
    </div>
</div>
END;

?>