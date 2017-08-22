%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0)%

        <div class="product">
            <h1 class="title"><span class="title__pos">%h1%</span></h1>

            %catalog viewObject(%id%, 'default')%

        </div>
    </div>
</div>
%system getOuterContent('./tpls/content/footer.tpl')%