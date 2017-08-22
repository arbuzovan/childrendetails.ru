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
%system getOuterContent('./tpls/content/footer.tpl')%