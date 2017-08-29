<?php

$FORMS = Array();

$FORMS['login'] = <<<END

<form action="%pre_lang%/users/login_do/" method="post" enctype="multipart/form-data">
    <div class="personal-login">
        <div class="personal-login__form">
            <form action="" class="form">
                <div class="form__row personal-login__form-row">
                    <div class="form__col">
                        <input type="text" id="login" name="login" class="input form__input" placeholder="Логин" name="login" />
                    </div>
                    <div class="form__col">
                        <input type="password" id="password" name="password" class="input form__input" placeholder="Пароль" />
                    </div>
                </div>

                <div class="form__row personal-login__form-row">
                        <div class="form__large">
                            <input style="display:none;" type="hidden" name="from_page" value="%from_page%" />                    
                        </div>
                        <div class="form__col">
                            <div class="ordering__bottom">
                                <span class="ordering__btn-true">
                                    <a href="#" class="ordering__btn-true">
                                        <svg class="btn__transparent-icon btn__transparent-icon--left">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#success"></use>
                                        </svg>
                                    </a>
                                </span>
                                <span class="ordering__btn-true">
                                    <input type="submit" class="btn btn__black  bold" value="%users_auth_enter%">
                                </span>
                            </div>
                        </div>
                </div>
        </div>
    </div>
</form>


<form action="%pre_lang%/users/login_do/" method="post">
<div>Если Вы еще не зарегистрированы на сайте, Вы можете <a href="%pre_lang%/users/registrate/" class="sub">зарегистрироваться</a></div>

<div>Если Вы забыли пароль, Вы можете <a href="%pre_lang%/users/forget/" class="sub">воспользоваться сервисом восстановления пароля</a></div>

END;


$FORMS['logged'] = <<<END
<div>
    %users_welcome% %user_name%
</div>

<div>
    <a href="%pre_lang%/users/logout/">Выйти</a><br />
    <a href="%pre_lang%/users/settings/">Перейти в персональный раздел</a>
</div>

END;
?>