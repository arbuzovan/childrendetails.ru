%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0)%

        <div class="rubric">
            <div class="rubric__list">

                %catalog getCategoryList('default', %id%')%

            </div>
        </div>

        <section class="info-text">
            <h1 class="title info-text__title"><span class="title__pos">%h1%</span></h1>

            %descr%

        </section>

    </div>
</div>

%system getOuterContent('./tpls/content/footer.tpl')%