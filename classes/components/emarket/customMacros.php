<?php
	/**
	 * Класс пользовательских макросов
	 */
	class EmarketCustomMacros {
		/**
		 * @var emarket $module
		 */
		public $module;

		/**
		 * Изменяет состояние корзины покупателя.
		 *
		 * Действия над корзиной:
		 *
		 * 1) /emarket/basket/put/element/16/ - положить в корзину товар (объект каталога) с id = 16
		 * 2) /emarket/basket/put/element/16/?amount=2 - положить в корзину товар (объект каталога) с id = 16 в количестве = 2
		 * 3) /emarket/basket/put/element/16/?options[name]=10 - положить в корзину товар (объект каталога) с id = 16 с опцией
		 * 4) /emarket/basket/put/element/16/?amount=2&options[name]=10  2) и 3) пункты одновременно
		 * 5) /emarket/basket/remove/element/16/ - убрать из корзины товар (объект каталога) с id = 16
		 * 6) /emarket/basket/remove/item/16/ - убрать из корзины товар (наименование заказа) с id = 16
		 * 7) /emarket/basket/remove_all - убрать из корзины все товары
		 *
		 * Вызывает пересчет корзины.
		 * Либо возвращает заказ, либо осуществляет перенаправление.
		 *
		 * @param string|bool $mode выполняемое действие (put/remove/remove_all)
		 * @param string|bool $itemType тип товара (element/item)
		 * @param int|bool $itemId идентификатор товара
		 * @return mixed
		 * @throws publicException
		 */
		public function custom_basket($mode = false, $itemType = false, $itemId = false) {
			$mode = $mode ? $mode : getRequest('param0');
			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			$order = $module->getBasketOrder(!in_array($mode, array('put', 'remove')));
			$itemType = $itemType ? $itemType : getRequest('param1');
			$itemId = (int) ($itemId ? $itemId : getRequest('param2'));
			$amount = (int) getRequest('amount');
			$options = getRequest('options');
                        
			switch ($mode) {
				case 'remove_all' : {
					foreach ($order->getItems() as $orderItem) {
						$order->removeItem($orderItem);
					}

					break;
				}
				case 'remove' : {
					$orderItem = ($itemType == 'element') ? $module->getBasketItem($itemId, false) : orderItem::get($itemId);

					if ($orderItem instanceof orderItem) {
						$order->removeItem($orderItem);
					}

					break;
				}
				case 'put' : {
					$newElement = false;

					if ($itemType == 'element') {
						$orderItem = $module->getBasketItem($itemId, false);

						if (!$orderItem) {
							$orderItem = $module->getBasketItem($itemId);
							$newElement = true;
						}
					} else {
						$orderItem = $order->getItem($itemId);
					}

					if (!$orderItem instanceof orderItem) {
						throw new publicException("Order item is not defined");
					}


                                        
                                        
					if (is_array($options)) {
						if ($itemType != 'element') {
							throw new publicException("Put basket method required element id of optionedOrderItem");
						}

						$orderItem = $this->appendOption($order, $orderItem, $options, $newElement, $itemId);
					}

					$oldAmount = $orderItem->getAmount();
					//$amount = $amount ? $amount : ($oldAmount + 1);
					$amount = $oldAmount + (int)$amount;
					$orderItem->setAmount($amount);
					$orderItem->refresh();
					$newAmount = $orderItem->getAmount();

					if ($itemType == 'element') {
						$order->appendItem($orderItem);
					} elseif($oldAmount != $newAmount) {
						$order->saveTotalProperties();
					}

					break;
				}
			}

			$order->refresh();
			$referrer = getServer('HTTP_REFERER');
			$noRedirect = getRequest('no-redirect');

			if ($redirectUri = getRequest('redirect-uri')) {
				$module->redirect($redirectUri);
			}

			if (!defined('VIA_HTTP_SCHEME') && !$noRedirect && $referrer) {
				$current = $_SERVER['REQUEST_URI'];

				if (substr($referrer, -strlen($current)) == $current) {
					if ($itemType == 'element') {
						$referrer = umiHierarchy::getInstance()->getPathById($itemId);
					} else {
						$referrer = "/";
					}
				}

				$module->redirect($referrer);
			}

			return $module->order($order->getId());
		}

                
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
	
                /**
                 * Функция проверят заявку на обратный звонок и отправляет письмо на почту менеджеру
                 */
                public function oneClickOrder() {
                    $itemID = getRequest('id');
                    $clientName = getRequest('name');
                    $clientPhone = getRequest('phone');
                            
                    $answer = array();
                    $answer['status'] = 'error';
                    
                    if(empty($clientName)){
                        $answer['message'] = 'Пожалуйста, укажите Ваше имя';
                        $answer['fldID'] = 'oneclick__name';
                        echo json_encode($answer);
                        exit;
                    }
                    
                    if(empty($clientPhone)){
                        $answer['message'] = 'Пожалуйста, укажите Ваше номер телефона для связи';
                        $answer['fldID'] = 'oneclick__phone';
                        echo json_encode($answer);
                        exit;
                    }
                    
                    $hierarchy = umiHierarchy::getInstance();
                    $page = $hierarchy->getElement($itemID);

                    $adminEmail = regedit::getInstance()->getVal("//modules/emarket/manager-email/1");
                    $fromEmail = regedit::getInstance()->getVal("//modules/emarket/from-email/1");
                    
                    $content = "";
                    $content .= "Пользователь: ".htmlspecialchars($clientName)."<br>";
                    $content .= "Номер телефона: ".htmlspecialchars($clientPhone)."<br>";
                    $content .= "Товар: ".$page->getName();
                    
                    $letter = new umiMail();
                    $letter->addRecipient($adminEmail);
                    $letter->setFrom($fromEmail);
                    $letter->setSubject('Заказ товара в 1 клик');
                    $letter->setContent($content);
                    $letter->commit();
                    $letter->send();
                    
                    $answer['status'] = 'ok';
                    $answer['message'] = false;
                    
                    echo json_encode($answer);
                    exit;
                }


                /**
                 * Функция проверят заявку на обратный звонок и отправляет письмо на почту менеджеру
                 */
                public function backCallRequest() {
                    $itemID = getRequest('id');
                    $clientName = getRequest('name');
                    $clientPhone = getRequest('phone');
                    $clientComment = getRequest('comment');
                            
                    $answer = array();
                    $answer['status'] = 'error';
                    
                    if(empty($clientName)){
                        $answer['message'] = 'Пожалуйста, укажите Ваше имя';
                        $answer['fldID'] = 'backcall__name';
                        echo json_encode($answer);
                        exit;
                    }
                    
                    if(empty($clientPhone)){
                        $answer['message'] = 'Пожалуйста, укажите Ваше номер телефона для связи';
                        $answer['fldID'] = 'backcall__phone';
                        echo json_encode($answer);
                        exit;
                    }
                    
                    if(empty($clientComment)){
                        $answer['message'] = 'Пожалуйста, напишите ваш вопрос';
                        $answer['fldID'] = 'backcall__comment';
                        echo json_encode($answer);
                        exit;
                    }
                    
                    $hierarchy = umiHierarchy::getInstance();
                    $page = $hierarchy->getElement($itemID);

                    $adminEmail = regedit::getInstance()->getVal("//modules/emarket/manager-email/1");
                    $fromEmail = regedit::getInstance()->getVal("//modules/emarket/from-email/1");
                    
                    $content = "";
                    $content .= "Пользователь: ".htmlspecialchars($clientName)."<br>";
                    $content .= "Номер телефона: ".htmlspecialchars($clientPhone)."<br>";
                    $content .= "Вопрос: ".htmlspecialchars($clientComment);
                    
                    $letter = new umiMail();
                    $letter->addRecipient($adminEmail);
                    $letter->setFrom($fromEmail);
                    $letter->setSubject('Вопрос с сайта');
                    $letter->setContent($content);
                    $letter->commit();
                    $letter->send();
                    
                    $answer['status'] = 'ok';
                    $answer['message'] = false;
                    
                    echo json_encode($answer);
                    exit;
                }


                
        }
?>