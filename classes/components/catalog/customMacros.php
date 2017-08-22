<?php
	/**
	 * Класс пользовательских методов административной панели
	 */
	class CatalogCustomMacros {
		/**
		 * @var catalog $module
		 */
		public $module;
                
                /**
                 * Вычисляем кол-во подкатегорий переданной категории
                 * @param type $categoryId ID категории
                 * @param type $level уровень вложенности
                 * @return mixed
                 */
                public function getCountSubCategries($categoryId = false, $depth = 1){
                    
                    if(!$categoryId){
                        return false;
                    }
                    
                    $typesCollection = umiObjectTypesCollection::getInstance();
                    $hierarchy = umiHierarchy::getInstance(); 
                    $count = 0;
                    
                    $typeId = $typesCollection->getBaseType('catalog', 'category'); // ID типа категории
                    
                    $count = $hierarchy->getChildrenCount($categoryId, false, true, $depth, $typeId);
                    
                    return $count;
                }
                
                /**
                 * Вычисляем кол-во товаров в катеогрии включая вложенные
                 * @param type $categoryId ID категории
                 * @param type $level уровень вложенности
                 * @return mixed
                 */
                public function getCountItems($categoryId = false, $depth = 4) {
                    
                    if(!$categoryId){
                        return false;
                    }
                    
                    $typesCollection = umiObjectTypesCollection::getInstance();
                    $hierarchy = umiHierarchy::getInstance(); 
                    $count = 0;
                    
                    $typeId = $typesCollection->getBaseType('catalog', 'objects'); // ID типа категории
                    
                    $count = $hierarchy->getChildrenCount($categoryId, false, true, $depth, $typeId);
                    
                    return $count;
                }
                
                public function getCountItemsPostfix($count = 0) {
                    switch ($count) {
                        case 1:
                            return 'ние';

                            break;
                        case $count > 1 && $count < 5:
                            return 'ния';
                            break;
                        default:
                            return 'ий';
                            break;
                    }
                }
	}
?>