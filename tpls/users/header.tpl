<?php

$FORMS = Array();

$FORMS['login'] = <<<END
<div class="header__top-user">
    <ul class="header__user-list">
        <li class="header__user-item">
            <a href="#login" class="js-popup header__user-link">Вход</a>
        </li>
        <li class="header__user-item">
            <a href="/users/registrate/" class="header__user-link">Регистрация</a>
        </li>
    </ul>
</div>
END;


$FORMS['logged'] = <<<END
<div class="header__top-user">
    <ul class="header__user-list">
        <li class="header__user-item">
            <a href="%pre_lang%/users/profile/">%user_name%</a>
        </li>
        <li class="header__user-item">
            <a href="%pre_lang%/users/logout/">Выйти</a>
        </li>
    </ul>
</div>

END;
?>