<?php

/**
 * Данный скрипт меняет баховый тип заддынх страниц. Сделан для смены некорректно заведнных в структуре сайта страниц.
 * Вместо категорий страницы были заведены как объекты каталога
 */
    define("CURRENT_WORKING_DIR", str_replace("\\", "/", $dirname = dirname(__FILE__)));
    require CURRENT_WORKING_DIR . '/libs/root-src/standalone.php';
    header("Content-Type: text/html; charset=utf-8");
    
    $oCollection = umiObjectsCollection::getInstance();
    $hierarchy = umiHierarchy::getInstance();
    $typesCollection = umiObjectTypesCollection::getInstance();
    $categoryTypeId = $typesCollection->getTypeIdByHierarchyTypeName('catalog', 'category');
    echo '$categoryTypeId = '.$categoryTypeId.'<br>';
    $objectTypeId = $typesCollection->getTypeIdByHierarchyTypeName('catalog', 'object');
    echo '$objectTypeId = '.$objectTypeId.'<br>';
    
    $hierchyElemnetIdsToChange = array();   // Массив ID для изменения
    $hierchyElemnetIdsToChange[] = 57;
    
    foreach ($hierchyElemnetIdsToChange as $hierarchyElementId) {
        $page = $hierarchy->getElement($hierarchyElementId);
        if($page instanceof umiHierarchyElement){
            echo $page->name.' - '.$page->getObjectTypeId()."<br>";
        }
    }
    
?>