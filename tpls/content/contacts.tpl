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
            <a href="tel:+79062355689" class="contact-info__tel">7 906 235 56 89</a>
        </div>
        <div class="contact-info__item">
            <span class="contact-info__title">Мы в соц сетях:</span>
             <div class="social">
    <ul class="social__list">
        
            <!-- <li class="social__item">
    <a href="#" class="social__link social__link--black">
        <svg class="social__icon social__icon--black social__icon--facebook">
            <use xlink:href="#facebook"></use>
        </svg>
    </a>
</li>
        
            <li class="social__item">
    <a href="#" class="social__link social__link--black">
        <svg class="social__icon social__icon--black social__icon--vk">
            <use xlink:href="#vk"></use>
        </svg>
    </a>
</li> -->
        
            <li class="social__item">
    <a href="https://www.instagram.com/childrendetails/" class="social__link social__link--black">
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
            <a href="mailto:&#105;&#110;&#102;&#111;&#064;&#099;&#104;&#105;&#108;&#100;&#114;&#101;&#110;&#100;&#101;&#116;&#097;&#105;&#108;&#115;&#046;&#114;&#117;" class="contact-info__mail">&#105;&#110;&#102;&#111;&#064;&#099;&#104;&#105;&#108;&#100;&#114;&#101;&#110;&#100;&#101;&#116;&#097;&#105;&#108;&#115;&#046;&#114;&#117;</a>
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
        <form action="" class="form">
            <div class="form__row">
                <div class="form__col">
                    <span class="form__item">
                        <input type="text" class="form__input input" placeholder="Ваше имя">
                    </span>
                    <span class="form__item">
                        <input type="text" class="form__input input" placeholder="Ваш телефон">
                    </span>
                </div>
                <div class="form__col">
                    <div class="form__item">
                        <textarea name="" id="" cols="30" rows="10" class="textarea form__textarea" placeholder="Ваше сообщение"></textarea>
                    </div>
                </div>
            </div>
            <button class="btn btn__black" type="submit">Отправить</button>
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