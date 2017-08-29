<?php

$FORMS = [];

$FORMS['captcha'] = <<<CAPTCHA
    <div class="form__large">
        <span class="captcha__title">Введите текст на картинке</span>
	<img class="captcha__img" src="/captcha.php" />
	<input class="input form__input" type="text" name="captcha" />
    </div>
CAPTCHA;

$FORMS['recaptcha'] = <<<RECAPTCHA
    <script src='%recaptcha-url%?hl=ru'></script>
    <div class="%recaptcha-class%" data-sitekey="%recaptcha-sitekey%"></div>
RECAPTCHA;
