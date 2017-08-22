<?php

	/**
	 * Базовый класс заказа.
	 * Одновременно является контейнером данных объекта-источника для заказа
	 * и предоставляет интерфейс для создания и получения заказа.
	 *
	 * В UMI.CMS заказ это собственно заказ и корзина покупателя.
	 * Заказ от корзины отличается наличием номера и статуса.
	 */
	class order extends umiObjectProxy {

		/** @var orderItem[] $items товарные наименования в заказе */
		protected $items = [];

		/** @var float $actualPrice цена заказа с учетом скидок */
		protected $actualPrice;

		/** @var float $originalPrice оригинальная цена заказа */
		protected $originalPrice;

		/** @var int $totalAmount количество товарных наименований в заказе */
		protected $totalAmount;

		/** @var discount $discount примененная скидка */
		protected $discount;

		/** @var iDomain $domain домен, на котором был создан заказа */
		protected $domain;

		/** @var float $discountValue абсолютное значение скидки заказа */
		protected $discountValue;

		/** @var \UmiCms\Classes\Components\Emarket\Orders\Calculator калькулятора данных заказа */
		protected $calculator;

		/** @var EmarketSettings экземпляр класса управления настройками */
		protected $settings;

		/**
		 * @const string гуид поля объекта-источника для заказа,
		 * в котором хранится абсолютное значение скидки заказа
		 */
		const ORDER_DISCOUNT_VALUE_FIELD_GUID = 'order_discount_value';

		/** @const имя поля номера заказа */
		const ORDER_NUMBER = 'number';

		/** @const имя поля общего веса */
		const TOTAL_WEIGHT_FIELD = 'total_weight';

		/** @const имя поля общей ширины */
		const TOTAL_WIDTH_FIELD = 'total_width';

		/** @const имя поля общей высоты */
		const TOTAL_HEIGHT_FIELD = 'total_height';

		/** @const имя поля общей длины */
		const TOTAL_LENGTH_FIELD = 'total_length';

		/** @const имя поля ссылки на адрес доставки */
		const DELIVERY_ADDRESS_FIELD = 'delivery_address';

		/** @const имя поля стоимости доставки */
		const DELIVERY_PRICE_FIELD = 'delivery_price';

		/** @const имя поля даты доставки клиенту */
		const DELIVERY_DATE_FIELD = 'delivery_date';

		/** @const имя поля оператора доставки */
		const DELIVERY_PROVIDER_FIELD = 'delivery_provider';

		/** @const имя поля тарифа доставки */
		const DELIVERY_TARIFF_FIELD = 'delivery_tariff';

		/** @const имя поля типа доставки клиенту */
		const DELIVERY_TYPE_FIELD = 'delivery_type';

		/** @const имя поля точки приема товара */
		const DELIVERY_POINT_IN_FIELD = 'delivery_point_in';

		/** @const имя поля точки выдачи товара */
		const DELIVERY_POINT_OUT_FIELD = 'delivery_point_out';

		/** @const имя поля типа доставки оператору доставки */
		const PICKUP_TYPE_FIELD = 'pickup_type';

		/** @const имя поля даты доставки оператору доставки */
		const PICKUP_DATE_FIELD = 'pickup_date';

		/** @const имя поля идентификатор покупателя заказа */
		const CUSTOMER_ID_FIELD = 'customer_id';

		/**
		 * Возвращает заказа по id его объекта-источника.
		 * Если id заказа false, то метод вернет текущую корзину.
		 * Если такого объекта еще нет, то он его создаст
		 * @param bool|int $orderId = false id заказа
		 * @param bool $ignoreCache нужно ли игнорировать кэш при получении объекта заказа
		 * @return null|order
		 * @throws publicException
		 */
		public static function get($orderId = false, $ignoreCache = false) {
			static $cache = [];

			if (!$orderId) {
				return $object = self::create();
			}

			if (isset($cache[$orderId]) && !$ignoreCache) {
				return $cache[$orderId];
			}

			$objects = umiObjectsCollection::getInstance();
			$object = $objects->getObject($orderId);

			if (!$object instanceof iUmiObject) {
				return null;
			}

			return $cache[$orderId] = new order($object);
		}

		/**
		 * @internal
		 * Возвращает заказ по его номеру
		 * @param int $number номер заказа
		 * @return null|order
		 * @throws selectorException
		 */
		public static function getByNumber($number) {
			$sel = new selector('objects');
			$sel->types('object-type')->guid('emarket-order');
			$sel->where('number')->equals($number);
			$sel->option('no-length')->value(true);
			$sel->limit(0, 1);
			$order = $sel->first;

			if (!$order instanceof iUmiObject) {
				return null;
			}

			/** @var iUmiObject $order */
			return new order($order);
		}

		/**
		 * Создает новый заказ и возвращает его id
		 * @param bool $useDummyOrder использовать заказ-заглушку вместо создания заказа
		 * @param bool|int $domainId идентификатор домена, к которому будет относится заказ
		 * Если не передан - возьмет текущий домен
		 * @return null|order
		 * @throws coreException
		 * @throws publicException
		 * @throws selectorException
		 */
		public static function create($useDummyOrder = false, $domainId = false) {
			$objectTypes = umiObjectTypesCollection::getInstance();
			$objects = umiObjectsCollection::getInstance();

			if ($domainId === false) {
				$cmsController = cmsController::getInstance();
				$domain = $cmsController->getCurrentDomain();
				$domainId = $domain->getId();
			}

			$orderTypeId = $objectTypes->getTypeIdByGUID('emarket-order');

			if ($useDummyOrder) {
				$sel = new selector('objects');
				$sel->types('object-type')->id($orderTypeId);
				$sel->where('name')->equals('dummy');
				$sel->option('no-length')->value(true);
				$sel->option('return')->value('id');
				$sel->limit(0, 1);
				$result = $sel->result();

				if (count($result) > 0 && isset($result[0]['id'])) {
					$orderId = $result[0]['id'];
				} else {
					$orderId = $objects->addObject('dummy', $orderTypeId);
					$order = $objects->getObject($orderId);

					if ($order instanceof iUmiObject == false) {
						throw new publicException("Can't load dummy object for order #{$orderId}");
					} else {
						$order->setValue('domain_id', $domainId);
						$order->commit();
					}
				}

				return self::get($orderId);
			}

			$managerId = 0;
			$statusId = self::getStatusByCode('basket');
			$customer = customer::get();
			$createTime = time();

			$orderId = $objects->addObject('', $orderTypeId);
			$order = $objects->getObject($orderId);

			if ($order instanceof iUmiObject == false) {
				throw new publicException("Can't load created object for order #{$orderId}");
			}

			$order->domain_id = $domainId;
			$order->manager_id = $managerId;
			$order->status_id = $statusId;
			$order->customer_id = $customer->getId();
			$order->order_create_date = $createTime;
			$order->commit();
			$customer->setLastOrder($orderId, $domainId);

			return self::get($orderId);
		}

		/**
		 * Возвращает идентификатор статуса заказа по его коду
		 * @param string $codename код статуса
		 * @param string $statusClass идентификатор поля, которое хранит статус (заказа, доставки или оплаты)
		 * @return bool
		 * @throws selectorException
		 */
		public static function getStatusByCode($codename, $statusClass = 'order_status') {
			static $cache = [];

			if (isset($cache[$codename][$statusClass])) {
				return $cache[$codename][$statusClass];
			}

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', $statusClass);
			$sel->where('codename')->equals($codename);
			$sel->option('no-length')->value(true);

			return $cache[$codename][$statusClass] = $sel->first ? $sel->first->id : false;
		}

		/**
		 * Возвращает код статуса по id статуса
		 * @param int $id идентификатор статуса
		 * @return bool
		 */
		public static function getCodeByStatus($id) {
			static $cache = [];

			if (isset($cache[$id])) {
				return $cache[$id];
			}

			/** @var iUmiObject $status */
			$status = selector::get('object')->id($id);
			return $cache[$id] = $status ? $status->getValue('codename') : false;
		}

		/**
		 * Возвращает список товарных наименования заказа
		 * @return orderItem[]
		 */
		public function getItems() {
			return $this->items;
		}

		/**
		 * Добавляет товарное наименование в заказ
		 * @param orderItem $orderItem товарное наименование
		 * @return bool
		 */
		public function appendItem(orderItem $orderItem) {
			foreach ($this->items as $item) {
				if ($item->getId() == $orderItem->getId()) {
					$this->saveTotalProperties();
					return false;
				}
			}

			$orderItem->refresh();
			$this->items[] = $orderItem;
			$this->saveTotalProperties();
		}

		/**
		 * Удаляет товарное наименование из заказа
		 * @param orderItem $orderItem товарное наименование
		 * @return bool
		 */
		public function removeItem(orderItem $orderItem) {
			foreach ($this->items as $i => $item) {
				if ($item instanceof orderItem && $item->getId() == $orderItem->getId()) {
					unset($this->items[$i]);
					$orderItem->remove();
					$this->saveTotalProperties();
					return true;
				}
			}

			return false;
		}

		/**
		 * Возвращает товарное наименование заказа по его id
		 * @param int $itemId идентификатор товарного наименования
		 * @return bool|orderItem
		 */
		public function getItem($itemId) {
			foreach ($this->items as $item) {
				if ($item->getId() == $itemId) {
					return $item;
				}
			}

			return false;
		}

		/**
		 * Есть товарные наименования в заказе
		 * @return bool
		 */
		public function isEmpty() {
			return (sizeof($this->items) == 0);
		}

		/**
		 * Удаляет товарные наименования в заказе
		 */
		public function earse() {
			foreach ($this->items as $item) {
				$item->delete();
			}

			$this->items = [];
		}

		/**
		 * Возвращает идентификатор текущего статуса заказа
		 * @return int|null
		 */
		public function getOrderStatus() {
			return $this->getValue('status_id');
		}

		/**
		 * Устанавливает статус заказа
		 * @param int|string $newStatusId идентификатор или код статуса заказа
		 * @throws coreException
		 */
		public function setOrderStatus($newStatusId) {
			if ($newStatusId && !is_numeric($newStatusId)) {
				$newStatusId = self::getStatusByCode($newStatusId, 'order_status');

				if (!is_numeric($newStatusId)) {
					return;
				}
			}

			$oldStatusId = $this->getOrderStatus();

			$event = new umiEventPoint('order-status-changed');
			$event->addRef('order', $this);
			$event->setParam('old-status-id', $oldStatusId);
			$event->setParam('new-status-id', $newStatusId);

			$isStatusChanged = ($oldStatusId != $newStatusId);

			if (!$isStatusChanged) {
				return;
			}

			$event->setMode('before');
			$event->call();

			$this->object->setValue('status_id', $newStatusId);

			$event->setMode('after');
			$event->call();
		}

		/**
		 * Возвращает идентификатор статуса оплаты заказа
		 * @return int|null
		 */
		public function getPaymentStatus() {
			return $this->object->getValue('payment_status_id');
		}

		/**
		 * Устанавливает статус оплаты заказа
		 * @param int|string $newStatusId идентификатор или код статуса оплаты
		 * @throws coreException
		 */
		public function setPaymentStatus($newStatusId) {
			if ($newStatusId && !is_numeric($newStatusId)) {
				$statusCode = $newStatusId;
				$newStatusId = self::getStatusByCode($newStatusId, 'order_payment_status');
			} else {
				$statusCode = self::getCodeByStatus($newStatusId);
			}

			$oldStatusId = $this->object->getValue('payment_status_id');

			$event = new umiEventPoint('order-payment-status-changed');
			$event->addRef('order', $this);
			$event->setParam('old-status-id', $oldStatusId);
			$event->setParam('new-status-id', $newStatusId);

			$isStatusChanged = ($oldStatusId != $newStatusId);

			if (!$isStatusChanged) {
				return;
			}

			$event->setMode('before');
			$event->call();

			$this->object->setValue('payment_status_id', $newStatusId);

			$event->setMode('after');
			$event->call();

			switch ($statusCode) {
				case 'initialized' : {
					$this->setOrderStatus('payment');
					break;
				}
				case 'declined' : {
					$this->setOrderStatus('execution');
					break;
				}
				case 'accepted' : {
					$this->object->setValue('payment_date', new umiDate());
					$this->order();
					break;
				}
			}
		}

		/**
		 * Возвращает идентификатор статус доставки заказа
		 * @return int|null
		 */
		public function getDeliveryStatus() {
			return $this->object->getValue('order_delivery_props');
		}

		/**
		 * Устанавливает статус доставки заказа
		 * @param int|string $newStatusId идентификатор или код статуса доставки
		 * @throws coreException
		 */
		public function setDeliveryStatus($newStatusId) {
			if ($newStatusId && !is_numeric($newStatusId)) {
				$newStatusId = self::getStatusByCode($newStatusId, 'order_delivery_status');
			}

			$oldStatusId = $this->object->getValue('delivery_status_id');

			$event = new umiEventPoint('order-delivery-status-changed');
			$event->addRef('order', $this);
			$event->setParam('old-status-id', $oldStatusId);
			$event->setParam('new-status-id', $newStatusId);

			$isStatusChanged = ($oldStatusId != $newStatusId);

			if (!$isStatusChanged) {
				return;
			}

			$event->setMode('before');
			$event->call();

			$this->object->setValue('delivery_status_id', $newStatusId);

			$event->setMode('after');
			$event->call();
		}

		/**
		 * Возвращает стоимость заказа с учетом скидок
		 * @return float
		 */
		public function getActualPrice() {
			return (float) $this->actualPrice;
		}

		/**
		 * Возвращает оригинальную стоимость заказа
		 * @return float
		 */
		public function getOriginalPrice() {
			return (float) $this->originalPrice;
		}

		/**
		 * Возвращает количество товарных наименований в заказе
		 * @return int
		 */
		public function getTotalAmount() {
			return (int) $this->totalAmount;
		}

		/**
		 * Возвращает стоимость доставки
		 * @return float
		 */
		public function getDeliveryPrice() {
			return (float) $this->delivery_price;
		}

		/**
		 * Пересчитывает стоимость заказа
		 * @param bool $useAppliedDiscount нужно ли использовать уже примененную скидку
		 * или провизвести поиск наиболее подходящей
		 * @throws coreException
		 */
		public function refresh($useAppliedDiscount = false) {
			/**
			 * @var iUmiObject|iUmiEntinty $object
			 */
			$object = $this->object;
			$items = $this->getItems();
			$originalPrice = 0;
			$totalAmount = 0;

			$eventPoint = new umiEventPoint("order_refresh");
			$eventPoint->setMode('before');
			$eventPoint->addRef("order", $object);
			$eventPoint->setParam("items", $items);
			$eventPoint->call();

			$recalculateDiscount = emarket::isBasket($this);

			foreach ($items as $item) {
				if (!$item instanceof orderItem) {
					continue;
				}

				$refreshed = $item->refresh($recalculateDiscount);

				if ($refreshed === false) {
					$this->removeItem($item);
					continue;
				}

				$originalPrice += $item->getTotalActualPrice();
				$totalAmount += $item->getAmount();
			}

			if ($recalculateDiscount) {

				if ($useAppliedDiscount && $this->getDiscount() instanceof orderDiscount) {
					$discount = $this->getDiscount();
				} else {
					$discount = $this->searchDiscount();
				}

				if ($discount instanceof orderDiscount) {
					$actualPrice = $discount->recalcPrice($originalPrice);

					$pricesDiff = ($originalPrice - $actualPrice);
					$discountValue = ($pricesDiff < 0) ? 0 : $pricesDiff;

					$this->setDiscount($discount);
					$this->setDiscountValue($discountValue);
				} else {
					$actualPrice = $originalPrice;

					$this->setDiscount();
					$this->setDiscountValue(0);
				}
			} else {
				$discountValue = $this->getDiscountValue();
				$actualPrice = $originalPrice - $discountValue;
			}

			$actualPrice += (float) $this->getDeliveryPrice();
			$actualPrice -= (float) $this->getBonusDiscount();

			$eventPoint->setMode('after');
			$eventPoint->setParam("originalPrice", $originalPrice);
			$eventPoint->setParam("totalAmount", $totalAmount);
			$eventPoint->addRef("actualPrice", $actualPrice);
			$eventPoint->call();

			$this->originalPrice = $originalPrice;
			$this->actualPrice = $actualPrice;
			$this->totalAmount = $totalAmount;
			$this->commit();
		}

		/**
		 * Устанавливает общий вес
		 * @param float $value новое значение веса
		 */
		public function setTotalWeight($value) {
			$this->getObject()
				->setValue(self::TOTAL_WEIGHT_FIELD, floatval($value));
		}

		/**
		 * Устанавливает общую ширину
		 * @param float $value новое значение ширины
		 */
		public function setTotalWidth($value) {
			$this->getObject()
				->setValue(self::TOTAL_WIDTH_FIELD, floatval($value));
		}

		/**
		 * Устанавливает общую высоту
		 * @param float $value новое значение высоты
		 */
		public function setTotalHeight($value) {
			$this->getObject()
				->setValue(self::TOTAL_HEIGHT_FIELD, floatval($value));
		}

		/**
		 * Устанавливает общую длину
		 * @param float $value новое значение длины
		 */
		public function setTotalLength($value) {
			$this->getObject()
				->setValue(self::TOTAL_LENGTH_FIELD, floatval($value));
		}

		/**
		 * Возвращает общий вес в граммах
		 * @return float
		 */
		public function getTotalWeight() {
			$totalWeight = $this->getObject()
				->getValue(self::TOTAL_WEIGHT_FIELD);

			if (!is_float($totalWeight) || $totalWeight == 0) {
				$totalWeight = (float) $this->getSettings()
					->get(EmarketSettings::ORDER_SECTION, 'defaultWeight');
			}

			return $totalWeight;
		}

		/**
		 * Возвращает общую ширину в сантиметрах
		 * @return float
		 */
		public function getTotalWidth() {
			$totalWidth = $this->getObject()
				->getValue(self::TOTAL_WIDTH_FIELD);

			if (!is_float($totalWidth) || $totalWidth == 0) {
				$totalWidth = (float) $this->getSettings()
					->get(EmarketSettings::ORDER_SECTION, 'defaultWidth');
			}

			return $totalWidth;
		}

		/**
		 * Возвращает общую высоту в сантиметрах
		 * @return float
		 */
		public function getTotalHeight() {
			$totalHeight = $this->getObject()
				->getValue(self::TOTAL_HEIGHT_FIELD);

			if (!is_float($totalHeight) || $totalHeight == 0) {
				$totalHeight = (float) $this->getSettings()
					->get(EmarketSettings::ORDER_SECTION, 'defaultHeight');
			}

			return $totalHeight;
		}

		/**
		 * Возвращает общую длину в сантиметрах
		 * @return float
		 */
		public function getTotalLength() {
			$totalLength = $this->getObject()
				->getValue(self::TOTAL_LENGTH_FIELD);

			if (!is_float($totalLength) || $totalLength == 0) {
				$totalLength = (float) $this->getSettings()
					->get(EmarketSettings::ORDER_SECTION, 'defaultLength');
			}

			return $totalLength;
		}

		/**
		 * Возвращает идентификатор адреса доставки, если он задан - иначе 0
		 * @return int
		 */
		public function getDeliveryAddressId() {
			return (int) $this->getObject()
				->getValue(self::DELIVERY_ADDRESS_FIELD);
		}

		/**
		 * Возвращает дату отгрузки заказа если она задана
		 * @return iUmiDate|null
		 */
		public function getPickupDate() {
			$pickupDate = $this->getObject()
				->getValue(self::PICKUP_DATE_FIELD);

			return ($pickupDate instanceof iUmiDate) ? $pickupDate : null;
		}

		/**
		 * Возвращает дату доставки заказа если она задана
		 * @return iUmiDate|null
		 */
		public function getDeliveryDate() {
			$delivery = $this->getObject()
				->getValue(self::DELIVERY_DATE_FIELD);

			return ($delivery instanceof iUmiDate) ? $delivery : null;
		}

		/**
		 * Возвращает идентификатор тарифа доставки
		 * @return string
		 */
		public function getDeliveryTariffId() {
			return (string) $this->getObject()
				->getValue(self::DELIVERY_TARIFF_FIELD);
		}

		/**
		 * Возвращает идентификатор типа доставки
		 * @return string
		 */
		public function getDeliveryTypeId() {
			return (string) $this->getObject()
				->getValue(self::DELIVERY_TYPE_FIELD);
		}

		/**
		 * Возвращает идентификатор провайдера доставки
		 * @return string
		 */
		public function getDeliveryProviderId() {
			return (string) $this->getObject()
				->getValue(self::DELIVERY_PROVIDER_FIELD);
		}

		/**
		 * Возвращает идентификатор пункта приема товара
		 * @return string
		 */
		public function getDeliveryPointInId() {
			return (string) $this->getObject()
				->getValue(self::DELIVERY_POINT_IN_FIELD);
		}

		/**
		 * Возвращает идентификатор пункта выдачи товара
		 * @return string
		 */
		public function getDeliveryPointOutId() {
			return (string) $this->getObject()
				->getValue(self::DELIVERY_POINT_OUT_FIELD);
		}

		/**
		 * Возвращает идентификатор типа отгрузки
		 * @return string
		 */
		public function getPickupTypeId() {
			return (string) $this->getObject()
				->getValue(self::PICKUP_TYPE_FIELD);
		}

		/**
		 * Возвращает абсолютное значение скидки заказа
		 * @return float
		 */
		public function getDiscountValue() {
			return $this->discountValue;
		}

		/**
		 * Оплачен ли заказ
		 * @return bool
		 */
		public function isOrderPayed() {
			return (order::getStatusByCode('accepted', 'order_payment_status') == $this->getPaymentStatus());
		}

		/**
		 * Являет ли заказ корзиной
		 * @return bool
		 */
		public function isOrderBasket() {
			$orderStatusDefined = (bool) $this->getOrderStatus();
			$orderStatusNotEditing = $this->getOrderStatus() != order::getStatusByCode('editing');
			$orderNumberDefined = (bool) $this->getNumber();

			return !($orderStatusDefined && $orderStatusNotEditing && $orderNumberDefined);
		}

		/**
		 * Устанавливает абсолютное значение скидки заказа
		 * @param float $value значение скидки
		 */
		public function setDiscountValue($value) {
			$value = (float) $value;
			$orderPrice = $this->getOriginalPrice();

			if ($value > $orderPrice) {
				$value = $orderPrice;
			}

			$this->discountValue = (float) $value;
		}

		/**
		 * Возвращает идентификатор покупателя заказа.
		 * Покупателем может быть пользователь или незарегистрированный покупатель.
		 * @return int|Mixed|string
		 */
		public function getCustomerId() {
			return $this->object->getValue(order::CUSTOMER_ID_FIELD);
		}

		/**
		 * Возвращает домен, на котором был оформлен заказ
		 * @return iDomain
		 */
		public function getDomain() {
			return $this->domain;
		}

		/**
		 * Устанавливает домен, на котором был оформлен заказ
		 * @param domain $domain домен
		 */
		public function setDomainId(domain $domain) {
			$this->domain = $domain;
		}

		/**
		 * Возвращает скидку, примененную к заказу
		 * @return discount
		 */
		public function getDiscount() {
			return $this->discount;
		}

		/**
		 * Устанавливает заказу скидку
		 * @param discount $discount скидка
		 */
		public function setDiscount(discount $discount = null) {
			/**
			 * @var orderDiscount $discount
			 */
			if ($discount instanceof orderDiscount && ($discount->validate($this) == false)) {
				$discount = null;
			}

			$this->discount = $discount;
		}

		/**
		 * Возвращает количество бонусом, которым был|будет оплачен заказ
		 * @return int
		 */
		public function getBonusDiscount() {
			return $this->object->getValue('bonus');
		}

		/**
		 * Устанавливает количество бонусов, которым был|будет оплачен заказ
		 * @param int $bonus количество бонусов
		 */
		public function setBonusDiscount($bonus) {
			$bonus = $bonus > 0 ? $bonus : 0;
			/**
			 * @var emarket $emarket
			 */
			$emarket = cmsController::getInstance()->getModule('emarket');
			/**
			 * @var iUmiObject $defaultCurrency
			 */
			$defaultCurrency = $emarket->getDefaultCurrency();
			/**
			 * @var iUmiObject $currency
			 */
			$currency = $emarket->getCurrentCurrency();

			$bonus = $bonus * $currency->getValue('nominal') * $currency->getValue('rate');
			$bonus = $bonus / $defaultCurrency->getValue('rate') / $defaultCurrency->getValue('nominal');
			$bonus = round($bonus, 2);

			$bonus = $bonus > $this->getActualPrice() ? $this->getActualPrice() : $bonus;

			$customerId = $this->getCustomerId();
			$customer = umiObjectsCollection::getInstance()->getObject($customerId);

			if ($customer->bonus < $bonus) {
				$bonus = $customer->bonus;
			}

			$this->object->setValue('bonus', $bonus);
			$this->object->commit();

			$customer->setValue('bonus', $customer->getValue('bonus') - $bonus);
			$customer->setValue('spent_bonus', $customer->getValue('spent_bonus') + $bonus);
			$customer->commit();
		}

		/**
		 * Устанавливает заказу номер
		 * @return mixed
		 * @throws coreException
		 */
		public function generateNumber() {
			$config = mainConfiguration::getInstance();
			$className = $config->get('modules', 'emarket.numbers') . 'OrderNumber';

			if (!class_exists($className)) {
				throw new coreException("Can't load order numbers generator. Check modules.emarket.numbers config setting");
			}

			/**
			 * @var $object iOrderNumber
			 */
			$object = new $className($this);
			return $object->number();
		}

		/**
		 * Возвращает номер заказа
		 * @return int
		 */
		public function getNumber() {
			return $this->getObject()
				->getValue(self::ORDER_NUMBER);
		}

		/** Применяет изменения заказа */
		public function commit() {
			$object = $this->getObject();
			$object->setValue('total_original_price', $this->originalPrice);
			$object->setValue('total_price', $this->actualPrice);
			$object->setValue('total_amount', $this->totalAmount);
			$object->setValue('domain_id', ($this->domain instanceof iDomain) ? $this->domain->getId() : false);
			$object->setValue('order_discount_id', ($this->discount instanceof discount) ? $this->discount->getId() : false);
			$object->setValue(self::ORDER_DISCOUNT_VALUE_FIELD_GUID, $this->discountValue);

			$session = \UmiCms\Service::Session();
			$oldHttpReferer = trim($object->getValue('http_referer'));
			$httpReferrer = empty($oldHttpReferer) ? urldecode($session->get('http_referer')) : $oldHttpReferer;
			$object->setValue('http_referer', $httpReferrer);

			$oldHttpReferer = trim($object->getValue('http_target'));
			$httpTarget = empty($oldHttpReferer) ? urldecode($session->get('http_target')) : $oldHttpReferer;
			$object->setValue('http_target', $httpTarget);

			$adv = $this->getAdvParamFromUrl($object->getValue('http_target'), $object->getValue('http_referer'));
			$object->setValue('source_domain', array_key_exists('utm_source', $adv) ? $adv['utm_source'] : '');
			$object->setValue('utm_medium', array_key_exists('utm_medium', $adv) ? $adv['utm_medium'] : '');
			$object->setValue('utm_term', array_key_exists('utm_term', $adv) ? $adv['utm_term'] : '');
			$object->setValue('utm_campaign', array_key_exists('utm_campaign', $adv) ? $adv['utm_campaign'] : '');
			$object->setValue('utm_content', array_key_exists('utm_content', $adv) ? $adv['utm_content'] : '');

			$this->applyItems();
			parent::commit();
		}

		/**
		 * Конструктор
		 * @param iUmiObject $object объект-источник данных для заказа
		 */
		protected function __construct(iUmiObject $object) {
			parent::__construct($object);

			$this->calculator = new UmiCms\Classes\Components\Emarket\Orders\Calculator($this);
			$domains = domainsCollection::getInstance();
			$this->totalAmount = (int) $object->getValue('total_amount');
			$this->originalPrice = (float) $object->getValue('total_original_price');
			$this->actualPrice = (float) $object->getValue('total_price');
			$this->domain = $domains->getDomain($domains->getDomainId($object->getValue('domain_id')));
			$this->discount = orderDiscount::get($object->getValue('order_discount_id'));
			$discountValue = $object->getValue(self::ORDER_DISCOUNT_VALUE_FIELD_GUID);

			if (!is_numeric($discountValue)) {
				$pricesDiff = ($this->originalPrice - $this->actualPrice);
				$discountValue = ($pricesDiff < 0) ? 0 : $pricesDiff;
			}

			/** @var emarket $module */
			$module = cmsController::getInstance()->getModule('emarket');
			$this->settings = $module->getImplementedInstance(emarket::SETTINGS_CLASS);

			$this->discountValue = (float) $discountValue;
			$this->readItems();
		}

		/** Сохраняет общее значение каждой характеристики */
		public function saveTotalProperties() {
			$this->setTotalWeight($this->calculator->getTotalWeight());
			$this->setTotalWidth($this->calculator->getTotalWidth());
			$this->setTotalHeight($this->calculator->getTotalHeight());
			$this->setTotalLength($this->calculator->getTotalLength());
		}

		/**
		 * Превращает корзину в оформленный заказ, если у корзины подходящие статусы
		 * @return bool
		 * @throws coreException
		 */
		public function order() {
			$status = $this->getOrderStatus();
			$code = self::getCodeByStatus($status);
			$isEligible = is_null($status) || in_array($code, ['payment', 'editing']);

			if (!$isEligible) {
				return false;
			}

			if (!$this->object->getValue(self::ORDER_NUMBER)) {
				$this->generateNumber();
			}

			$this->object->setValue('order_date', time());
			$this->setOrderStatus('waiting');
			$this->object->commit();
			customer::get()->freeze();

			return true;
		}

		/**
		 * Возвращает экземпляр класса управления настройками
		 * @return EmarketSettings
		 * @throws coreException
		 */
		protected function getSettings() {
			return $this->settings;
		}

		/**
		 * Загружает в заказ его товарные наименования
		 */
		protected function readItems() {
			$objectItems = $this->getValue('order_items');
			$items = [];

			foreach ($objectItems as $objectId) {
				try {
					$items[] = orderItem::get($objectId);
				} catch (privateException $e) {
				}
			}

			$this->items = $items;
		}

		/**
		 * Сохраняет товарные наименования заказа
		 */
		protected function applyItems() {
			$values = [];

			foreach ($this->items as $item) {
				if ($item instanceof orderItem) {
					$values[] = $item->getId();
				}
			}

			$this->object->setValue('order_items', $values);
		}

		/**
		 * Возвращает самую выгодную для покупателя скидку на текущий заказ
		 * @return null|orderDiscount
		 */
		public function searchDiscount() {
			$discount = orderDiscount::search($this);
			return ($discount instanceof orderDiscount) ? $discount : null;
		}

		/**
		 * Разбирает параметры рекламной компании, если рекламная компания не найдена - разбирает реферер
		 * @param string $url адрес на который пришёл пользователь
		 * @param null|string $refer адрес с которого пришёл пользователь
		 * @return array
		 */
		public function getAdvParamFromUrl($url, $refer = null) {
			$params = [];
			$parseUrl = parse_url(urldecode($url));

			if (isset($parseUrl['query'])) {
				$res = explode('&amp;', $parseUrl['query']);
				if (!empty($res[0])) {
					foreach ($res as $r) {
						$param = explode('=', $r);
						if (count($param) > 1) {
							$params[$param[0]] = $param[1];
						}
					}
				}
			}

			if (count($params) <= 0 && !is_null($refer)) {
				$params = $this->parseUrlReferer($refer);
			}

			return $params;
		}

		/**
		 * Возвращает параметры рекламной компании из реферера
		 * @param string $url реферер
		 * @return array
		 */
		public function parseUrlReferer($url) {
			$query = [];
			switch (true) {
				case (strpos($url, 'yandex') != 0) : {
					preg_match('"text=((.*?)[^&]*)"', $url, $arr);
					$query = [
						'utm_source' => 'yandex',
						'utm_medium' => 'organic',
						'utm_term' => count($arr) > 0 ? $arr[1] : '',
					];
					break;
				}
				case (strpos($url, 'google') != 0) : {
					preg_match('"q=((.*?)[^&]*)"', $url, $arr);
					$query = [
						'utm_source' => 'google',
						'utm_medium' => 'organic',
						'utm_term' => count($arr) > 0 ? $arr[1] : '',
					];
					break;
				}
				case (strpos($url, 'rambler') != 0) : {
					preg_match('"query=((.*?)[^&]*)"', $url, $arr);
					$query = [
						'utm_source' => 'rambler',
						'utm_medium' => 'organic',
						'utm_term' => count($arr) > 0 ? $arr[1] : '',
					];
					break;
				}
				case (strpos($url, 'nigma') != 0) : {
					preg_match('"s=((.*?)[^&]*)"', $url, $arr);
					$query = [
						'utm_source' => 'nigma',
						'utm_medium' => 'organic',
						'utm_term' => count($arr) > 0 ? $arr[1] : '',
					];
					break;
				}
				default: {
					$urlRes = parse_url($url);
					if (!empty($urlRes['host']) > 0) {
						$query = [
							'utm_source' => $urlRes['host'],
							'utm_medium' => 'referal',
						];
					}
				}
			}
			return $query;
		}

		/**
		 * Выполняет функцию для каждого наименования заказа
		 * @param callable $callback
		 */
		public function forEachItem(Callable $callback) {
			/** @var \OrderItem $item */
			foreach ($this->getItems() as $item) {
				$callback($item);
			}
		}
	}
