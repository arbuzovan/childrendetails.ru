<?php

$FORMS = Array();

$FORMS['profile_page_menu'] = <<<END
<aside class="page__bar">
    <div class="bar">
        <div class="bar__top">
            <div class="bar__inner">
                <span class="bar__inner-title">МОИ ДАННЫЕ</span>
                <ul class="bar__inner-list">
                    <li class="bar__inner-item"><a href="/users/profile/" class="bar__inner-link %users isActiveProfilePage('profile')%">Личная информация</a></li>
                    <li class="bar__inner-item"><a href="/users/adress/" class="bar__inner-link %users isActiveProfilePage('adress')%">Адреса доставки</a></li>
                </ul>
            </div>
        </div>
        <div class="bar__bottom">
            <div class="bar__inner">
                <span class="bar__inner-title">ЗАКАЗЫ</span>
                <ul class="bar__inner-list">
                    <li class="bar__inner-item"><a href="/users/orders/" class="bar__inner-link %users isActiveProfilePage('orders')%">История заказов</a></li>
                    <li class="bar__inner-item"><a href="/users/orderState/" class="bar__inner-link %users isActiveProfilePage('orderState')%">Состояние заказа</a></li>
                </ul>
            </div>
        </div>
    </div>
</aside>
END;

$FORMS['profile_block'] = <<<END
            <div class="personal">
                <div class="page">
                    %users getProfilePageMenu()%
                    <div class="page__content">
                        <div class="personal-area">
                            %users settings()%
                            <!--div class="personal-area__form">
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
                            </div-->
                        </div>
                    </div>
                </div>
            </div>
END;

$FORMS['adress'] = <<<END
    <div class="personal">
        <div class="page">
            %users getProfilePageMenu()%
            <div class="page__content">
                <div class="personal-area">
                    <div class="personal-area__form">
                        %emarket customerDeliveryList('profile')%
                    </div>
                </div>
            </div>
        </div>
    </div>
END;

$FORMS['orders'] = <<<END
    <div class="personal">
        <div class="page">
            %users getProfilePageMenu()%
            <div class="page__content">
                <div class="personal-area">
                    <div class="personal-area__form">
                        %emarket ordersList()%
                    </div>
                </div>
            </div>
        </div>
    </div>
END;

$FORMS['order_state'] = <<<END
    <div class="personal">
        <div class="page">
            %users getProfilePageMenu()%
            <div class="page__content">
                <div class="personal-area">
                    <div class="personal-area__form">

                        <div class="form__row personal-settings__form-row">

                            <div class="form__col">
                                <input class="input form__input" id="orderNumber" placeholder="Номер заказа" name="orderNumber" value="" type="text">
                            </div>
                            <div class="form__col">
                                <span class="profile__orderState"></span>
                            </div>
                        </div>

                        <div class="form__row personal-settings__form-row">
                            <div class="form__col">
                                <div class="ordering__bottom">
                                    <span class="ordering__btn-true">
                                        <a href="#" class="ordering__btn-true">
                                            <svg class="btn__transparent-icon btn__transparent-icon--left">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#success"/>
                                            </svg>
                                        </a>
                                    </span>
                                    <span class="ordering__btn-true">
                                        <input class="btn btn__black  bold orderStateCheckBtn" value="Проверить" type="submit">
                                    </span>
                                </div>
                            </div>
                        </div>
                    
                    
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