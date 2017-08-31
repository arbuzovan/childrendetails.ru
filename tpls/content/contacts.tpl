%system getOuterContent('./tpls/content/header.tpl')%

 <div class="wrapper">
    	
     <div class="container">
%core navibar('default',0)%
         
        
          <div class="contact">
    <h1 class="title"><span class="title__pos">%h1%</span></h1>
    
    <div class="contact__inf">
        <div class="contact-info">
    <div class="contact-info__list">
        <div class="contact-info__item">
            <span class="contact-info__title">Позвоните нам:</span>
            <a href="tel:%custom getSiteSetting('Шапка','phone_1')%" class="contact-info__tel">%custom getSiteSetting('Шапка','phone_1')%</a>
        </div>
        <div class="contact-info__item">
            <span class="contact-info__title">Мы в соц сетях:</span>
             <div class="social">
    <ul class="social__list">
            <li class="social__item">
                <a href="%custom getSiteSetting('Шапка','instagram')%" class="social__link social__link--black">
                    <svg class="social__icon social__icon--black social__icon--instagram">
                        <use xlink:href="#instagram"></use>
                    </svg>
                </a>
            </li>
        
    </ul>
</div>
        </div>
        <div class="contact-info__item">
            <span class="contact-info__title">Напишите нам:</span>
            <a href="mailto:%custom getSiteSetting('Шапка','email_1')%" class="contact-info__mail">%custom getSiteSetting('Шапка','email_1')%</a>
        </div>
    </div>
</div>
    </div>
    
    <div class="contact__map">
        <div id="map"></div>
    </div>
    
    <div class="writetous">
    <span class="title"><span class="title__pos">Напишите нам</span></span>
    <div class="writetous__form">
        <form action="" class="form contact_form" >
            <div class="form__row">
                <div class="form__col">
                    <span class="form__item">
                        <input type="text" id="contact__name" class="form__input input" placeholder="Ваше имя">
                    </span>
                    <span class="form__item">
                        <input type="text" id="contact__phone" class="form__input input" placeholder="Ваш телефон">
                    </span>
                </div>
                <div class="form__col">
                    <div class="form__item">
                        <textarea name="" id="contact__comment" id="" cols="30" rows="10" class="textarea form__textarea" placeholder="Ваше сообщение"></textarea>
                    </div>
                </div>
            </div>
            <button class="btn btn__black contact_form_btn" type="submit">Отправить</button>
        </form>
    </div>
</div>
    
    <section class="info-text">
        %content%
</section>
    
</div>
         
         
         
         
     </div>
 </div>



%system getOuterContent('./tpls/content/footer.tpl')%