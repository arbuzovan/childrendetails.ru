<?php
	class custom extends def_module {
		public function cms_callMethod($method_name, $args) {
			return call_user_func_array(Array($this, $method_name), $args);
		}

		public function __call($method, $args) {
			throw new publicException("Method " . get_class($this) . "::" . $method . " doesn't exist");
		}

                /**
                 * Создание миниатюры с сохранием пропорций и т.п. Работает лучше штатного
                 * @param string $path  Путь к картинке
                 * @param type $width   Ширина
                 * @param type $height  Высота
                 * @param string $template Шаблон вывода
                 * @param type $returnArrayOnly возвращать массив с данными поленного изображения
                 * @param type $fixHeight
                 * @param type $alt_text    Текст alt
                 * @return string
                 */
		public function makeThumbnail($path, $width, $height, $template = "default", $returnArrayOnly = false, $fixHeight = false, $alt_text = '') {

                    if(!$template){
                        $template = "default";
                    }

                    $thumbs_path = CURRENT_WORKING_DIR."/images/.tmb/";
                    $path = CURRENT_WORKING_DIR.$path;

                    $image = new umiImageFile($path);

                    $file_name = $image->getFileName();

                    $file_ext = $image->getExt();

                    $thumbPath = sha1($image->getDirName());

                    if (!is_dir($thumbs_path.$thumbPath)) {
                        mkdir($thumbs_path.$thumbPath, 0755);
                    }

                    $file_ext = strtolower($file_ext);


                    $allowedExts = Array('gif', 'jpeg', 'jpg', 'png', 'bmp');


                    if(!in_array($file_ext, $allowedExts)){
                        return "";
                    }

                    $file_name = substr($file_name, 0, (strlen($file_name) - (strlen($file_ext) + 1)) );
                    $file_name_new = $file_name . "_" . $width . "_" . $height . "." . $file_ext;
                    $path_new = $thumbs_path .$thumbPath."/". $file_name_new;

                    if(!file_exists($path_new) || filemtime($path_new) < filemtime($path)) {
                        if(file_exists($path_new)) {
                            unlink($path_new);
                        }
                        $width_src = $image->getWidth();
                        $height_src = $image->getHeight();

                        if($width_src <= $width && $height_src <= $height) {
                            copy($path, $path_new);
                            $real_width = $width;
                            $real_height = $height;
                        } else {

                        if ($width == "auto" && $height == "auto"){
                            $real_height = $height_src;
                            $real_width = $width_src;
                        }elseif ($width == "auto" || $height == "auto"){
                            if ($height == "auto"){
                                $real_width = (int) $width;
                                $real_height = (int) round($height_src * ($width / $width_src));
                            }elseif($width == "auto"){
                                $real_height = (int) $height;
                                $real_width = (int) round($width_src * ($height / $height_src));
                            }
                        }else{
                            //для фона
                            if($fixHeight){
                                $real_width = $width;// для макс заданного контура
                                $real_height = $height;// для макс заданного контура
                            }

                            //определяем размеры картинки
                            if($width_src > $height_src) {//горизонт
                                $real_width = $width;
                                $real_height = (int) round($height_src * ($width / $width_src));
                                if($real_height > (int) $height){
                                    $real_height = (int) $height;
                                    $real_width = (int) round($width_src * ($real_height / $height_src));
                                }
                            }
                            else{
                                $real_height = (int) $height;
                                $real_width = (int) round($width_src * ($height / $height_src));
                                if($real_width > $width){
                                    $real_width = (int) $width;
                                    $real_height = (int) round($height_src * ($real_width / $width_src));
                                }
                            }
                        }

                        if($fixHeight){
                            $thumb = imagecreatetruecolor($width, $height);//width для макс заданного контура
                        }
                        else{
                            $thumb = imagecreatetruecolor($real_width, $real_height);
                        }

                        if($image->getExt() == "gif") {
                            $source = imagecreatefromgif($path);

                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        } else if($image->getExt() == "png") {
                            $source = imagecreatefrompng($path);

                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        } else {
                            $source = imagecreatefromjpeg($path);
                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        }

                        //определяем координаты по середине полотна
                        $dstY = 0;
                        $dstX = 0;
                        if($fixHeight){
                            $dstX = round(($width - $real_width)/2);//для макс контура
                            $dstY = round(($height - $real_height)/2);
                        }

                        imagecopyresampled($thumb, $source, $dstX, $dstY, 0, 0, $real_width, $real_height, $width_src, $height_src);

                        if($image->getExt() == "png") {
                            imagepng($thumb, $path_new);
                        } else if($image->getExt() == "gif") {
                            imagegif($thumb, $path_new);
                        } else {
                            imagejpeg($thumb, $path_new, 75);
                        }
                    }
                    }

                    //Parsing
                    $value = new umiImageFile($path_new);

                    $arr = Array();
                    $arr['size'] = $value->getSize();
                    $arr['filename'] = $value->getFileName();
                    $arr['filepath'] = $value->getFilePath();
                    $arr['src'] = $value->getFilePath(true);
                    $arr['ext'] = $value->getExt();

                    $arr['width'] = $value->getWidth();
                    $arr['height'] = $value->getHeight();

                    $arr['template'] = $template;

                    $arr['alt_text'] = $alt_text;

                    if(cmsController::getInstance()->getCurrentMode() == "admin") {
                        $arr['src'] = str_replace("&", "&amp;", $arr['src']);
                    }

                    if(true == $returnArrayOnly) {
                        return $arr;
                    } else {

                        list($tpl) = def_module::loadTemplates("thumbs/{$template}.tpl", "image");
                        return def_module::parseTemplate($tpl, $arr);
                    }
		}

                /**
                 *
                 * @param type $settingName Имя блока настроек
                 * @param type $paramName   Имя параметра для вывода
                 * @param type $alltrim   Если передано true, то в выводе будут убраны все пробелы. Бывает нужно для телефонов и пр.
                 * @return boolean
                 * @throws publicException
                 */
                public function getSiteSetting($settingName = false, $paramName = false, $alltrim = false){
                    if(!$settingName || empty($settingName)){
                        return false;
                    }

                    if(!$paramName || empty($paramName)){
                        return false;
                    }

                    $mSettings = cmsController::getInstance()->getModule("umiSettings");
                    $settingsId = $mSettings->getId($settingName);

                    if(!$settingsId || empty($settingsId)){
                        throw new publicException("Ощибка получения настроек");
                    }

                    $value = umiObjectsCollection::getInstance()->getObject($settingsId)->getValue($paramName);

                    if($alltrim){
                        $value = str_replace(' ','',$value);
                    }

                    if(empty($value)){
                        return false;
                    }

                    return $value;
                }

                /**
                 * Функция берет блок с ссылками на соц сети и зависимости от их наличлия показывает иконки
                 * @param type $settingName
                 * @param type $groupName
                 * @param string $template
                 * @return boolean
                 * @throws publicException
                 */
                public function getSiteSocialsNeworkSetting($settingName = false, $groupName = false, $template = 'social_networks'){
                    if(!$settingName || empty($settingName)){
                        return false;
                    }

                    if(!$groupName || empty($groupName)){
                        return false;
                    }

                    $mSettings = cmsController::getInstance()->getModule("umiSettings");
                    $settingsId = $mSettings->getId($settingName);

                    if(!$settingsId || empty($settingsId)){
                        throw new publicException("Ощибка получения настроек");
                    }

                    $aIds = umiObjectsCollection::getInstance()->getObject($settingsId)->getPropGroupByName($groupName);

                    if($aIds == false || empty($aIds)){
                        return false;
                    }

                    if (!$template) {
                        $template = "social_networks";
                    }

                    list($template_block,$template_line) = def_module::loadTemplates("data/reflection/".$template, "sc_networks_block","sc_networks_item");
                    $block_arr = array();
                    $lines = array();

                    foreach ($aIds as $socialNetworkId){

                        $scFldObject = umiFieldsCollection::getInstance()->getField($socialNetworkId);

                        $fldName = $scFldObject->getName();
                        $settingValue = $this->getSiteSetting($settingName, $scFldObject->getName());

                        if(empty($settingValue)){
                            continue;
                        }

                        $line_arr = Array();
                        $line_arr['attribute:id'] = $socialNetworkId;
                        $line_arr['attribute:name'] = $fldName;
                        $line_arr['attribute:title'] = $scFldObject->getTitle();

                        $line_arr['attribute:value'] = $this->getSiteSetting($settingName, $scFldObject->getName());

                        $lines[] = def_module::parseTemplate($template_line, $line_arr, $socialNetworkId);
                    }

                    $block_arr['subnodes:items']  = $lines;
                    return def_module::parseTemplate($template_block, $block_arr);

                }

                /**
                 * Функция фозвращает массив позиций элемента
                 * @param type $string
                 * @param type $symbol
                 * @return boolean
                 */
                public function getSymbolPos($string = false, $symbol = false){
                    if(empty($string) || empty($symbol)){
                        return false;
                    }

                    $result = [];
                    $pos = 0;
                    while(($pos = strpos($string, $symbol, $pos+1))!==false) {
                        $result[] = $pos;
                    }

                    return $result;
                }

                /**
                 * Показ плейсхолдера из настроек
                 * @return boolean
                 */
                public function getPlaceholderFromSettings(){
                    $placeholderRawString = $this->getSiteSetting('шапка', 'placeholder');
                    
//  Первоначально была возможность указать на какие конкретнпо товары может вести ссылка.
//                    $placeholderItems = $this->getSiteSetting('шапка', 'placeholder_item_links');
//
//                    $itemsId = array();
//                    foreach ($placeholderItems as $itemId){
//                        $itemsId[] = $itemId;
//                    }

                    $openSymbolPositions = $this->getSymbolPos($placeholderRawString,'[');
                    $closeSymbolPositions = $this->getSymbolPos($placeholderRawString,']');

                    if(count($openSymbolPositions) != count($closeSymbolPositions)){
                        return false;
                    }

                    $replaceStringArray = array();
                    $index = 0;
                    foreach ($openSymbolPositions as $index => $value) {
                        $start_position = $openSymbolPositions[$index];
                        $stop_position = $closeSymbolPositions[$index];
                        $length = $stop_position - $start_position;
                        $replaceString = substr($placeholderRawString, $start_position, $length+1);
                        $replaceStringArray[] = $replaceString;
                        $index++;
                    }

                    $hierarchy = umiHierarchy::getInstance();

                    $paths = array();
                    foreach ($itemsId as $index => $itemElement){
                        $paths[] = $hierarchy->getPathById($itemElement->id);
                    }

                    foreach ($replaceStringArray as $index => $replaceString){;
                        $searchWord = str_replace('[', '',$replaceString);
                        $searchWord = str_replace(']', '',$searchWord);
                        $placeholderRawString = str_replace($replaceStringArray[$index], '<a href="/search/search_do/?search_string='.$searchWord.'&search-or-mode=0">'.$searchWord.'</a>',$placeholderRawString);
                    }

                    return $placeholderRawString;
                }
                
                /**
                 * Вывод на главной странице товаров, которые заданы в настройках
                 * @param type $limit
                 * @param type $template
                 * @return type
                 */
                public function getMainPageItemsFromSettings($limit = 3, $template = "mainPageItems") {
                    $main_page_items = $this->getSiteSetting('шапка', 'main_page_items');
                    
                    $umiHierarchy = umiHierarchy::getInstance();
                    
			list(
				$itemsTemplate,
				$emptyItemsTemplate,
				$itemTemplate
				) = def_module::loadTemplates(
				'catalog/' . $template,
				'objects_block',
				'objects_block_empty',
				'objects_block_line'
			);
                        
                    
			$result = array();
			$items = array();

			foreach ($main_page_items as $page) {

                            $pageId = $page->getId();
                            
                            $categroyId = $umiHierarchy->getParent($pageId);
                            $categryName = $umiHierarchy->getElement($categroyId)->getName();
                            $categryPath = $umiHierarchy->getPathById($categroyId);
                            
                            $item = array();

                            $item['attribute:id'] = $pageId;
                            $item['attribute:alt_name'] = $page->getAltName();
                            $item['attribute:price'] = $page->getValue('price');
                            $item['attribute:link'] = $umiHierarchy->getPathById($pageId);
                            $item['attribute:categry_name'] = $categryName;
                            $item['attribute:category_link'] = $categryPath;
                            $item['xlink:href'] ='upage://' . $pageId;
                            $item['node:text'] = $page->getName();
                            $items[] = def_module::parseTemplate($itemTemplate, $item, $pageId);
                            $umiHierarchy->unloadElement($pageId);
			}
                        
			$result['subnodes:lines'] = $items;

			return def_module::parseTemplate($itemsTemplate, $result);
                }
	}
