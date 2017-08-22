<?php
	use UmiCms\Classes\Components\Emarket\Delivery\Address;
	/**
	 * Класс макросов, то есть методов, доступных в шаблоне
	 */
	class EmarketMacros {
		/**
		 * @var emarket $module
		 */
		public $module;

		/**
		 * Возвращает адрес, перейдя по которому товар будет добавлен в корзину
		 * @param int $elementId идентификатор товара (объекта каталога)
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function basketAddLink($elementId, $template = 'default') {
			list($tpl_block) = emarket::loadTemplates(
				"emarket/" . $template,
				'basket_add_link'
			);

			return emarket::parseTemplate($tpl_block, array(
				'link' => $this->module->pre_lang . '/emarket/basket/put/element/' . (int) $elementId . '/'
			));
		}

		/**
		 * Возвращает адрес, перейдя по которому товар будет добавлен в корзину
		 * и в заказе будет выбран способ оплаты
		 * @param int $elementId идентификатор товара (объекта каталога)
		 * @param int|string $paymentIdOrGUID идентификатор или гуид способа оплаты
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function basketAddFastLink($elementId, $paymentIdOrGUID, $template = 'default'){
			if (is_null($elementId)) {
				$elementId = getRequest('param0');
			}

			if (is_null($paymentIdOrGUID)) {
				$paymentIdOrGUID = (int) getRequest('param1');
			}

			list($tplBlock, $tplError) = emarket::loadTemplates(
				"emarket/" . $template,
				'basket_add_fast_link',
				'basket_add_fast_error'
			);

			$umiObjectsCollection = umiObjectsCollection::getInstance();

			if (!is_numeric($paymentIdOrGUID)) {
				$payment = $umiObjectsCollection->getObjectByGUID($paymentIdOrGUID);
			} else {
				$payment = $umiObjectsCollection->getObject($paymentIdOrGUID);
			}

			if (!$payment instanceof iUmiObject || !in_array($payment, payment::getList())) {
				return emarket::parseTemplate($tplError, array(
					'error' => getLabel('error-basket_fast_add-no_payment')
				));
			}
			/**
			 * @var iUmiObject|UmiEntinty $payment
			 */
			return emarket::parseTemplate($tplBlock, array(
				'link' => $this->module->pre_lang . '/emarket/fastPurchase/' . (int) $elementId . '/' . $payment->getId() . '/'
			));
		}

		/**
		 * Добавляет в товар в заказ и устанавливает заказу способ оплаты
		 * @param null|int $elementId идентификатор товара (объекта каталога)
		 * @param null|int $paymentId идентификатор способа оплаты
		 * @throws breakException
		 */
		public function fastPurchase($elementId = null, $paymentId = null) {
			if (is_null($elementId)) {
				$elementId = getRequest('param0');
			}
			if (is_null($paymentId)) {
				$paymentId = (int) getRequest('param1');
			}

			$umiObjectsCollection = umiObjectsCollection::getInstance();
			$payment = $umiObjectsCollection->getObject($paymentId);

			if (!$payment instanceof iUmiObject || !in_array($payment, payment::getList())) {
				throw new breakException(getLabel('error-basket_fast_add-no_payment'));
			}

			$noRedirect = getRequest('no-redirect');
			$redirectUrl = null;

			if (!$noRedirect) {
				if (NULL == ($redirectUrl = getRequest('redirect-uri'))) {
					$redirectUrl = $this->module->pre_lang . '/emarket/cart/';
				}
			}

			$_REQUEST['no-redirect'] = 1;
			$this->basket('put', 'element', $elementId);
			$order = $this->module->getBasketOrder();
			$order->setValue('payment_id', (int) $paymentId);
			$order->commit();

			if (!$noRedirect && !is_null($redirectUrl)) {
				$this->module->redirect($redirectUrl);
			}
		}

		/**
		 * Возвращает стоимость товара с учетом скидок.
		 * @param null|int $elementId идентификатор товара (объекта каталога)
		 * @param string $template имя шаблона (для tpl)
		 * @param bool $showAllCurrency выводить во всех доступных валютах
		 * @return mixed|null
		 * @throws publicException
		 */
		public function price($elementId = null, $template = 'default', $showAllCurrency = true) {
			if (!$elementId) {
				return null;
			}

			$hierarchy = umiHierarchy::getInstance();
			$elementId = $this->module->analyzeRequiredPath($elementId);
			$element = $hierarchy->getElement($elementId);

			if ($element instanceof iUmiHierarchyElement == false) {
				throw new publicException("Wrong element id given");
			}

			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			list($tpl_block) = emarket::loadTemplates(
				"emarket/" . $template,
				'price_block'
			);

			$result = array(
				'attribute:element-id' => $elementId
			);

			$discount = itemDiscount::search($element);

			if ($discount instanceof discount) {
				$result['discount'] = array(
					'attribute:id'		=> $discount->getId(),
					'attribute:name'	=> $discount->getName(),
					'description'		=> $discount->getValue('description')
				);
				$result['void:discount_id'] = $discount->getId();
			}

			$price = $module->formatPrice($element->getValue('price'), $discount);

			if ($currencyPrice = $module->formatCurrencyPrice($price)) {
				$result['price'] = $currencyPrice;
			} else {
				$result['price'] = $price;
			}

			$result['price'] = $module->parsePriceTpl($template, $result['price']);
			$result['void:price-original'] = getArrayKey($result['price'], 'original');
			$result['void:price-actual'] = getArrayKey($result['price'], 'actual');

			if ($showAllCurrency) {
				$result['currencies'] = $module->formatCurrencyPrices($price);
				$result['currency-prices'] = $module->parseCurrencyPricesTpl($template, $price);
			}

			return emarket::parseTemplate($tpl_block, $result);
		}

		/**
		 * @param string $template
		 * @param array $pricesData
		 * @param iUmiObject $currentCurrency
		 * @return mixed
		 */
		public function parseCurrencyPricesTpl($template = 'default', $pricesData = array(), iUmiObject $currentCurrency = null) {
			list($tpl_block, $tpl_item) = emarket::loadTemplates(
				"emarket/currency/{$template}",
				'currency_prices_block',
				'currency_prices_item'
			);

			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;

			if (is_null($currentCurrency)) {
				/**
				 * @var iUmiObject|iUmiEntinty $currentCurrency
				 */
				$currentCurrency = $this->module->getCurrentCurrency();
			}

			$block_arr = array();
			$items_arr = array();
			$currencyIds = $this->module->getCurrencyList();

			/**
			 * @var iUmiObject|iUmiEntinty $currency
			 */
			foreach ($currencyIds as $currency) {
				if ($currentCurrency->getId() == $currency->getId()) {
					continue;
				}

				if ($info = $module->formatCurrencyPrice($pricesData, $currency, $currentCurrency)) {
					if (!$info['original']) {
						$info['original'] = $info['actual'];
					}

					$info['price-original'] = $info['original'];
					$info['price-actual'] = $info['actual'];
					$items_arr[] = emarket::parseTemplate($tpl_item, $info);
				}
			}

			$block_arr['subnodes:items'] = $items_arr;
			return emarket::parseTemplate($tpl_block, $block_arr);
		}

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
		public function basket($mode = false, $itemType = false, $itemId = false) {
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
					$amount = $amount ? $amount : ($oldAmount + 1);
					$orderItem->setAmount($amount ? $amount : 1);
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

		/**
		 * Применяет опции к товару в заказ и возвращает его
		 * @param order $order заказ
		 * @param orderItem $orderItem товар в заказе
		 * @param array $options данные опций
		 * @param bool $isNewElement новый ли товар модифицируется
		 * @param int $itemId идентификатор товара
		 * @return null|optionedOrderItem|orderItem
		 * @throws publicException
		 */
		public function appendOption(order $order, orderItem $orderItem, array $options, $isNewElement, $itemId) {
			$orderItems = $order->getItems();
			$currentProduct = $orderItem->getItemElement();

			if (!$currentProduct instanceof iUmiHierarchyElement) {
				throw new publicException("Wrong current item");
			}

			/**
			 * @var iUmiHierarchyElement|iUmiEntinty $currentProduct
			 */
			foreach ($orderItems as $tOrderItem) {
				if (!$tOrderItem instanceOf optionedOrderItem) {
					$itemOptions = null;
					$tOrderItem = null;
					continue;
				}

				$itemOptions = $tOrderItem->getOptions();

				if (sizeof($itemOptions) != sizeof($options)) {
					$itemOptions = null;
					$tOrderItem = null;
					continue;
				}

				$itemProduct = $tOrderItem->getItemElement();

				if (!$itemProduct instanceof iUmiHierarchyElement) {
					$itemOptions = null;
					$tOrderItem = null;
					continue;
				}

				/**
				 * @var iUmiHierarchyElement|iUmiEntinty $itemProduct
				 */
				if ($itemProduct->getId() != $currentProduct->getId()) {
					$itemOptions = null;
					$tOrderItem = null;
					continue;
				}

				foreach ($options as $optionName => $optionId) {
					$itemOption = getArrayKey($itemOptions, $optionName);

					if (getArrayKey($itemOption, 'option-id') != $optionId) {
						$tOrderItem = null;
						continue 2;
					}
				}

				break;
			}

			if (!isset($tOrderItem) || is_null($tOrderItem)) {
				$tOrderItem = orderItem::create($itemId);
				$order->appendItem($tOrderItem);

				if ($isNewElement) {
					$orderItem->remove();
				}
			}

			if ($tOrderItem instanceof optionedOrderItem) {
				foreach ($options as $optionName => $optionId) {
					if ($optionId) {
						$tOrderItem->appendOption($optionName, $optionId);
					} else {
						$tOrderItem->removeOption($optionName);
					}
				}
			}

			if ($tOrderItem) {
				$orderItem = $tOrderItem;
			}

			return $orderItem;
		}

		/**
		 * Возвращает содержимое заказа пользователя.
		 * Вызывает пересчет заказа
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function cart($template = 'default') {
			$customer_id = (int) \UmiCms\Service::CookieJar()
				->get('customer-id');
			/**
			 * @var emarket|EmarketMacros|EmarketPurchasingStages $module
			 */
			$module = $this->module;

			if (!permissionsCollection::getInstance()->isAuth() && !$customer_id){

				list($tpl_block_empty) = emarket::loadTemplates(
					"emarket/" . $template,
					'order_block_empty'
				);

				$result = array(
					'attribute:id' => 'dummy',
					'summary' => array('amount' => 0),
					'steps' => $module->getPurchaseSteps($template, null)
				);

				return emarket::parseTemplate($tpl_block_empty, $result);
			}

			$order = $this->module->getBasketOrder();
			$order->refresh();

			return $module->order($order->getId(), $template);
		}

		/**
		 * Возвращает данные текущего покупателя
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function getCustomerInfo($template = 'default') {
			$order = $this->module->getBasketOrder();
			/**
			 * @var emarket|EmarketMacros|EmarketPurchasingStages $module
			 */
			$module = $this->module;
			return $module->renderOrderCustomer($order, $template);
		}

		/**
		 * Возвращает список складов товара
		 * @param int|string|bool $elementId идентификатор или адрес товара (объекта каталога)
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws publicException
		 */
		public function stores($elementId, $template = 'default') {
			if (!$template) {
				$template = 'default';
			}

			$hierarchy = umiHierarchy::getInstance();
			$objects = umiObjectsCollection::getInstance();

			list($tpl_block, $tpl_block_empty, $tpl_item) = emarket::loadTemplates(
				"emarket/stores/" . $template,
				'stores_block',
				'stores_block_empty',
				'stores_item'
			);

			$elementId = $this->module->analyzeRequiredPath($elementId);

			if ($elementId == false) {
				throw new publicException("Wrong element id given");
			}

			$element = $hierarchy->getElement($elementId);

			if ($element instanceof iUmiHierarchyElement == false) {
				throw new publicException("Wrong element id given");
			}

			$storesInfo = $element->getValue('stores_state');
			$items_arr = array();
			$stores = array();
			$total = 0;

			if (is_array($storesInfo)) {
				foreach ($storesInfo as $storeInfo) {
					/**
					 * @var iUmiObject|iUmiEntinty $object
					 */
					$object = $objects->getObject(getArrayKey($storeInfo, 'rel'));

					if (!$object instanceof iUmiObject) {
						continue;
					}

					$amount = (int) getArrayKey($storeInfo, 'int');
					$total += $amount;

					$store = array('attribute:amount' => $amount);

					if ($object->getValue('primary')) {
						$reserved = (int) $element->getValue('primary');
						$store['attribute:amount'] -= $reserved;
						$store['attribute:reserved'] = $reserved;
						$store['attribute:primary'] = 'primary';
					}

					$store['item'] = $object;
					$stores[] = $store;
					$items_arr[] = emarket::parseTemplate($tpl_item, array(
						'store_id' => $object->getId(),
						'amount' => $amount,
						'name' => $object->getName()
					), false, $object->getId());
				}
			}

			$result = array(
				'stores' => array(
					'attribute:total-amount' => $total,
					'nodes:store' => $stores
				)
			);

			$result['void:total-amount'] = $total;
			$result['void:items'] = $items_arr;

			if (!$total) {
				$tpl_block = $tpl_block_empty;
			}

			return emarket::parseTemplate($tpl_block, $result);
		}

		/**
		 * Возвращает данные скидки на товар
		 * @param bool $discountId
		 * @param string $template
		 * @return mixed
		 */
		public function discountInfo($discountId = false, $template = 'default') {
			if(!$template) $template = 'default';
			list($tpl_block, $tpl_block_empty) = def_module::loadTemplates("emarket/discounts/{$template}",
				'discount_block', 'discount_block_empty');

			try {
				/**
				 * @var discount $discount
				 */
				$discount = itemDiscount::get($discountId);
			} catch (privateException $e) {
				$discount = null;
			}

			if (!$discount instanceof discount) {
				return emarket::parseTemplate($tpl_block_empty, array());
			}

			$info = array(
				'attribute:id'		=> $discount->getId(),
				'attribute:name'	=> $discount->getName(),
				'description'		=> $discount->getValue('description')
			);

			return emarket::parseTemplate($tpl_block, $info, false, $discount->getId());
		}

		/**
		 * Возвращает список цен, пересчитанных в разные валюты
		 * @param array $prices оригинальная и актуальная цены
		 * @param iUmiObject $defaultCurrency валюта по умолчанию
		 * @return array
		 */
		public function formatCurrencyPrices($prices, iUmiObject $defaultCurrency = null) {
			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			$currencyIds = $module->getCurrencyList();
			$result = array();

			foreach ($currencyIds as $currency) {
				$info = $module->formatCurrencyPrice($prices, $currency, $defaultCurrency);

				if (is_array($info)) {
					$result[] = $info;
				}
			}
			return array(
				'nodes:price' => $result
			);
		}

		/**
		 * Получает значение скидки и возвращает оригинальную цену и цену со скидкой
		 * @param float $originalPrice оригинальная цена
		 * @param itemDiscount $discount скидка
		 * @return array
		 */
		public function formatPrice($originalPrice, itemDiscount $discount = null) {
			$actualPrice = ($discount instanceof itemDiscount) ? $discount->recalcPrice($originalPrice) : $originalPrice;

			if ($originalPrice == $actualPrice) {
				$originalPrice = null;
			}

			return array(
				'original' => $originalPrice,
				'actual' => $actualPrice
			);
		}

		/**
		 * Возвращает список заказов пользователя, отсортированные по id
		 * @param string $template имя шаблона (для tpl)
		 * @param string $sort режим сортировки (asc/desc)
		 * @return mixed
		 * @throws selectorException
		 */
		public function ordersList($template = 'default', $sort = "asc") {
			list($tplBlock, $tplBlockEmpty, $tplItem) = emarket::loadTemplates(
				"emarket/" . $template,
				'orders_block',
				'orders_block_empty',
				'orders_item'
			);

			$domainId = cmsController::getInstance()->getCurrentDomain()->getId();

			$select = new selector('objects');
			$select->types('object-type')->name('emarket', 'order');
			$select->where('customer_id')->equals(customer::get()->getId());
			$select->where('name')->isNull(false);
			$select->where('domain_id')->equals($domainId);
			$select->option('no-length')->value(true);
			$select->option('load-all-props')->value(true);

			if (in_array($sort, array("desc"))) {
				call_user_func(array($select->order('id'), $sort));
			}

			if (!$select->first) {
				$tplBlock = $tplBlockEmpty;
			}

			$itemsArray = array();
			/**
			 * @var umiObject $order
			 */
			foreach ($select->result() as $order) {
				$item = array(
					'attribute:id' => $order->getId(),
					'attribute:name' => $order->getName(),
					'attribute:type-id' => $order->getTypeId(),
					'attribute:guid' => $order->getGUID(),
					'attribute:type-guid' => $order->getTypeGUID(),
					'attribute:ownerId' => $order->getOwnerId(),
					'xlink:href' => $order->xlink,
				);

				$itemsArray[] = emarket::parseTemplate($tplItem, $item, false, $order->getId());
			}

			return emarket::parseTemplate($tplBlock, array(
				'subnodes:items' => $itemsArray
			));
		}

		/**
		 *  Возвращает ссылку на оформление заказа в соответствии с настройкой модуля магазина - "покупать в 1 шаг"
		 *  @return string
		 */
		public function getPurchaseLink() {
			$umiRegistry = regedit::getInstance();
			$purchaseMethod = ($umiRegistry->getVal('//modules/emarket/purchasing-one-step')) ? 'purchasing_one_step' : 'purchase';
			return $this->module->pre_lang . "/" . cmsController::getInstance()->getUrlPrefix() . "emarket/" . $purchaseMethod;
		}

		/**
		 * Возвращает список валют магазина
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws coreException
		 */
		public function currencySelector($template = 'default') {
			list(
				$tpl_block, $tpl_item, $tpl_item_a
				) = emarket::loadTemplates(
				"emarket/currency/{$template}",
				'currency_block',
				'currency_item',
				'currency_item_a'
			);

			/**
			 * @var iUmiObject|iUmiEntinty $defaultCurrency
			 */
			$defaultCurrency = $this->module->getDefaultCurrency();
			/**
			 * @var iUmiObject|iUmiEntinty $currentCurrency
			 */
			$currentCurrency = $this->module->getCurrentCurrency();
			$items_arr = array();

			/**
			 * @var iUmiObject|iUmiEntinty $currency
			 */
			foreach ($this->module->getCurrencyList() as $currency) {
				$item_arr = array(
					'attribute:id'	=> $currency->getId(),
					'attribute:name' => $currency->getName(),
					'attribute:codename' => $currency->getValue('codename'),
					'attribute:rate' => $currency->getValue('rate'),
					'xlink:href' => $currency->xlink,
				);

				if ($currency->getValue('codename') == $defaultCurrency->getValue('codename')) {
					$item_arr['attribute:default'] = 'default';
				}

				$tpl = ($currentCurrency->getId() == $currency->getId()) ? $tpl_item_a : $tpl_item;
				$items_arr[] = emarket::parseTemplate($tpl, $item_arr, false, $currency->getId());
			}

			$block_arr = array(
				'subnodes:items' => $items_arr
			);

			return emarket::parseTemplate($tpl_block, $block_arr);
		}

		/**
		 * Возвращает список товаров (объектов каталога), добавленных
		 * к сравнению со значениями полей заданных групп
		 * @param string $template имя шаблона (для tpl)
		 * @param string $groups_names строковые идентификатор групп полей,
		 * разделенные пробелом
		 * @return mixed
		 */
		public function compare($template = "default", $groups_names = '') {
			if (!$template) {
				$template = "default";
			}

			list(
				$template_block,
				$template_block_empty,
				$template_block_header,
				$template_block_header_item,
				$template_block_line,
				$template_block_line_item
				) = emarket::loadTemplates(
				"emarket/compare/{$template}",
				"compare_block",
				"compare_block_empty",
				"compare_block_header",
				"compare_block_header_item",
				"compare_block_line",
				"compare_block_line_item"
			);

			$elements = $this->getCompareElements();

			if (count($elements) == 0) {
				return $template_block_empty;
			}

			$hierarchy = umiHierarchy::getInstance();
			$hierarchy->loadElements($elements);
			$umiLinksHelper = umiLinksHelper::getInstance();
			$umiLinksHelper->loadLinkPartForPages($elements);

			$block_arr = array();
			$items = array();
			$headers_arr = array();

			foreach ($elements as $element_id) {
				$element = $hierarchy->getElement($element_id);

				if (!$element instanceof iUmiHierarchyElement) {
					continue;
				}

				$item_arr = array(
					'attribute:id' => $element_id,
					'attribute:link' => $umiLinksHelper->getLinkByParts($element),
					'node:title' => $element->getName()
				);

				$items[] = emarket::parseTemplate($template_block_header_item, $item_arr, $element_id);
			}

			$headers_arr['subnodes:items'] = $items;
			$headers = emarket::parseTemplate($template_block_header, $headers_arr);
			$fields = array();

			foreach ($elements as $element_id) {
				$comparableFields = $this->module->getComparableFields($element_id, $groups_names);
				foreach ($comparableFields as $field) {
					$fields[$field->getName()] = $field;
				}
			}

			$lines = array();
			$iCnt = 0;

			/**
			 * @var iUmiField $field
			 */
			foreach ($fields as $field_name => $field) {
				$field_title = $field->getTitle();
				$items = array();
				$is_void = true;

				foreach ($elements as $element_id) {
					$element = $hierarchy->getElement($element_id);

					$item_arr = array(
						'attribute:id'		=> $element_id,
						'void:name'			=> $field_name,
						'void:field_name'	=> $field_name,
						'value'				=> $element->getObject()->getPropByName($field_name)
					);

					if ($is_void && $element->getValue($field_name)) {
						$is_void = false;
					}

					$items[] = emarket::parseTemplate($template_block_line_item, $item_arr, $element_id);
				}

				if ($is_void) {
					continue;
				}

				$iCnt++;
				$line_arr = array(
					'attribute:title'	=> $field_title,
					'attribute:name'	=> $field_name,
					'attribute:type'	=> $field->getDataType(),
					'attribute:par'		=> intval($iCnt / 2 == ceil($iCnt / 2)),
					'subnodes:values'	=> $line_arr['void:items'] = $items
				);

				$lines[] = emarket::parseTemplate($template_block_line, $line_arr);
			}

			$block_arr['headers'] = $headers;
			$block_arr['void:lines'] = $block_arr['void:fields'] = $lines;
			$block_arr['fields'] = array();
			$block_arr['fields']['nodes:field'] = $lines;

			return emarket::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Возвращает список товаров, добавленных к сравнению
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function getCompareList($template = "default") {
			if (!$template) {
				$template = "default";
			}

			list(
				$template_block, $template_block_empty, $template_block_line, $template_block_link
				) = emarket::loadTemplates(
				"emarket/compare/{$template}",
				"compare_list_block",
				"compare_list_block_empty",
				"compare_list_block_line",
				"compare_list_block_link"
			);

			$block_arr = array();
			$elements = $this->getCompareElements();
			$maxItemsCount = $this->module->iMaxCompareElements;

			if (sizeof($elements) == 0) {
				$block_arr['void:max_elements'] = $maxItemsCount ? $maxItemsCount : getLabel('label-unlimited');

				if ($maxItemsCount) {
					$block_arr['attribute:max-elements'] = $maxItemsCount;
				}

				return emarket::parseTemplate($template_block_empty, $block_arr);
			}

			$items = [];
			$hierarchy = umiHierarchy::getInstance();
			$hierarchy->loadElements($elements);
			$umiLinksHelper = umiLinksHelper::getInstance();
			$umiLinksHelper->loadLinkPartForPages($elements);

			foreach ($elements as $element_id) {
				$el = $hierarchy->getElement($element_id);

				if (!$el instanceof iUmiHierarchyElement) {
					continue;
				}

				$line_arr = array();
				$line_arr['attribute:id'] = $element_id;
				$line_arr['node:value'] = $el->getName();
				$line_arr['attribute:link'] = $umiLinksHelper->getLinkByParts($el);
				$line_arr['xlink:href'] = 'upage://' . $element_id;
				$items[] = emarket::parseTemplate($template_block_line, $line_arr, $element_id);
			}

			$block_arr['compare_link'] = (sizeof($elements) >= 2) ? $template_block_link : "";
			$block_arr['void:max_elements'] = $maxItemsCount ? $maxItemsCount : getLabel('label-unlimited');

			if ($maxItemsCount) {
				$block_arr['attribute:max-elements'] = $maxItemsCount;
			}

			$block_arr['subnodes:items'] = $items;
			return emarket::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Возвращает адреса, по которым можно добавить товар к сравнению и
		 * удалить его из сравнения
		 * @param null|int $elementId
		 * @param string $template
		 * @return mixed|void
		 */
		public function getCompareLink($elementId = null, $template = 'default') {
			if (!$elementId) {
				return;
			}

			if (!$template) {
				$template = "default";
			}

			list($tpl_add_link, $tpl_del_link) = emarket::loadTemplates(
				"emarket/compare/{$template}",
				'add_link',
				'del_link'
			);

			$elements = $this->getCompareElements();
			$inCompare = in_array($elementId, $elements);
			$prefix = $this->module->pre_lang;

			$addLink = $prefix . '/emarket/addToCompare/' . $elementId . '/';
			$delLink = $prefix . '/emarket/removeFromCompare/' . $elementId . '/';
			$block_arr = array(
				'add-link' => $inCompare ? null : $addLink,
				'del-link' => $inCompare ? $delLink : null
			);

			return emarket::parseTemplate(($inCompare ? $tpl_del_link : $tpl_add_link), $block_arr, $elementId);
		}

		/**
		 * Добавляет товар с сранению и перенаправляет не реферер
		 */
		public function addToCompare() {
			$this->add_to_compare(getRequest("param0"));
			$this->module->redirect(getServer('HTTP_REFERER'));
		}

		/**
		 * Добавляет товар с сранению и выводит результат в буффер
		 */
		public function jsonAddToCompareList() {
			$element_id = getRequest("param0");
			list($add_to_compare_tpl, $already_exists_tpl) = emarket::loadTemplates(
				"emarket/compare/default",
				"json_add_to_compare",
				"json_compare_already_exists"
			);

			$template = $this->add_to_compare($element_id) ? $add_to_compare_tpl : $already_exists_tpl;
			$block_arr = array(
				'id' => $element_id
			);

			/** @var HTTPOutputBuffer $buffer */
			$buffer = outputBuffer::current('HTTPOutputBuffer');
			$buffer->contentType('text/javascript');
			$this->module->flush(emarket::parseTemplate($template, $block_arr, $element_id));
		}

		/**
		 * Убирает товар из сравнения и перенаправляет не реферер
		 */
		public function removeFromCompare() {
			$this->remove_from_compare(getRequest("param0"));
			$referrer = getServer('HTTP_REFERER');

			if (stristr(getServer('HTTP_USER_AGENT'), 'msie')) {
				$referrer = preg_replace(array("/\b\d{10,}\b/", "/&{2,}/", "/&$/"), array("", "&", ""), $referrer);
				$referrer.= (strstr($referrer, "?") ? "&" : "?") . time();
				$referrer = str_replace("?&", "?", $referrer);
			}

			$this->module->redirect($referrer);
		}

		/**
		 * Убирает товар из сравнения и выводит результат в буффер
		 */
		public function jsonRemoveFromCompare() {
			$element_id = getRequest("param0");
			$this->remove_from_compare($element_id);

			list($template) = emarket::loadTemplates(
				"emarket/compare/default",
				"json_remove_from_compare"
			);

			$block_arr = array(
				'id' => $element_id
			);

			/** @var HTTPOutputBuffer $buffer */
			$buffer = outputBuffer::current('HTTPOutputBuffer');
			$buffer->contentType('text/javascript');
			$buffer->charset('utf-8');
			$this->module->flush($template, $block_arr, $element_id);
		}

		/**
		 * Очищает список товаров, добавленных к сравнению,
		 * и перенаправляет на реферер
		 */
		public function resetCompareList() {
			$this->reset_compare();
			$this->module->redirect(getServer('HTTP_REFERER'));
		}

		/**
		 * Очищает список товаров, добавленных к сравнению,
		 * и выводи результат в буффер
		 */
		public function jsonResetCompareList() {
			$this->reset_compare();

			list($template) = emarket::loadTemplates(
				"emarket/compare/default",
				"json_reset_compare_list"
			);

			/** @var HTTPOutputBuffer $buffer */
			$buffer = outputBuffer::current('HTTPOutputBuffer');
			$buffer->contentType('text/javascript');
			$buffer->charset('utf-8');
			$this->module->flush($template);
		}

		/**
		 * Возвращает данные для вывода личного кабинета покупателя
		 * @param string $template имя шаблона (для tpl)
		 * @param int|bool $customerId ID покупателя владельца личного кабинета
		 * @param string|bool $checkSum Контрольная сумма для верификации покупателя
		 * @return array
		 */
		public function personal($template = 'default', $customerId = false, $checkSum = false) {
			$customer = null;

			if ($customerId !== false && $checkSum !== false) {
				$correctCheckSum = $this->module->getCheckSum($customerId);

				if ($correctCheckSum === $checkSum) {
					$customer = customer::get(false, $customerId);
				}
			}

			if (!$customer) {
				$customer = customer::get();
			}

			$data = array(
				'customer' => array(
					'@id' =>  $customer->getId()
				)
			);

			list($tpl_block) = emarket::loadTemplates(
				"emarket/" . $template,
				"personal"
			);

			return emarket::parseTemplate($tpl_block, $data);
		}

		/**
		 * Устанавливает покупателю предпочитаемую валюту.
		 * У зарегистрированного покупателя она хранится поле 'preffered_currency'
		 * объекта пользователя, у незарегистрированного в cookie 'customer_currency'.
		 * После операции перенаправляет на реферер.
		 * @throws coreException
		 * @throws privateException
		 */
		public function selectCurrency() {
			$currencyCode = getRequest('currency-codename');
			/**
			 * @var iUmiObject|iUmiEntinty $selectedCurrency
			 */
			$selectedCurrency = $this->module->getCurrency($currencyCode);

			if ($currencyCode && $selectedCurrency instanceof iUmiObject) {
				$selectedCurrencyId = $selectedCurrency->getId();
				/**
				 * @var iUmiObject|iUmiEntinty $defaultCurrency
				 */
				$defaultCurrency = $this->module->getDefaultCurrency();
				$defaultCurrencyId = $defaultCurrency->getId();

				if (permissionsCollection::getInstance()->isAuth()){
					$customer = customer::get();

					if ($customer->preffered_currency != $selectedCurrencyId) {

						if ($selectedCurrencyId == $defaultCurrencyId) {
							$customer->preffered_currency = null;
						} else {
							$customer->preffered_currency = $selectedCurrencyId;
						}

						$customer->commit();
					}

				} else {
					\UmiCms\Service::CookieJar()
						->set('customer_currency', $selectedCurrencyId, time() + customer::$defaultExpiration);
				}
			}

			if ($redirectUri = getRequest('redirect-uri')) {
				$this->module->redirect($redirectUri);
			} else {
				$this->module->redirect(getServer('HTTP_REFERER'));
			}
		}

		/**
		 * Убирает адрес доставки из списка адресов покупателя.
		 * Если адрес не использует ни в одном заказе, то
		 * адрес удаляется.
		 * @param bool|int $addressId идентификатор адреса доставки
		 * @throws coreException
		 * @throws publicException
		 * @throws selectorException
		 */
		public function removeDeliveryAddress($addressId = false) {
			if (!$addressId) {
				$addressId = getRequest("param0");
			}

			$addressId = (int) $addressId;
			$collection = umiObjectsCollection::getInstance();

			if (!$collection->isExists($addressId)) {
				throw new publicException("Wrong address id passed");
			}

			$customer = customer::get();
			$addresses = $customer->getValue('delivery_addresses');
			$addressKey = array_search($addressId, $addresses);

			if (!is_bool($addressKey)) {
				unset($addresses[$addressKey]);
				$customer->setValue('delivery_addresses', $addresses);
				$customer->commit();
			}

			$sel = new selector("objects");
			$sel->types("hierarchy-type")->name("emarket", "order");
			$sel->where("delivery_address")->equals($addressId);
			$sel->option('no-length')->value(true);

			if (!$sel->first instanceof iUmiObject) {
				$collection->delObject($addressId);
			}

			$this->module->redirect(getServer("HTTP_REFERER"));
		}

		/**
		 * Возвращает список товаров (объектов каталога), добавленных к сравнению
		 * @return int[]|bool|null
		 */
		public function getCompareElements() {
			static $elements;

			if (is_array($elements)) {
				return $elements;
			}

			$session = \UmiCms\Service::Session();
			$compareList = $session->get('compare_list');
			$compareList = (is_array($compareList)) ? $compareList : [];

			if (is_array(getRequest('compare_list'))) {
				$compareList = getRequest('compare_list');
			}

			$session->set('compare_list', $compareList);

			$elements = $session->get('compare_list');
			return (is_array($elements)) ? array_unique($elements) : [];
		}

		/**
		 * Добавляет товар (объект каталога) в список сравниваемых товаров
		 * @param int $element_id идентификатор товара
		 * @return bool
		 * @throws coreException
		 * @throws errorPanicException
		 * @throws privateException
		 */
		public function add_to_compare($element_id) {
			$session = \UmiCms\Service::Session();
			$compareList = $session->get('compare_list');
			$compareList = (is_array($compareList)) ? $compareList : [];

			/**
			 * @var emarket $module
			 */
			$module = $this->module;

			if ($module->iMaxCompareElements && count($compareList) >= $module->iMaxCompareElements) {
				$module->errorNewMessage("%errors_max_items_compare%");
				$module->errorPanic();
			}

			$oEventPoint = new umiEventPoint("emarket_add_to_compare");
			$oEventPoint->setMode("before");
			$oEventPoint->setParam("element_id", $element_id);
			$oEventPoint->setParam("compare_list", $compareList);
			emarket::setEventPoint($oEventPoint);
			$result =  false;

			if (!in_array($element_id, $compareList)) {
				$compareList[] = $element_id;
				$oEventPoint = new umiEventPoint("emarket_add_to_compare");
				$oEventPoint->setMode("after");
				$oEventPoint->setParam("element_id", $element_id);
				$oEventPoint->setParam("compare_list", $compareList);
				emarket::setEventPoint($oEventPoint);
				$result = true;
			}

			$session->set('compare_list', $compareList);
			return $result;
		}

		/**
		 * Удаляет товар (объект каталога) из списка сравниваемых товаров
		 * @param int $element_id идентификатор товара
		 */
		public function remove_from_compare($element_id) {
			$session = \UmiCms\Service::Session();
			$compareList = $session->get('compare_list');
			$compareList = (is_array($compareList)) ? $compareList : [];

			if (in_array($element_id, $compareList)) {
				$key = array_search($element_id, $compareList);
				unset($compareList[$key]);
				$session->set('compare_list', $compareList);
			}
		}

		/**
		 * Очищает список сравниваемых товаров
		 */
		public function reset_compare() {
			\UmiCms\Service::Session()->set('compare_list', []);
		}

		/**
		 * Обрабатывает запрос от платежной системы
		 * @return mixed
		 * @throws publicException
		 */
		public function gateway() {
			if ($error = getRequest('err_msg')) {
				$error = $error[0];
				$error = iconv("windows-1251", "utf-8", urldecode($error));
				cmsController::getInstance()->errorUrl = "/emarket/ordersList/";
				$this->module->errorNewMessage($error);
			}

			$orderId = payment::getResponseOrderId();

			if (!$orderId) {
				throw new publicException("Couldn't receive the order id from the payment system");
			}

			$order = order::get($orderId);

			if ($order instanceof order === false) {
				throw new publicException("Order #{$orderId} doesn't exist");
			}

			$paymentId = $order->getValue('payment_id');

			if (!$paymentId) {
				throw new publicException("No payment method inited for order #{$orderId}");
			}

			/**
			 * @var payment $payment
			 */
			$payment = payment::get($paymentId, $order);
			return $payment->poll();
		}

		/**
		 * Возвращает адрес доставки заказа
		 * @param string $template имя шаблона (для tpl)
		 * @param null|int $orderId идентификатор заказа (если не передать - возьмет текущий)
		 * @return mixed
		 * @throws publicAdminException
		 */
		public function getOrderDeliveryAddress($template = 'default', $orderId = null) {
			$order = (is_null($orderId)) ? $this->module->getBasketOrder() : order::get($orderId);

			if (!$order instanceof order) {
				throw new publicAdminException('Wrong order id given');
			}

			$addressId = $order->getDeliveryAddressId();
			$address = Address\AddressFactory::createByObjectId($addressId);

			$result = [
				'result' => [
					'country' => $address->getCountry(),
					'country_iso_code' => $address->getCountryISOCode(),
					'index' => $address->getPostIndex(),
					'region' => $address->getRegion(),
					'city' => $address->getCity(),
					'street' => $address->getStreet(),
					'house' => $address->getHouseNumber(),
					'flat' => $address->getFlatNumber(),
					'order_comments' => $address->getComment()
				]
			];

			list($block) = emarket::loadTemplates(
				'emarket/' . $template,
				'delivery_address'
			);

			return emarket::parseTemplate($block, $result, false, $addressId);
		}

		/**
		 * Возвращает данные заказа
		 * @param bool $orderId идентификатор заказа
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws publicException
		 */
		public function order($orderId = false, $template = 'default') {
			if (!$template) {
				$template = 'default';
			}

			$permissions = permissionsCollection::getInstance();
			$orderId = (int) ($orderId ? $orderId : getRequest('param0'));

			if (!$orderId) {
				throw new publicException("You should specify order id");
			}

			$order = order::get($orderId);

			if ($order instanceof order == false) {
				throw new publicException("Order #{$orderId} doesn't exist");
			}

			$auth = UmiCms\Service::Auth();

			if (
				!$permissions->isSv() &&
				($order->getName() !== 'dummy') &&
				(customer::get()->getId() != $order->customer_id) &&
				!$permissions->isAllowedMethod($auth->getUserId(), "emarket", "control")
			) {
				throw new publicException(getLabel('error-require-more-permissions'));
			}

			list($tpl_block, $tpl_block_empty) = emarket::loadTemplates(
				"emarket/" . $template,
				'order_block',
				'order_block_empty'
			);

			$discount = $order->getDiscount();
			$totalAmount = $order->getTotalAmount();
			$originalPrice = $order->getOriginalPrice();
			$actualPrice = $order->getActualPrice();
			$deliveryPrice = $order->getDeliveryPrice();
			$bonusDiscount = $order->getBonusDiscount();

			if ($originalPrice == $actualPrice) {
				$originalPrice = null;
			}

			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			$discountAmount = ($originalPrice) ? $originalPrice + $deliveryPrice - $actualPrice - $bonusDiscount : 0;
			$steps = null;

			if (cmsController::getInstance()->getCurrentMode() != 'admin') {
				/**
				 * @var emarket|EmarketMacros|EmarketPurchasingStages $module
				 */
				$steps = $module->getPurchaseSteps($template, null);
			}

			$result = array(
				'attribute:id'	=> ($orderId),
				'xlink:href'	=> ('uobject://' . $orderId),
				'customer'		=> ($order->getName() == 'dummy') ? null : $module->renderOrderCustomer($order, $template),
				'subnodes:items'=> ($order->getName() == 'dummy') ? null : $module->renderOrderItems($order, $template),
				'delivery'		=> $module->renderOrderDelivery($order, $template),
				'summary'		=> array(
					'amount'		=> $totalAmount,
					'price'			=> $module->formatCurrencyPrice(array(
						'original'		=> $originalPrice,
						'delivery'		=> $deliveryPrice,
						'actual'		=> $actualPrice,
						'discount'		=> $discountAmount,
						'bonus'			=> $bonusDiscount
					))
				),
				'discount_value' => $order->getDiscountValue(),
				'steps' => $steps
			);

			if ($order->number) {
				$result['number'] = $order->number;
				$result['status'] = selector::get('object')->id($order->status_id);
			}

			if (!arrayValueContainsNotEmptyArray($result, 'subnodes:items')) {
				$tpl_block = $tpl_block_empty;
			}

			$result['void:total-price'] = $module->parsePriceTpl($template, $result['summary']['price']);
			$result['void:delivery-price'] = $module->parsePriceTpl($template, $module->formatCurrencyPrice(
				array('actual' => $deliveryPrice)
			));

			$result['void:bonus'] = $module->parsePriceTpl($template, $module->formatCurrencyPrice(
				array('actual' => $bonusDiscount)
			));

			$result['void:total-amount'] = $totalAmount;
			$result['void:discount_id'] = false;

			if ($discount instanceof discount) {
				$result['discount'] = array(
					'attribute:id'		=> $discount->id,
					'attribute:name'	=> $discount->getName(),
					'description'		=> $discount->getValue('description')
				);
				$result['void:discount_id'] = $discount->id;
			}

			return emarket::parseTemplate($tpl_block, $result, false, $order->id);
		}

		/**
		 * Возвращает оформленные цены
		 * @param string $template имя шаблона (для tpl)
		 * @param array $priceData значения цен
		 * @return array
		 */
		public function parsePriceTpl($template = 'default', $priceData = array()) {
			if (emarket::isXSLTResultMode()) {
				return $priceData;
			}

			list($tpl_original, $tpl_actual) = emarket::loadTemplates(
				"emarket/" . $template,
				'price_original',
				'price_actual'
			);

			$originalPrice = getArrayKey($priceData, 'original');
			$actualPrice = getArrayKey($priceData, 'actual');

			$result = array();
			$result['original'] = emarket::parseTemplate(($originalPrice ? $tpl_original : ''), $priceData);
			$result['actual'] = emarket::parseTemplate(($actualPrice ? $tpl_actual : ''), $priceData);
			return $result;
		}

		/**
		 * Формирует цену и пересчитывает ее разные валюты
		 * @param int|float $price цены
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function applyPriceCurrency($price = 0, $template = 'default') {
			list($tpl_block) = emarket::loadTemplates(
				"emarket/{$template}",
				'price_block'
			);

			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			$price = $module->parsePriceTpl($template, $module->formatCurrencyPrice(array(
				'actual' => $price
			)));

			$result = array(
				'price' => $price
			);
			$result['void:price-original'] = getArrayKey($result['price'], 'original');
			$result['void:price-actual'] = getArrayKey($result['price'], 'actual');

			return emarket::parseTemplate($tpl_block, $result);
		}

		/**
		 * Пересчитывает каждую цену в валюту и возвращает список полученных цен
		 * @param array $prices список цен
		 * @param iUmiObject $currency валюта, в которую требуется пересчитать цены
		 * @param iUmiObject $defaultCurrency валюта по умолчанию
		 * @return array
		 * @throws coreException
		 */
		public function formatCurrencyPrice($prices, iUmiObject $currency = null, iUmiObject $defaultCurrency = null) {
			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;

			if (is_null($defaultCurrency)) {
				/**
				 * @var iUmiObject|iUmiEntinty $defaultCurrency
				 */
				$defaultCurrency = $module->getDefaultCurrency();
			}

			/**
			 * @var iUmiObject|iUmiEntinty $currentCurrency
			 */
			$currentCurrency = $module->getCurrentCurrency();

			/**
			 * @var iUmiObject|iUmiEntinty $currency
			 */
			if (is_null($currency)) {
				$currency = $currentCurrency;
			} else {
				if (($currency->getId() == $currentCurrency->getId()) && ($defaultCurrency == $module->getDefaultCurrency())) {
					return $prices;
				}
			}

			$result = array(
				'attribute:name' => $currency->getName(),
				'attribute:code' => $currency->getValue('codename'),
				'attribute:rate' => $currency->getValue('rate'),
				'attribute:nominal' => $currency->getValue('nominal'),
				'void:currency_name' => $currency->getValue('name')
			);

			if ($currency->getValue('prefix')) {
				$result['attribute:prefix'] = $currency->getValue('prefix');
			} else {
				$result['void:prefix'] = false;
			}

			if ($currency->getValue('suffix')) {
				$result['attribute:suffix'] = $currency->getValue('suffix');
			} else {
				$result['void:suffix'] = false;
			}

			foreach ($prices as $key => $price) {
				if ($price == null) {
					$result[$key] = null;
					continue;
				}

				$price = $price * $defaultCurrency->getValue('nominal') * $defaultCurrency->getValue('rate');
				$price = $price  / $currency->getValue('rate') / $currency->getValue('nominal');
				$result[$key] = round($price, 2);
			}

			return $result;
		}

		/**
		 * Возвращает данные о адресе или способе доставки заказа
		 * @param order $order заказ
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function renderOrderDelivery(order $order, $template = 'default') {
			$objectsCollection = umiObjectsCollection::getInstance();

			list($tpl, $tplMethod, $tplAddress, $tplPrice) = emarket::loadTemplates(
				'emarket/' . $template,
				'order_delivery',
				'delivery_method',
				'delivery_address',
				'delivery_price'
			);

			$result = array();
			$method = $objectsCollection->getObject($order->delivery_id);

			if ($method instanceof iUmiObject == false) {
				return emarket::parseTemplate($tpl, $result);
			}

			$deliveryMethod = array(
				'attribute:id' => $method->getId(),
				'attribute:name' => $method->getName(),
				'xlink:href' => ('uobject://' . $method->getId()),
			);

			$result['method'] = emarket::parseTemplate($tplMethod, $deliveryMethod);

			/**
			 * @var umiObject $address
			 */
			$address = $objectsCollection->getObject($order->getValue('delivery_address'));

			if ($address instanceof iUmiObject) {
				$country = $objectsCollection->getObject($address->getValue('country'));
				$countryName = $country instanceof iUmiObject ? $country->getName() : '';
				$deliveryAddress = array(
					'attribute:id' => $address->getId(),
					'attribute:name' => $address->getName(),
					'xlink:href' => ('uobject://' . $address->getId()),
					'country' => $countryName,
					'index' => $address->getValue('index'),
					'region' => $address->getValue('region'),
					'city' => $address->getValue('city'),
					'street' => $address->getValue('street'),
					'house' => $address->getValue('house'),
					'flat' => $address->getValue('flat'),
					'comment' => $address->getValue('order_comments'),
				);
				$result['address'] = emarket::parseTemplate($tplAddress, $deliveryAddress);
			}

			$result['price'] = emarket::parseTemplate($tplPrice, $this->formatCurrencyPrice(array(
				'delivery' => $order->getValue('delivery_price')
			)));

			return emarket::parseTemplate($tpl, $result);
		}

		/**
		 * Возвращает данные покупателя
		 * @param order $order заказ покупателя
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws publicException
		 */
		public function renderOrderCustomer(order $order, $template = 'default') {
			$customer = selector::get('object')->id($order->customer_id);

			if ($customer instanceof iUmiObject == false) {
				throw new publicException(getLabel('error-object-does-not-exist', null, $order->customer_id));
			}

			/**
			 * @var iUmiObject|iUmiEntinty $customer
			 */
			list($tpl_user, $tpl_guest) = emarket::loadTemplates(
				"emarket/customer/" . $template,
				"customer_user",
				"customer_guest"
			);

			/**
			 * @var iUmiObjectType $objectType
			 */
			$objectType = selector::get('object-type')->id($customer->getTypeId());
			$tpl = ($objectType->getModule() == 'users') ? $tpl_user : $tpl_guest;

			return emarket::parseTemplate($tpl, array(
				'full:object' => $customer
			), false, $customer->getId());
		}

		/**
		 * Возвращает данные списка товаров заказа
		 * @param order $order заказ
		 * @param string $template имя шаблона (для tpl)
		 * @return array
		 */
		public function renderOrderItems(order $order, $template = 'default') {
			$items_arr = array();
			$objects = umiObjectsCollection::getInstance();

			list($tpl_item, $tpl_options_block, $tpl_options_block_empty, $tpl_options_item) = emarket::loadTemplates(
				"emarket/" . $template,
				'order_item',
				'options_block',
				'options_block_empty',
				'options_item'
			);

			$orderItems = $order->getItems();

			if (count($orderItems) == 0) {
				return emarket::parseTemplate($tpl_options_block_empty, array());
			}

			/**
			 * @var emarket|EmarketMacros $module
			 */
			$module = $this->module;
			$isBasket = emarket::isBasket($order);
			/**
			 * @var orderItem $orderItem
			 */
			foreach ($orderItems as $orderItem) {
				$orderItemId = $orderItem->getId();

				$item_arr = array(
					'attribute:id'	=> $orderItemId,
					'attribute:name'=> htmlspecialchars($orderItem->getName()),
					'xlink:href'	=> 'uobject://' . $orderItemId,
					'amount'		=> $orderItem->getAmount(),
					'options'		=> null
				);

				$plainPriceOriginal = $orderItem->getItemPrice();

				if ($isBasket) {
					$itemDiscount = $orderItem->getDiscount();
					$plainPriceActual = ($itemDiscount instanceof itemDiscount) ? $itemDiscount->recalcPrice($plainPriceOriginal) : $plainPriceOriginal;
					$pricesDiff = ($plainPriceOriginal - $plainPriceActual);
					$discountValue = ($pricesDiff < 0) ? 0 : $pricesDiff;
				} else {
					$discountValue = $orderItem->getDiscountValue();
					$plainPriceActual = $plainPriceOriginal - $discountValue;
				}

				$totalPriceOriginal = $orderItem->getTotalOriginalPrice();
				$totalPriceActual = $orderItem->getTotalActualPrice();

				if ($plainPriceOriginal == $plainPriceActual) {
					$plainPriceOriginal = null;
				}

				if ($totalPriceOriginal == $totalPriceActual) {
					$totalPriceOriginal = null;
				}

				$item_arr['price'] = $module->formatCurrencyPrice(array(
					'original'	=> $plainPriceOriginal,
					'actual'	=> $plainPriceActual
				));

				$item_arr['total-price'] = $module->formatCurrencyPrice(array(
					'original'	=> $totalPriceOriginal,
					'actual'	=> $totalPriceActual
				));

				$item_arr['price'] = $module->parsePriceTpl($template, $item_arr['price']);
				$item_arr['total-price'] = $module->parsePriceTpl($template, $item_arr['total-price']);
				$item_arr['discount_value'] = (float) $discountValue;
				$item_arr['weight'] = (int) $orderItem->getWeight();

				$status = order::getCodeByStatus($order->getOrderStatus());

				if (!$status || $status == 'basket') {
					$element = $orderItem->getItemElement();
				} else {
					$symlink = $orderItem->getObject()->getValue('item_link');

					if (is_array($symlink) && sizeof($symlink)) {
						list($item) = $symlink;
						$element = $item;
					} else {
						$element = null;
					}
				}

				/**
				 * @var iUmiHierarchyElement|iUmiEntinty $element
				 */
				if ($element instanceof iUmiHierarchyElement) {
					$item_arr['page'] = $element;
					$item_arr['void:element_id'] = $element->getId();
					$item_arr['void:link'] = $element->link;
				}

				$discountAmount = $totalPriceOriginal ? $totalPriceOriginal - $totalPriceActual : 0;
				$discount = $orderItem->getDiscount();

				if ($discount instanceof itemDiscount) {
					$item_arr['discount'] = array(
						'attribute:id' => $discount->getId(),
						'attribute:name' => $discount->getName(),
						'description' => $discount->getValue('description'),
						'amount' => $discountAmount
					);
					$item_arr['void:discount_id'] = $discount->getId();
				}

				$elementId = ($element instanceof iUmiHierarchyElement) ? $element->getId() : null;

				if ($orderItem instanceof optionedOrderItem) {
					/**
					 * @var optionedOrderItem $orderItem
					 */
					$options = $orderItem->getOptions(); $options_arr = array();

					foreach ($options as $optionInfo) {
						$optionId = $optionInfo['option-id'];
						$price = $optionInfo['price'];
						$fieldName = $optionInfo['field-name'];
						$option = $objects->getObject($optionId);
						if ($option instanceof iUmiObject) {
							$option_arr = array(
								'attribute:id' => $optionId,
								'attribute:name' => $option->getName(),
								'attribute:price' => $price,
								'attribute:field-name' => $fieldName,
								'attribute:element_id' => $elementId,
								'xlink:href'=> ('uobject://' . $optionId)
							);

							$options_arr[] = emarket::parseTemplate($tpl_options_item, $option_arr, false, $optionId);
						}
					}

					$item_arr['options'] = emarket::parseTemplate($tpl_options_block, array(
						'nodes:option' => $options_arr,
						'void:items' => $options_arr
					));
				}

				$items_arr[] = emarket::parseTemplate($tpl_item, $item_arr);
			}

			return $items_arr;
		}

	}
