<?php
	use Yandex\OAuth\OAuthClient;
	use Yandex\Market\MarketClient;

	/**
	 * Клиент сервиса Яндекс.Маркет.
	 * Умеет:
	 *
	 * 1) Уточнять у UMI.CMS актуальность состава покупки в Яндекс.Маркет;
	 * 2) Создавать заказ в UMI.CMS, на основе данных заказа в Яндекс.Маркет;
	 * 3) Обновлять заказ в UMI.CMS, на основе изменения данных заказа в Яндекс.Маркет;
	 * 4) Обвновлять заказ в Яндекс.Маркет, на основе изменения данных заказа в UMI.CMS;
	 *
	 * @link https://tech.yandex.ru/market/partner/doc/dg/reference/purchase-methods-docpage/
	 */
	class EmarketYandexMarketClient {
		/**
		 * @var emarket $module
		 */
		public $module;

		/**
		 * Административный метод.
		 * Записывает настройки интеграции с Яндекс.Маркет в UMI.CMS.
		 * @throws coreException
		 * @throws publicAdminException
		 */
		public function yandexMarketCreateToken() {
			$cmsController = cmsController::getInstance();

			if ($cmsController->getCurrentMode() != 'admin') {
				return;
			}

			$domain = getRequest('domain');
			$clientId = getRequest('clientId');
			$password = getRequest('password');
			$login = getRequest('login');
			$marketToken = getRequest('marketToken');
			$marketCampaignId = getRequest('marketCampaignId');
			$cashOnDelivery = getRequest('cashOnDelivery');
			$cardOnDelivery = getRequest('cardOnDelivery');
			$shopPrepaid = getRequest('shopPrepaid');

			if (strlen($domain) == 0) {
				throw new publicAdminException(getLabel('error-yandex_market-no-domain'));
			}

			$domainId = domainsCollection::getInstance()->getDomainId($domain);

			if (!$domainId) {
				throw new publicAdminException(getLabel('error-yandex_market-domain-not-add'));
			}

			if (strlen($clientId)==0 ||strlen($password)==0 ) {
				throw new publicAdminException(getLabel('error-yandex-market-empty-field'));
			}

			$umiRegistry = regedit::getInstance();
			$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/clientId", $clientId);
			$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/password", $password);

			if (strlen($login)) {
				$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/login", $login);
			}

			if (strlen($marketToken)) {
				$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/marketToken", $marketToken);
			}

			if (strlen($marketCampaignId)) {
				$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/marketCampaignId", $marketCampaignId);
			}

			$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/cashOnDelivery", $cashOnDelivery);
			$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/cardOnDelivery", $cardOnDelivery);
			$umiRegistry->setVar("//modules/emarket/yandex_market/{$domainId}/shopPrepaid", $shopPrepaid);
			$umiRegistry->setVar("//modules/emarket/yandex_market/getTokenRequestDomainId", $domainId);

			$buffer = outputBuffer::current();
			$buffer->send();

			$client = new OAuthClient($clientId);
			$client->authRedirect();
		}

		/**
		 * Административный метод.
		 * Принимает токен от Яндекс.Маркет и записывает в UMI.CMS.
		 * @throws coreException
		 * @throws publicAdminException
		 */
		public function yandexMarketTokenCallback() {
			$cmsController = cmsController::getInstance();

			if ($cmsController->getCurrentMode() != 'admin') {
				return;
			}

			$regedit = regedit::getInstance();
			$domainId = $cmsController->getCurrentDomain()->getId();

			$domainRequestId = $regedit->getVal("//modules/emarket/yandex_market/getTokenRequestDomainId");

			if ($domainId != $domainRequestId) {
				throw new publicAdminException(getLabel('error-yandex_market-domain-error'));
			}

			$clientId = $regedit->getVal("//modules/emarket/yandex_market/{$domainId}/clientId");
			$clientSecret = $regedit->getVal("//modules/emarket/yandex_market/{$domainId}/password");

			$code = getRequest('code');

			try {
				$client = new OAuthClient($clientId, $clientSecret);
				$client->requestAccessToken($code);
				$accessToken = $client->getAccessToken();
			} catch(Exception $e) {
				throw new publicAdminException(getLabel('error-yandex_market-permission-denied'));
			}

			$regedit->setVar("//modules/emarket/yandex_market/{$domainId}/token", $accessToken);
			outputBuffer::current()->redirect('/admin/emarket/yandex_market_config/');
		}

		/**
		 * Обрабатывает запросы от Яндекс.Маркет.
		 * Обрабатывает три типа запросов:
		 *
		 * 1) Запрос информации о товарах;
		 * 2) Передача заказа и запрос на принятие заказа;
		 * 3) Уведомление о смене статуса заказа;
		 */
		public function yandex_market() {
			$method = getRequest("param0");

			switch($method) {
				case 'cart': {
					$this->yandexCart();
					break;
				}
				case 'order': {
					$this->yandexOrder();
					break;
				}
			}

			$this->_sendForbidden('Incorrect command');
		}

		/**
		 * Принимает корзину покупателя из Яндекс.Маркет, уточняет актуальную информацию по товарам в корзине:
		 *
		 * 1) цену и наличие товаров;
		 * 2) опции доставки, доступные для указанной корзины товаров и региона доставки;
		 * 3) доступные способы оплаты.
		 *
		 * Результат уточнения выводитв буффер.
		 * @link http://api.yandex.ru/market/partner/doc/dg/reference/post-cart.xml
		 */
		public function yandexCart() {
			$authorized = getRequest('umi_authorization');

			if (strlen($authorized) === 0) {
				$this->_sendForbidden('Auth param expected');
			}

			$domainId = cmsController::getInstance()->getCurrentDomain()->getId();
			$settings = $this->getYandexMarketSettings($domainId);

			if (!$settings || $authorized !== $settings['marketToken']) {
				$this->_sendForbidden('Not authorized request');
			}

			if (strtolower(getServer('REQUEST_METHOD')) == 'post') {
				$postData = file_get_contents("php://input");
				$request = json_decode($postData);
				$currency = $request->cart->currency;
				$items = array();

				foreach ($request->cart->items as $item) {
					$items[] = $this->_isValidItemFromPostCart(
						$item->feedId,
						$item->offerId,
						$item->feedCategoryId,
						$item->count,
						$currency
					);
				}

				$paymentMethods = $this->_getPaymentMethods($settings);
				$deliveryOptions = $this->_getDeliveryOptions($currency);

				$response = array(
					"cart" => array(
						"items" => $items,
						"deliveryOptions" => $deliveryOptions,
						"paymentMethods" => $paymentMethods
					)
				);

				$this->_outputJson($response);
			}

			$this->_sendBadRequest();
		}

		/**
		 * Возвращает информацию о товаре
		 * @param int $feedId идентификатор прайс-листа, в котором указан товар
		 * @param int $itemId идентификатор товара (объекта каталога)
		 * @param int $categoryId идентификатор категории товара (раздела каталога)
		 * @param int $count количество товара, находящегося в корзине
		 * @param string $currency код валюты, в которой продается товар
		 * @return array
		 * @throws selectorException
		 */
		public function _isValidItemFromPostCart($feedId, $itemId, $categoryId, $count, $currency) {
			$price = umiHierarchy::getInstance()->getElement($itemId)->getValue('price');

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'currency');
			$sel->where('codename')->equals($currency);
			$sel->option('no-length')->value(true);
			$currencyList = $sel->result;

			$availableCount = $count;
			$delivery = true;

			$data = array(
				"feedId" => $feedId,
				"offerId" => $itemId,
				"price" => round($price / $currencyList[0]->rate),
				"count" => $availableCount,
				"delivery" => $delivery
			);

			return $data;
		}

		/**
		 * Возвращает доступные способы оплаты заказа
		 * @param array $settings настройки интеграции с Яндекс.Маркет
		 * @return array
		 */
		public function _getPaymentMethods($settings) {
			$payments = array();

			if ($settings['cashOnDelivery']) {
				array_push($payments, "CASH_ON_DELIVERY");
			}

			if ($settings['cardOnDelivery']) {
				array_push($payments, "CARD_ON_DELIVERY");
			}

			if ($settings['shopPrepaid']) {
				array_push($payments, "SHOP_PREPAID");
			}

			return $payments;
		}

		/**
		 * Возвращает доступные способы доставки заказа
		 * @param string $currency код валюты, в которой продается товар
		 * @return array
		 * @throws selectorException
		 */
		public function _getDeliveryOptions($currency) {
			$response = array();

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'currency');
			$sel->where('codename')->equals($currency);
			$sel->option('no-length')->value(true);
			$sel->option('load-all-props')->value(true);
			$currencyList = $sel->result;

			$order = order::create();
			$deliveriesList = delivery::getList();

			/**
			 * @var iUmiObject|iUmiEntinty $delivery
			 */
			foreach ($deliveriesList as $delivery) {
				/**
				 * @var delivery $deliveryTypeObject
				 */
				$deliveryTypeObject = delivery::get($delivery->getId());
				$deliveryPrice = (float) $deliveryTypeObject->getDeliveryPrice($order);

				$data = array(
					"id" => (string) $delivery->getId(),
					"serviceName" => $delivery->getName(),
					"price" => round($deliveryPrice / $currencyList[0]->rate),
					"dates" => array(
						"fromDate" => date("d-m-Y"),
					)
				);

				if ($deliveryTypeObject instanceof selfDelivery) {
					$data['type'] = 'PICKUP';
					$data['outlets'] = array(
						array('id' => 1)
					);
				} elseif ($deliveryTypeObject instanceof courierDelivery) {
					$data['type'] = 'DELIVERY';
				} else {
					$data['type'] = 'POST';
				}

				$response[] = $data;
			}

			$order->delete();
			return $response;
		}

		/**
		 * Возвращает название страны по идентификатору ее объекта
		 * @param int $code идентификатор объекта
		 * @return string
		 */
		public function _getCountryNameByCode($code) {
			$objects = umiObjectsCollection::getInstance();
			$country = $objects->getObject($code);

			return $country instanceof iUmiObject ? $country->getName() : '';
		}

		/**
		 * Возвращает идентификатор объекта страницы по ее названию
		 * @param string $name название страны
		 * @return null|int
		 * @throws selectorException
		 */
		public function _getCountryCodeByName($name) {
			$label = ulangStream::getI18n($name);
			$selector = new selector('objects');
			$selector->types('object-type')->guid('d69b923df6140a16aefc89546a384e0493641fbe');
			$selector->option('or-mode')->field('name');
			$selector->where('name')->equals($name);
			$selector->where('name')->equals($label);
			$selector->option('no-length')->value(true);
			$selector->option('return')->value('id');

			if ($selector->first) {
				return $selector->first['id'];
			}

			return null;
		}

		/**
		 * Обрабатывает:
		 *
		 * 1) Передачу заказа и запрос на принятие заказа от Яндекс.Маркет;
		 * @link https://tech.yandex.ru/market/partner/doc/dg/reference/post-order-accept-docpage/
		 * 2) Уведомление о смене статуса заказа от Яндекс.Маркет;
		 * @link https://tech.yandex.ru/market/partner/doc/dg/reference/post-order-status-docpage/
		 **
		 * В первом случае создает заказ, аналогичный заказу на
		 * сервисе.
		 * Во втором случае обновляет заказ, в соответствии с
		 * изменениями аналогичного заказа на сервисе.
		 *
		 * Результаты операции выводит в буффер.
		 * @throws coreException
		 * @throws selectorException
		 */
		public function yandexOrder() {
			$authorized = getRequest('umi_authorization');
			if (strlen($authorized) == 0) {
				$this->_sendForbidden('Auth param expected');
			}

			$domainId = cmsController::getInstance()->getCurrentDomain()->getId();
			$settings = $this->getYandexMarketSettings($domainId);

			if (!$settings || $authorized !== $settings['marketToken']) {
				$this->_sendForbidden('Not authorized request');
			}

			$requestType = getRequest('param1');

			if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
				if ($requestType == 'status') {
					$orderStatus = file_get_contents("php://input");
					$request = json_decode($orderStatus);

					$status = $request->order->status;
					$marketOrderId = $request->order->id;
					$buyer = $request->order->buyer;
					$sel = new selector('objects');
					$sel->types('object-type')->name('emarket', 'order');
					$sel->where('yandex_order_id')->equals($marketOrderId);
					$sel->option('no-length')->value(true);
					$sel->limit(0, 1);

					if ($sel->first) {
						$order = $sel->first;
					}

					/** @var order $order */
					$order->setValue('status_change_date', time());

					switch ($status) {
						case MarketClient::ORDER_STATUS_PROCESSING: {
							$order->setValue('status_id', order::getStatusByCode('waiting'));

							if ($request->order->paymentType === 'PREPAID' && $request->order->paymentMethod === 'YANDEX') {
								$order->setValue('payment_status_id', order::getStatusByCode('accepted', 'order_payment_status'));
								$order->commit();
							}

							$customerId = $order->getValue("customer_id");
							$customer = umiObjectsCollection::getInstance()->getObject($customerId);
							$customer->setName($buyer->firstName . ' ' . $buyer->lastName);
							$customer->setValue('fname', $buyer->firstName);
							$customer->setValue('lname', $buyer->lastName);
							$customer->setValue('phone', $buyer->phone);
							$customer->setValue('email', $buyer->email);
							$customer->commit();

							$addressId = $order->getValue("delivery_address");
							$address = umiObjectsCollection::getInstance()->getObject($addressId);
							$address->setValue('country', $this->_getCountryCodeByName($request->order->delivery->address->country));
							$address->setValue('region', $request->order->delivery->region->parent->name);
							$address->setValue('city', $request->order->delivery->address->city);
							$address->setValue('street', $request->order->delivery->address->street);
							$address->setValue('house', $request->order->delivery->address->house);

							if (isset($request->order->delivery->address->apartment)) {
								$address->setValue('flat', $request->order->delivery->address->apartment);
							}

							if (isset($request->order->delivery->address->postcode)) {
								$address->setValue('index', $request->order->delivery->address->postcode);
							}

							$orderComment = '';

							if (isset($request->order->notes)) {
								$orderComment = $request->order->notes;
							}

							if (isset($request->order->delivery->address->block)) {
								$orderComment .= ', Корпус: ' . $request->order->delivery->address->block;
							}

							if (isset($request->order->delivery->address->entrance)) {
								$orderComment .= ', Подъезд: ' . $request->order->delivery->address->entrance;
							}
							if (isset($request->order->delivery->address->entryphone)) {
								$orderComment .= ', Домофон: ' . $request->order->delivery->address->entryphone;
							}
							if (isset($request->order->delivery->address->floor)) {
								$orderComment .= ', Этаж: ' . $request->order->delivery->address->floor;
							}
							if (isset($request->order->delivery->address->recipient)) {
								$orderComment .= ', Получатель: ' . $request->order->delivery->address->recipient;
							}
							if (isset($request->order->delivery->address->phone)) {
								$orderComment .= ', Телефон: ' . $request->order->delivery->address->phone;
							}

							$address->setValue('order_comments', $orderComment);
							$address->commit();

							$order->commit();
							break;
						}
						case MarketClient::ORDER_STATUS_CANCELLED: {
							$order->setValue('status_id', order::getStatusByCode('canceled'));
							$order->commit();
							break;
						}
						case MarketClient::ORDER_STATUS_DELIVERED: {
							$order->setValue('status_id', order::getStatusByCode('ready'));
							$order->commit();
							break;
						}
						case MarketClient::ORDER_STATUS_DELIVERY: {
							$order->setValue('status_id', order::getStatusByCode('delivery'));
							$order->commit();
							break;
						}
						case MarketClient::ORDER_STATUS_PICKUP: {
							$order->setValue('status_id', order::getStatusByCode('ready'));
							$order->commit();
							break;
						}
					}

					/** @var HTTPOutputBuffer $buffer */
					$buffer = outputBuffer::current('HTTPOutputBuffer');
					$buffer->contentType('application/json');
					$buffer->end();

				} elseif ($requestType == 'accept') {
					$postData = file_get_contents("php://input");
					$request = json_decode($postData);
					$marketOrderId = $request->order->id;

					$user = customer::get();
					$umiObjects = umiObjectsCollection::getInstance();
					$typeId = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName("emarket", "delivery_address");

					$addressId = $umiObjects->addObject("Address for customer #" . $user->getId(), $typeId);
					$address = $umiObjects->getObject($addressId);
					$address->setValue('country', $this->_getCountryCodeByName($request->order->delivery->address->country));
					$address->setValue('region', $request->order->delivery->region->parent->name);
					$address->setValue('city', $request->order->delivery->address->city);
					$address->setValue('street', $request->order->delivery->address->street);
					$address->setValue('house', $request->order->delivery->address->house);
					$address->commit();

					if (!in_array($addressId, $user->delivery_addresses)) {
						$user->setValue("delivery_addresses", array_merge($user->delivery_addresses, array($addressId)));
						$user->commit();
					}

					$orderId = $request->order->id;
					$order = null;

					if ($orderId) {
						$sel = new selector('objects');
						$sel->types('object-type')->name('emarket', 'order');
						$sel->where('yandex_order_id')->equals($marketOrderId);
						$sel->option('no-length')->value(true);
						$sel->limit(0, 1);
						$order = ($sel->first instanceof iUmiObject) ? $sel->first : null;
					}

					if (is_null($order)) {
						$order = order::create();
						$order->setValue("yandex_order_id", $request->order->id);
						$order->setValue('order_date', time());
					}

					$order->setValue("delivery_address", $addressId);
					$order->commit();

					foreach ($request->order->items as $item) {
						$orderItem = orderItem::create($item->offerId);
						$orderItem->setAmount($item->count);
						$order->appendItem($orderItem);
					}

					$deliveryId = $request->order->delivery->id;
					/**
					 * @var delivery $delivery
					 */
					$delivery = delivery::get($deliveryId);
					$deliveryPrice = (float) $delivery->getDeliveryPrice($order);

					$order->setValue('delivery_id', $deliveryId);
					$order->setValue('delivery_price', $deliveryPrice);
					$order->refresh();
					$order->generateNumber();
					$order->setOrderStatus(order::getStatusByCode('waiting'));
					$response = array(
						"order" => array(
							"accepted" => true,
							"id" => (string) $order->id
						)
					);

					$this->_outputJson($response);
				}
			}

			$this->_sendBadRequest();
		}

		/**
		 * Выводит в буффер пустой ответ со статусом 400
		 */
		public function _sendBadRequest() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->status('400 Bad Request');
			$buffer->end();
		}

		/**
		 * Выводит в буффер ответ со статусом 403
		 * @param string $message сообщение ответа
		 */
		public function _sendForbidden($message) {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->status('403 Forbidden');
			$buffer->push($message);
			$buffer->end();
		}

		/**
		 * Выводит в буффер пустой ответ со статусом 404
		 */
		public function _sendNotFound() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->status('404 Not Found');
			$buffer->end();
		}

		/**
		 * Выводит в буффер пустой ответ со статусом 500
		 */
		public function _sendServerError() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->status('500 Internal Server Error');
			$buffer->end();
		}

		/**
		 * Выводит данные в буффер в формате json
		 * @param mixed $data данные
		 * @throws coreException
		 */
		public function _outputJson($data) {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->contentType('application/json');
			$buffer->charset('utf-8');
			$buffer->clear();
			$buffer->push(json_encode($data));
			$buffer->end();
		}


		/**
		 * Обновляет адрес доставки в заказа в Яндекс.Маркет, в соответсвии с заказом в UMI.CMS
		 * @param int|string $orderId идентификатор заказа в Яндекс.Маркет
		 * @param array $data данные адреса заказа в UMI.CMS
		 * @param iUmiObject $order заказ в UMI.CMS
		 */
		public function _updateDeliveryAddress($orderId, $data, $order) {
			$domainName = $order->getValue('domain_id');
			$domainId = domainsCollection::getInstance()->getDomainId($domainName);
			$settings = $this->getYandexMarketSettings($domainId);

			$market = new MarketClient($settings['token']);
			$market->setClientId($settings['clientId']);
			$market->setLogin($settings['login']);
			$market->setCampaignId($settings['marketCampaignId']);

			$order = $market->getOrder($orderId);

			$recipient = '';

			if (isset($order['order']['delivery']['address']['recipient'])) {
				$recipient = $order['order']['delivery']['address']['recipient'];
			}
			$phone = '';

			if (isset($order['order']['delivery']['address']['phone'])) {
				$phone = $order['order']['delivery']['address']['phone'];
			}

			$data = array(
				'delivery' => array(
					'address' => array(
					'country' => $this->_getCountryNameByCode($data['country']),
					'postcode' => $data['index'],
					'city' => $data['city'],
					'street' => $data['street'],
					'house' => $data['house'],
					'apartment' => $data['flat'],
					"recipient" => $recipient,
					"phone" => $phone
					)
				)
			);

			$market->updateDelivery($orderId, $data);
		}

		/**
		 * Возвращает статус заказа в Яндекс.Маркет, соответствующий статусу заказа в UMI.CMS
		 * @param string $status статус заказа в UMI.CMS.
		 * @return array
		 */
		public function _getMarketStatusByShopStatus($status) {
			$statuses = array(
				'canceled' => array(
					'status' => MarketClient::ORDER_STATUS_CANCELLED,
					'subStatus' => MarketClient::ORDER_SUBSTATUS_USER_CHANGED_MIND
				),
				'rejected' => array(
					'status' => MarketClient::ORDER_STATUS_CANCELLED,
					'subStatus' => MarketClient::ORDER_SUBSTATUS_SHOP_FAILED
				),
				'delivery' => array(
					'status' => MarketClient::ORDER_STATUS_DELIVERY,
					'subStatus' => null
				),
				'ready' => array(
					'status' => MarketClient::ORDER_STATUS_DELIVERED,
					'subStatus' => null
				),
				'waiting' => array(
					'status' => MarketClient::ORDER_STATUS_PROCESSING,
					'subStatus' => null
				),
				'payment' => array(
					'status' => MarketClient::ORDER_STATUS_PROCESSING,
					'subStatus' => null
				),
				'editing' => array(
					'status' => MarketClient::ORDER_STATUS_PROCESSING,
					'subStatus' => null
				),
				'accepted' => array(
					'status' => MarketClient::ORDER_STATUS_PROCESSING,
					'subStatus' => null
				),
				'waiting_shipping' => array(
					'status' => MarketClient::ORDER_STATUS_DELIVERY,
					'subStatus' => null
				),
				'shipping' => array(
					'status' => MarketClient::ORDER_STATUS_DELIVERY,
					'subStatus' => null
				),
				'not_defined' => array(
					'status' => MarketClient::ORDER_STATUS_PROCESSING,
					'subStatus' => null
				)
			);

			return $statuses[$status];
		}

		/**
		 * Можно ли менять статус заказа
		 * @param string $oldStatus старый статус заказа в Яндекс.Маркет
		 * @param string $newStatus новый статус заказа в Яндекс.Маркет
		 * @return bool
		 */
		public function _isAllowChangeStatus($oldStatus, $newStatus) {

			if ($oldStatus === 'UNPAID') {
				if ($newStatus === MarketClient::ORDER_STATUS_DELIVERY || $newStatus === MarketClient::ORDER_STATUS_CANCELLED) {
					return true;
				}
			}

			if ($oldStatus === MarketClient::ORDER_STATUS_PROCESSING) {
				if ($newStatus === MarketClient::ORDER_STATUS_DELIVERY || $newStatus === MarketClient::ORDER_STATUS_CANCELLED) {
					return true;
				}
			}

			if ($oldStatus === MarketClient::ORDER_STATUS_DELIVERY) {
				if (
					$newStatus === MarketClient::ORDER_STATUS_PICKUP ||
					$newStatus === MarketClient::ORDER_STATUS_DELIVERED ||
					$newStatus === MarketClient::ORDER_STATUS_CANCELLED
				) {
					return true;
				}
			}

			if ($oldStatus === MarketClient::ORDER_STATUS_PICKUP) {
				if ($newStatus === MarketClient::ORDER_STATUS_DELIVERED || $newStatus === MarketClient::ORDER_STATUS_CANCELLED) {
					return true;
				}
			}

			return false;
		}


		/**
		* @param int $orderId
		* @param string $status
		* @throws Exception
		*/

		/**
		 * Обновляет статус заказа в Яндекс.Маркет, в соответсвии со статусом заказа в UMI.CMS
		 * @param int|string $orderId идентификатор заказа в Яндекс.Маркет
		 * @param string $status статус заказа в UMI.CMS
		 * @param bool|iUmiObject $object заказ в UMI.CMS
		 * @return bool|void
		 * @throws Exception
		 */
		public function _sendStatusOnChangeOrderStatus($orderId, $status, $object = false) {
			$settings = $this->getYandexMarketSettings($object->getValue('domain_id'));

			if (!$settings) {
				return false;
			}

			$market = new MarketClient($settings['token']);
			$market->setClientId($settings['clientId']);
			$market->setLogin($settings['login']);
			$market->setCampaignId($settings['marketCampaignId']);

			$order = $market->getOrder($orderId);
			$currentMarketStatus = $order['order']['status'];

			$statusData = $this->_getMarketStatusByShopStatus($status);

			if ($currentMarketStatus === $statusData['status']) {
				return;
			}

			if ($this->_isAllowChangeStatus($currentMarketStatus, $statusData['status'])) {
				$market->setOrderStatus(
					$orderId,
					$statusData['status'],
					$statusData['subStatus']
				);
			} else {
				throw new Exception('Нельзя изменить на такой статус для заказа с Яндекс.Маркета');
			}

		}

		/**
		 * Возвращает настройки интеграции с Яндекс.Маркет для заданного сайта системы
		 * @param bool|int $domainId идентификатор домена (сайта) системы
		 * @return array|bool
		 */
		public function getYandexMarketSettings($domainId = false) {
			if (!$domainId) {
				return false;
			}

			if (!is_numeric($domainId)) {
				$domainId = domainsCollection::getInstance()->getDomainId($domainId);
			}

			if (!$domainId) {
				return false;
			}

			$umiRegistry = regedit::getInstance();

			$settings = array();
			$settings['clientId'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/clientId");
			$settings['password'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/password");
			$settings['token'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/token");
			$settings['login'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/login");
			$settings['marketToken'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/marketToken");
			$settings['marketCampaignId'] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/marketCampaignId");
			$settings["cashOnDelivery"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/cashOnDelivery");
			$settings["cardOnDelivery"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/cardOnDelivery");
			$settings["shopPrepaid"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domainId}/shopPrepaid");

			if (strlen($settings['clientId']) == 0) {
				return false;
			}

			return $settings;
		}

		/**
		 * Возвращает заказ в Яндекс.Маркет, соответствующий заказу в UMI.CMS
		 * @param iUmiObject $order заказ в UMI.CMS
		 * @return object
		 */
		public function getYandexMarketOrderById(iUmiObject $order) {
			$settings = $this->getYandexMarketSettings($order->getValue('domain_id'));

			$market = new MarketClient($settings['token']);
			$market->setClientId($settings['clientId']);
			$market->setLogin($settings['login']);
			$market->setCampaignId($settings['marketCampaignId']);

			return $market->getOrder($order->getValue('yandex_order_id'));
		}

		/**
		 * Инициирует обновления данных доставки заказа в Яндекс.Маркет
		 * @param iUmiObject $order заказ в UMI.CMS
		 * @param mixed $data данные доставки
		 */
		public function updateYandexMarketDelivery(iUmiObject $order, $data) {
			$settings = $this->getYandexMarketSettings($order->getValue('domain_id'));

			$market = new MarketClient($settings['token']);
			$market->setClientId($settings['clientId']);
			$market->setLogin($settings['login']);
			$market->setCampaignId($settings['marketCampaignId']);
			$market->updateDelivery($order->getValue('yandex_order_id'), $data);
		}
	}
?>
