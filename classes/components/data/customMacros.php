<?php
	/**
	 * Класс пользовательских макросов
	 */
	class DataCustomMacros {
		/**
		 * @var data $module
		 */
		public $module;
                
                /* Возврат отренедеренного макроса. Сделано для ajax в  tpl */
                public function getAjaxMacrosResult(){
                    $param = getRequest('macros');
                    echo cmsController::getInstance()->getCurrentTemplater()->parseInput($param);
                    exit;
                }
	}
?>