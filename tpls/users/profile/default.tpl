<?php

$FORMS = Array();

$FORMS['profile_block'] = <<<END
            <div class="personal">
                <div class="page">
                    <aside class="page__bar">
                        <div class="bar">
                            <div class="bar__top">
                                <div class="bar__inner">
                                <span class="bar__inner-title">МОИ ДАННЫЕ</span>
                                <ul class="bar__inner-list">
                                    <li class="bar__inner-item"><a href="/users/profile/" class="bar__inner-link active">Личная информация</a></li>
                                    <li class="bar__inner-item"><a href="/emarket/customerDeliveryList/" class="bar__inner-link ">Адрес доставки</a></li>
                                </ul>
                            </div>
                            </div>
                            <div class="bar__bottom">
                                <div class="bar__inner">
                                    <span class="bar__inner-title">ЗАКАЗЫ</span>
                                    <ul class="bar__inner-list">
                                        <li class="bar__inner-item"><a href="#" class="bar__inner-link ">История заказов</a></li>
                                        <li class="bar__inner-item"><a href="#" class="bar__inner-link ">Состояние заказа</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </aside>
                    <div class="page__content">
                        <div class="personal-area">
                            <span class="personal-area__title">Личная информация</span>

                            <div class="personal-area__form">
                                <form action="" class="form">
                                    <div class="form__row personal-area__form-row">
                                        <div class="form__col">
                                            <input type="text" class="input form__input" placeholder="Ваше имя" value="%fname%">
                                        </div>
                                        <div class="form__col">
                                            <input type="email" class="input form__input" placeholder="E-mail" value="%e-mail%">
                                        </div>
                                    </div>
                                    <div class="form__row personal-area__form-row">
                                        <div class="form__col">
                                            <input type="tel" class="input form__input" placeholder="Ваш телефон" value="%phone%">
                                        </div>
                                    </div>
                                    <div class="form__row personal-area__form-row">
                                        <div class="form__col">
                                            <input type="password" class="input form__input" placeholder="Пароль">
                                        </div>
                                        <div class="form__col">
                                            <input type="password" class="input form__input" placeholder="Подтверждение пароля">
                                        </div>
                                    </div>
                                    <div class="form__button">
                                        <button class="btn btn__black personal-area__form-btn" type="submit">
                                            <svg class="btn__icon">
                                                <use xlink:href="#success"></use>
                                            </svg>
                                            <span class="btn__text">Сохранить</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           

END;

$FORMS['bad_user_block'] = <<<END

<p>Данного пользователя не существует</p>

END;

?>