%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0)%


        <div class="catalog-subsection">
            <div class="subsection-filter subsection-filter--noradius">
                <div class="subsection-filter__nav ">

                    %catalog getSiblingCategories(%id%)%

                </div>
                <div class="subsection-filter__select subsection-filter__select--noradius">
                    <select name="cat_order" id="cat_order">
                        <option label="Цена (от низкой к высокой)" value='price_asc' %catalog isOrderedValue('price_asc')% >Цена (от низкой к высокой)</option>
                        <option label="Цена (от высокой к низкой)" value='price_desc' %catalog isOrderedValue('price_desc')% >Цена (от высокой к низкой)</option>
                        <option label="Алфавит (от А до Я)" value="name_asc" %catalog isOrderedValue('name_asc')% >Алфавит (от А до Я)</option>
                        <option label="Алфавит (от Я до А)" value="name_desc" %catalog isOrderedValue('name_desc')% >Алфавит (от Я до А)</option>
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