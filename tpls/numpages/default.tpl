<?php
	$FORMS = Array();

	$FORMS['pages_block'] = <<<END
     <div class="pagination">
    <ul class="pagination__list">
    
       <li class="pagination__item pagination__item--arrow">	
   
        <a href="/catalog/" class="pagination__link pagination__link--arrow">
                <svg class="pagination__icon pagination__icon--right">
                    <use xlink:href="#arrow_left"></use>
                </svg>
            </a>

        </li>
    
    
		%pages%
                
                
                       <li class="pagination__item pagination__item--arrow">
            <a href="#" class="pagination__link pagination__link--arrow">
                <svg class="pagination__icon pagination__icon--right">
                    <use xlink:href="#arrow_right"></use>
                </svg>
            </a>
        </li>
                
                </ul>
	</div>

END;



	$FORMS['pages_item'] = <<<END
	
           <li class="pagination__item"><a class="pagination__link " href="%link%"></a></li>
END;

	$FORMS['pages_item_a'] = <<<END
    <li class="pagination__item"><a class="pagination__link active" href="%link%"></a></li> 
END;

	$FORMS['pages_quant'] = <<<END
|
END;

	$FORMS['pages_block_empty'] = "";
?>