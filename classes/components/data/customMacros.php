<?php
	/**
	 * Класс пользовательских макросов
	 */
	class DataCustomMacros {
		/**
		 * @var data $module
		 */
		public $module;

                public function getAjaxMacrosResult(){
                    $param = getRequest('macros');
                    echo cmsController::getInstance()->getCurrentTemplater()->parseInput($param);
                    exit;
                }
	}
?>