<?php
	/**
	 * Класс пользовательских макросов
	 */
	class UsersCustomMacros {
		/**
		 * @var users $module
		 */
		public $module;
                
                public function getProfilePageMenu($template = "default"){

			list($template_block) = users::loadTemplates(
				"users/profile/" . $template,
				"profile_page_menu"
			);
			$block_arr = []; 
                        return users::parseTemplate($template_block, $block_arr);
                }


                /**
                 * 
                 * @param type $param
                 */
                public function adress($template = 'default') {

			list($template_block) = users::loadTemplates(
				"users/profile/" . $template,
				"adress"
			);
			$block_arr = []; 
                        return users::parseTemplate($template_block, $block_arr);
                }
                
                public function orders($template = "default") {
			list($template_block) = users::loadTemplates(
				"users/profile/" . $template,
				"orders"
			);
			$block_arr = []; 
                        return users::parseTemplate($template_block, $block_arr);
                }
                
                /* Метод для  */
                public function orderState($template = "default") {

			list($template_block) = users::loadTemplates(
				"users/profile/" . $template,
				"order_state"
			);
			$block_arr = []; 
                        return users::parseTemplate($template_block, $block_arr);
                }
                
                /**
                 * Макрос проверяет выводит метку active для активной в данный момент страницы
                 * @param type $param
                 * @return string
                 */
                public function isActiveProfilePage($param){
                    $currUrlAddr = explode('/',getServer('REQUEST_URI'));
                    
                    $curr = $currUrlAddr[count($currUrlAddr)-2];
                    
                    if($curr == $param){
                        return 'active';
                    }else{
                        return $curr;
                    }
                }
                
                public function getAdressData() {
                    $id = getRequest('id');
                    $oAdress = umiObjectsCollection::getInstance()->getObject($id);
                    $answer = array();
                    $answer['id'] = $id;
                    $answer['city'] = $oAdress->getValue('city');
                    $answer['index'] = $oAdress->getValue('index');
                    $answer['street'] = $oAdress->getValue('street');
                    $answer['house'] = $oAdress->getValue('house');
                    
                    echo json_encode($answer);
                    
                    exit;
                }
                
                public function setAdressData(){
                    $oCollection = umiObjectsCollection::getInstance();
                    $permissions = permissionsCollection::getInstance();
                    $userId = $permissions->getUserId();
                    
                    $typesCollection = umiObjectTypesCollection::getInstance();
                    $typeId = $typesCollection->getBaseType('emarket', 'delivery_address');
                    $objectsCollection = umiObjectsCollection::getInstance();
                    
                    $userObject = $objectsCollection->getObject($userId);
                    
                    $id = getRequest('id');
                    $city = getRequest('city');
                    $index = getRequest('index');
                    $street = getRequest('street');
                    $house = getRequest('house');
                    
                    
                    if($id == 'new-address'){
                        $id = $objectsCollection->addObject("Adress for customer #".$userId, $typeId);
                        $userDeliveryAdresses = $userObject->delivery_addresses;
                        $userDeliveryAdresses[] = $id;
                        $userObject->setValue('delivery_addresses', $userDeliveryAdresses);
                    }
                    
                    $adressObject = $oCollection->getObject($id);
                    $adressObject->city = $city;
                    $adressObject->index = $index;
                    $adressObject->street = $street;
                    $adressObject->house = $house;

                    
                    $adressObject->commit();
                    
                    setcookie('ADRESS_ID',$id); // Ставим куку
                    
                    $answer = array();
                    $answer['status'] = 'ok';
                    
                    echo json_encode($answer);
                    exit;
                    
                }
	}
?>