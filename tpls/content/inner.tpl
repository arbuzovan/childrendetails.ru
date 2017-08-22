%system getOuterContent('./tpls/content/header.tpl')%

<div class="wrapper">
    <main class="content main-page">	
        <div class="static-content">
            <div class="container">

                %core navibar('default',0)%

                <h1 class="title"><span class="title__pos">%h1%</span></h1>

                %content%

            </div>
        </div>
    </main> 
</div>

%system getOuterContent('./tpls/content/footer.tpl')%