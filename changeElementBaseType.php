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
    
    $categoryHierarchyTypeId = 50; //catalog-category
    $objectHierarchyTypeId = 52; //catalog-object
    
    $hierchyElemnetIdsToChange = array();   // Массив ID для изменения
    
    $hierchyElemnetIdsToChange[] = 57;  // Мальчики -> головные уборы
    
    $hierchyElemnetIdsToChange[] = 60;  // Сумки и рюкзаки -> Детсские рюкзаки
    $hierchyElemnetIdsToChange[] = 61;  //  Сумки и рюкзаки -> Сумки для мамы
    
    $hierchyElemnetIdsToChange[] = 66;  //  Домашняя коллекция -> Игровые коврики
    $hierchyElemnetIdsToChange[] = 67;  //  Домашняя коллекция -> Корзины для игрушек
    $hierchyElemnetIdsToChange[] = 68;  //  Домашняя коллекция -> Пледы
    $hierchyElemnetIdsToChange[] = 69;  //  Домашняя коллекция -> Полотенца
    $hierchyElemnetIdsToChange[] = 70;  //  Домашняя коллекция -> Постельное белье
    
    $hierchyElemnetIdsToChange[] = 79;  //  Коляски и аксессуары -> Вкладыши
    $hierchyElemnetIdsToChange[] = 80;  //  Коляски и аксессуары -> Дождевики
    $hierchyElemnetIdsToChange[] = 81;  //  Коляски и аксессуары -> Коляски
    $hierchyElemnetIdsToChange[] = 82;  //  Коляски и аксессуары -> Конверты
    $hierchyElemnetIdsToChange[] = 83;  //  Коляски и аксессуары -> Москитные сетки
    
    foreach ($hierchyElemnetIdsToChange as $hierarchyElementId) {
        $page = $hierarchy->getElement($hierarchyElementId);
        if($page instanceof umiHierarchyElement){
            echo $page->name.' - '.$page->getObjectTypeId()."<br>";
            echo $page->name.' - '.$page->getHierarchyType()."<br>";
            if($page->getObjectTypeId() == $objectTypeId){
                $object = $page->getObject();
                $object->setTypeId($categoryTypeId);
                $object->commit();
            }
            
            
            if($page->getHierarchyType()->getId() == $objectHierarchyTypeId){
                $page->setTypeId($categoryHierarchyTypeId);
                $page->commit();
            }
        }
    }
    
    echo 'ok';
    
?>