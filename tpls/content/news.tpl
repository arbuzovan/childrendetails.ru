%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <div class="container">

        %core navibar('default',0)%

            %content%

    </div>
</div>

%system getOuterContent('./tpls/content/footer.tpl')%