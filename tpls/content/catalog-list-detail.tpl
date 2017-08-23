%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0)%


        <div class="catalog-subsection">
            <div class="subsection-filter subsection-filter--noradius">
                <div class="subsection-filter__nav ">


                    <ul class="subsection-filter__list">

                        <li class="subsection-filter__item">
                            <a href="#" class="subsection-filter__link active">держатели для пустышек</a>
                        </li>

                        <li class="subsection-filter__item">
                            <a href="#" class="subsection-filter__link ">перчатки и варешки</a>
                        </li>

                        <li class="subsection-filter__item">
                            <a href="#" class="subsection-filter__link ">нагрудники</a>
                        </li>

                        <li class="subsection-filter__item">
                            <a href="#" class="subsection-filter__link ">головные уборы</a>
                        </li>

                    </ul>

                </div>
                <div class="subsection-filter__select subsection-filter__select--noradius">
                    <select name="cat_order" id="cat_order">
                        <option label="Цена (от высокой к низкой)" value='price_desc'>Цена (от высокой к низкой)</option>
                        <option label="Цена (от низкой к высокой)" value='price_asc'>Цена (от низкой к высокой)</option>
                        <option label="Алфавит (от А до Я)" value="name_asc">Алфавит (от А до Я)</option>
                        <option label="Алфавит (от Я до А)" value="name_desc">Алфавит (от Я до А)</option>
                    </select>
                </div>
            </div>
            <div class="catalog-subsection__list">
                %catalog getSmartCatalog('default', %id%, 10, false, 1, %catalog getOrderFieldName()%, %catalog getisAscDirection()%)%
            </div>



        </div>


        <section class="info-text">
            <h1 class="title info-text__title"><span class="title__pos">%h1%</span></h1>

            %descr%

        </section>

    </div>
</div>

%system getOuterContent('./tpls/content/footer.tpl')%