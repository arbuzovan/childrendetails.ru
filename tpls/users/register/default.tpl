<?php

$FORMS = Array();

$FORMS['registrate_block'] = <<<REGISTRATE
%system listErrorMessages()%
<form action="%pre_lang%/users/registrate_do/" method="post" enctype="multipart/form-data">
    <div class="personal-register">
        <div class="personal-register__form">
            <form action="" class="form">
                <div class="form__row personal-register__form-row">
                    <div class="form__large">
                        <input type="text" class="input form__input" placeholder="Логин" name="login" />
                    </div>
                    <div class="form__col">
                        <input type="text" class="input form__input" name="email" placeholder="Email" />
                    </div>
                </div>
                <div class="form__row personal-register__form-row">
                    <div class="form__large">
                        <input type="text" type="password" name="password" class="input form__input" placeholder="Пароль">
                    </div>
                    <div class="form__col">
                        <input type="text" type="password" name="password_confirm" class="input form__input" placeholder="Подтверждение пароля">
                    </div>
                </div>

                <div class="form__row personal-register__form-row">
                    <div class="form__large">
                        %system captcha()%
                        <!-- this field determines what template will be used for e-mail sending -->
                        <input type="hidden" name="template" value="default" />

                        <script type="text/javascript">
                                restoreFormData(document.getElementById('registrate'));
                        </script>
                    
                    </div>
                </div>
                <div class="form__row personal-register__form-row">
                        <div class="form__large">
                        
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
                                    <input type="submit" class="btn btn__black  bold" value="Зарегистрироваться">
                                </span>
                            </div>
                        </div>
                </div>
        </div>
    </div>
</form>

REGISTRATE;



$FORMS['settings_block'] = <<<REGISTRATE

<form action="%pre_lang%/users/settings_do/" method="post" enctype="multipart/form-data">
    <div class="personal-settings">
        <div class="personal-settings__form">
            <div class="form__row personal-settings__form-row">
                <div class="form__col">
                    <input type="text" disabled="disabled" value="%login%" class="input form__input" placeholder="Логин" name="login" />
                </div>
                
                <div class="form__col">
                    <input type="text" class="input form__input" placeholder="E-mail" name="email" value="%e-mail%" />
                </div>
            </div>
            
            <div class="form__row personal-settings__form-row">
                <div class="form__col">
                    <input type="password" class="input form__input" name="password" value="" placeholder="Пароль" />
                </div>
                
                <div class="form__col">
                    <input type="password" class="input form__input" name="password_confirm" value="" placeholder="Подтвердите пароль" />
                </div>
            </div>
            
            <div class="form__row personal-settings__form-row">
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
                            <input type="submit" class="btn btn__black  bold" value="Сохранить изменения">
                        </span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</form>
REGISTRATE;


$FORMS['registrate_done_block'] = <<<END
Регистрация прошла успешно. На ваш e-mail отправлено письмо с инструкциями по активации аккаунта.

END;

$FORMS['registrate_done_block_without_activation'] = <<<END
Регистрация прошла успешно.

END;

$FORMS['registrate_done_block_error'] = <<<END
Регистрация завершилась неудачей. Проверьте правильность заполнения всех полей.

END;

$FORMS['registrate_done_block_user_exists'] = <<<END
Пользователь с таким именем уже существует. Попробуйте выбрать другое.

END;


$FORMS['activate_block'] = <<<END
<p>Аккаунт активирован.</p>

END;

$FORMS['mail_registrated_subject'] = "Регистрация на UMI.CMS Demo Site";

$FORMS['activate_block_failed'] = <<<END
<p>Неверный код активации.</p>

END;


$FORMS['mail_registrated'] = <<<MAIL

	<p>
		Здравствуйте, %lname% %fname% %father_name%, <br />
		Вы зарегистрировались на сайте <a href="http://%domain%">%domain%</a>.
	</p>


	<p>
		Логин: %login%<br />
		Пароль: %password%
	</p>


	<p>
		<div class="notice">
			Чтобы активировать Ваш аккаунт, необходимо перейти по ссылке, либо скопировать ее в адресную строку браузера:<br />
			<a href="%activate_link%">%activate_link%</a>
		</div>
	</p>

MAIL;

$FORMS['mail_admin_registrated'] = <<<END
<p>Зарегистрировался новый пользователь "%login%".</p>
END;
$FORMS['mail_admin_registrated_subject'] = "Зарегистрировался новый пользователь";


?>
