%system getOuterContent('./tpls/content/header.tpl')%
<div class="wrapper">
   <div class="container">
      %core navibar('default',0)%
      <div class="catalog">
         <div class="catalog__list js-grid">
            <div class="catalog__sizer js-grid-sizer"></div>
                %catalog getCategoryList('catalog', '%id%', 100, 1)%
         </div>
      </div>
      <section class="info-text">
         <h1 class="title info-text__title"><span class="title__pos">Товары для детей</span></h1>
         %descr%
      </section>
   </div>
</div>
<div id="question" class="popup" style="display: none">
   <div class="popup__wrapper popup__wrapper-question">
      <span class="title"><span class="title__pos">Возникли вопросы?</span></span>
      <span class="popup__info">Мы перезвоним вам в ближайшее время</span>
      <span class="popup__subinfo">График работы понедельник - пятница с 09:00 - 18:00</span>
      <div class="popup__form">
         <form action="" class="form">
            <div class="form__item">
               <input type="text" class="input form__input" placeholder="Ваше имя">
            </div>
            <div class="form__item">
               <input type="tel" class="input form__input" placeholder="Ваш телефон">
            </div>
            <div class="form__item">
               <textarea name="" id="" cols="30" rows="10" class="textarea form__textarea" placeholder="Ваш комментарий"></textarea>
            </div>
            <a href="#" class="btn btn__black js-popups-true">Отправить</a>
         </form>
      </div>
   </div>
</div>
<div id="oneclick" class="popup" style="display: none">
   <div class="popup__wrapper popup__wrapper-oneclick">
      <span class="title"><span class="title__pos">Купить в  1 клик</span></span>
      <span class="popup__info">Мы перезвоним вам в ближайшее время</span>
      <span class="popup__subinfo">Для совершения заказа достаточно ввести <br>ваше имя и контактный телефон</span>
      <div class="popups__form">
         <form action="" class="form">
            <div class="form__item">
               <input type="text" class="input form__input" placeholder="Ваше имя">
            </div>
            <div class="form__item">
               <input type="tel" class="input form__input" placeholder="Ваш телефон">
            </div>
            <a href="#" class="btn btn__black js-popups-true">Отправить</a>
         </form>
      </div>
   </div>
</div>
<div id="carts" class="popup" style="display: none">
   <div class="popup__wrapper popup__wrapper-cart">
      <span class="title"><span class="title__pos">Товар добавлен в корзину</span></span>
      <span class="popup__info">Теперь в Вашей корзине</span>
      <span class="popup__info-cart"><a href="#" class="popup__info-link">2 ТОВАРА</a> на сумму 1600 РУБ</span>
      <div class="popup__product">
         <div class="popup__product-list">
            <div class="order__cart">
               <div class="order__cart-list">
                  <a href="#" class="order__cart-item">
                  <span class="order__cart-img">
                  <img src="pic/cart/img01.jpg" alt="img01">
                  </span>
                  <span class="order__cart-right">
                  <span class="order__cart-name">Комплект Babystyle Защитный оранжевый</span>
                  <span class="order__cart-price">8660 РУБ</span>
                  </span>
                  </a>
                  <a href="#" class="order__cart-item">
                  <span class="order__cart-img">
                  <img src="pic/cart/img02.jpg" alt="img02">
                  </span>
                  <span class="order__cart-right">
                  <span class="order__cart-name">Пустышка - Розовая пудра</span>
                  <span class="order__cart-price">1835 РУБ</span>
                  </span>
                  </a>
               </div>
            </div>
         </div>
      </div>
      <div class="popup__bottom">
         <span class="popup__col">
            <a href="#" class="btn btn__transparent btn__transparent--black bold">
               <svg class="btn__transparent-icon btn__transparent-icon--left">
                  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#arrow_left"></use>
               </svg>
               <span class="btn__transparent-text">Продолжить покупки</span>
            </a>
         </span>
         <span class="popup__col">
         <a href="#" class="btn btn__black  bold">Перейти в корзину</a>
         </span>
      </div>
   </div>
</div>
<div id="thank" class="popup" style="display: none">
   <div class="popup__wrapper popup__wrapper-thank">
      <span class="title"><span class="title__pos">Спасибо!</span></span>
      <span class="popup__info">Ваш запрос успешно отправлен</span>
      <span class="popup__subinfo">Мы перезвоним Вам в ближайшее время</span>
   </div>
</div>
%system getOuterContent('./tpls/content/footer.tpl')%