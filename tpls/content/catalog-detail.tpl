%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0,1,1)%

        <div class="product">
            
            <h1 class="title"><span class="title__pos">%header%</span></h1>

            %content%

        </div>
    </div>
</div>
%system getOuterContent('./tpls/content/footer.tpl')%