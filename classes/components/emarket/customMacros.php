<?php
	/**
	 * Класс пользовательских макросов
	 */
	class EmarketCustomMacros {
		/**
		 * @var emarket $module
		 */
		public $module;

                public function deliveryListCustom($template = 'onestep') {
                        $module = $this->module;
                        $order = $module->getBasketOrder(!in_array($mode, array('put', 'remove')));
                        list($tpl_block, $tpl_item_free, $tpl_item_priced) = def_module::loadTemplates(
                                "emarket/delivery/{$template}.tpl",
                                'delivery_block',
                                'delivery_item_free',
                                'delivery_item_priced'
                        );

                        if (!isset($_SESSION['emarket'])) $_SESSION['emarket'] = array();
                        if (!isset($_SESSION['emarket']['delivery'])) $_SESSION['emarket']['delivery'] = array();

                        $deliveryIds = delivery::getList();
                        $items_arr = array();
                        $currentDeliveryId = $order->getValue('delivery_id');

                        foreach($deliveryIds as $delivery) {
                                $delivery = delivery::get($delivery);
                                if ($delivery->validate($order) == false) {
                                        continue;
                                }

                                $deliveryObject = $delivery->getObject();
                                $deliveryPrice  = $delivery->getDeliveryPrice($order);
                                $_SESSION['emarket']['delivery'][$delivery->id] = (float)$deliveryPrice;

                                $item_arr = array(
                                        'attribute:id'		=> $deliveryObject->id,
                                        'attribute:name'	=> $deliveryObject->name,
                                        'attribute:price'	=> $deliveryPrice.'',
                                        'xlink:href'		=> $deliveryObject->xlink
                                );

                                if ($delivery->id == $currentDeliveryId) $item_arr['attribute:active'] = 'active';

                                $tpl_item = $deliveryPrice ? $tpl_item_priced : $tpl_item_free;
                                $items_arr[] = def_module::parseTemplate($tpl_item, $item_arr, false, $deliveryObject->id);
                        }

                        if ($tpl_block) {
                                return def_module::parseTemplate($tpl_block, array('items' => $items_arr));
                        } else {
                                return array('items' => array('nodes:item'	=> $items_arr));
                        }

                }
         
                
		/**
		 * Возвращает данные всех этапов оформления заказа для построения единой формы
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws publicException
		 */
		public function one_step_order($template = 'onestep'){
			/**
			 * @var emarket|EmarketPurchasingOneStep|EmarketPurchasingStagesSteps $module
			 */
			$module = $this->module;
			list($purchasing_one_step) = emarket::loadTemplates(
				"emarket/onestep/{$template}.tpl",
				'purchasing_one_step'
			);
                                
			$order = $module->getBasketOrder();
                        
			if ($order->isEmpty()) {
				throw new publicException('%error-market-empty-basket%');
			}

			$result = array();

			if (!permissionsCollection::getInstance()->isAuth()){
				$result['onestep']['customer'] = $module->personalInfo($template);
				if (emarket::isXSLTResultMode()) {
					$result['onestep']['customer']['@id'] = customer::get()->getId();
				}
			}

			if ($module->isDeliveryAvailable()) {
                            
                            //$result['onestep']['delivery'] = $module->customerDeliveryList($template);
                            $result['onestep']['delivery_choose'] = $module->renderDeliveryList($order, $template);
			}
                        
                        $result['onestep']['ajax_adress_block'] = '<div class="ordering__form-address"></div>';

			if ($module->isPaymentAvailable()) {
				$result['onestep']['payment'] = $module->paymentsList($template);
			}

			return emarket::parseTemplate($purchasing_one_step, $result);
		}
                
                public function getPersonalReccomendation($template = 'default', $limit = 10){
                    $order = $module->getBasketOrder();
                    $orderItems = $order->getItems();
                    
                    $itemsInCartReccomendationIds = array();
                    
                    foreach ($orderItems as $orderItem){
                        
                        $itemsInCartReccomendationIds = '';
                    }
                }
                
                public function addNewAdress(){
                    $addressId = getRequest($addressId);
                    
                    $umiObjects = umiObjectsCollection::getInstance();
                    $addressTypeId = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName("emarket", "delivery_address");
                    
                    if ($addressId == 'new') {
                            $customer = customer::get();
                            $addressId = $umiObjects->addObject('Address for customer #' . $customer->getId(), $addressTypeId);
                            
                            
                            $customerAddressList = (array) $customer->getValue('delivery_addresses');
                            $customerAddressList[] = $addressId;
                            $customer->setValue('delivery_addresses', $customerAddressList);
                            $customer->commit();

                            /**
                             * @var DataForms $data
                             */
                            $data = cmsController::getInstance()
                                    ->getModule('data');
                            $data->saveEditedObjectWithIgnorePermissions($addressId, true, true);
                    }
                }
	}
?>