<?php
	/**
	 * Базовый класс способа оплаты абстракного типа.
	 * Одновременно является родительским классом всех способов оплаты
	 * и предоставляет интерфейс для создания и получения конкретного способа оплаты.
	 *
	 * По умолчанию в системе существуют следующие реализации типов способов оплаты:
	 *
	 * 1) "AcquiroPay";
	 * 2) "Наличными курьеру";
	 * 3) "Деньги Online";
	 * 4) "Счет для юридических лиц";
	 * 5) "КупиВКредит";
	 * 6) "PayAnyWay";
	 * 7) "PayOnline System";
	 * 8) "PayPal";
	 * 9) "RBK Money";
	 * 10) "Платежная квитанция";
	 * 11) "Robokassa";
	 * 12) "Яндекс.Касса";
	 *
	 * Пример добавления собственного способа оплаты описан в документации:
	 * @link http://api.docs.umi-cms.ru/razrabotka_nestandartnogo_funkcionala/integraciya_platzhnyh_sistem/
	 */
	abstract class payment extends umiObjectProxy {
		/**
		 * @var order $order заказ
		 */
		protected $order;

		/**
		 * Создает способ оплаты заданного типа
		 * @param iUmiObject $paymentTypeObject объект типа способа оплаты
		 * @return payment|bool
		 * @throws coreException
		 */
		final public static function create(iUmiObject $paymentTypeObject) {
			/**
			 * @var iUmiObject|iUmiEntinty $paymentTypeObject
			 */
			$objects = umiObjectsCollection::getInstance();
			$paymentTypeId = null;
			$paymentTypeGUID = $paymentTypeObject->getValue('payment_type_guid');

			if (strlen($paymentTypeGUID)) {
				$paymentTypeId = umiObjectTypesCollection::getInstance()->getTypeIdByGUID($paymentTypeGUID);
			} else {
				$paymentTypeId = $paymentTypeObject->getValue('payment_type_id');
			}

			$objectId = $objects->addObject('', $paymentTypeId);
			$object = $objects->getObject($objectId);

			if ($object instanceof umiObject) {
				$object->setValue('payment_type_id', $paymentTypeObject->getId());
				$object->commit();
				return self::get($objectId);
			}

			return false;
		}

		/**
		 * Возвращает способ оплаты по идентификатор объекта-источника данных для способа оплаты
		 * @param int $objectId идентификато объекта-источника данных для способа оплаты
		 * @param order|null $order заказ
		 * @return payment|bool|null
		 * @throws coreException
		 */
		final public static function get($objectId, order $order = null) {
			if ($objectId instanceof iUmiObject) {
				$object = $objectId;
			} else {
				$object = umiObjectsCollection::getInstance()->getObject($objectId);

				if ($object instanceof iUmiObject == false || !$object->getValue('payment_type_id')) {
					return null;
				}
			}

			$classPrefix = objectProxyHelper::getClassPrefixByType($object->getValue('payment_type_id'));

			objectProxyHelper::includeClass('emarket/classes/payment/systems/', $classPrefix);
			$className = $classPrefix . 'Payment';

			if (is_null($order)){
				return new $className($object);
			}

			return new $className($object, $order);
		}

		/**
		 * Возвращает список способов оплаты
		 * @return array
		 * @throws selectorException
		 */
		final public static function getList() {
			static $paymentsList = null;

			if (!is_null($paymentsList)) {
				return $paymentsList;
			}

			$cacheFrontend = cacheFrontend::getInstance();
			$key = 'payments_list';
			$cachedPaymentsList = $cacheFrontend->loadData($key);

			if (is_array($cachedPaymentsList)) {
				return $cachedPaymentsList;
			}

			$sel = new selector('objects');
			$sel->types('hierarchy-type')->name('emarket', 'payment');
			$sel->option('load-all-props')->value(true);
			$paymentsList = $sel->result();

			$cacheFrontend->saveData($key, $paymentsList, 3600);
			return $paymentsList;
		}

		/**
		 * Возвращает список типов способов оплаты, которые используются на сайте
		 * @return array
		 * @throws selectorException
		 */
		final public static function getUsedPaymentsTypes() {
			static $paymentsTypes;

			if (is_array($paymentsTypes)) {
				return $paymentsTypes;
			}

			$payments = new selector('objects');
			$payments->types('hierarchy-type')->name('emarket', 'payment');
			$payments->option('load-all-props')->value(true);
			$payments->option('no-length')->value(true);
			$payments->option('return')->value('payment_type_id');
			$payments = $payments->result();

			$usedPaymentsTypesIds = array();

			foreach ($payments as $paymentData) {
				$usedPaymentsTypesIds[] = $paymentData['payment_type_id'];
			}

			$usedPaymentsTypesIds = array_unique($usedPaymentsTypesIds);

			$paymentsTypes = new selector('objects');
			$paymentsTypes->types('object-type')->guid('emarket-paymenttype');
			$paymentsTypes->where('id')->equals($usedPaymentsTypesIds);
			$paymentsTypes->option('load-all-props')->value(true);
			$paymentsTypes->option('no-length')->value(true);

			return $paymentsTypes = $paymentsTypes->result();
		}

		/**
		 * Возвращает идентификатор заказа из ответа платежной системы.
		 * Сначала проверяются стандартные поля, потом опрашивается метод getOrderId
		 * каждой подключенной платежной системы.
		 * @return int|bool
		 */
		final public static function getResponseOrderId() {
			$orderId = (int) getRequest('param0');

			if (!$orderId) {
				$orderId = (int) getRequest('orderid');
			}

			if (!$orderId) {
				$orderId = (int)getRequest('orderId');// RBK
			}

			if (!$orderId) {
				$orderId = (int) getRequest('order-id');// Chronopay
			}

			if (!$orderId) {
				$orderId = (int) getRequest('item_number');// PayPal
			}

			if (!$orderId) {
				$paymentSystems = self::getList();
				/**
				 * @var iUmiObject $paymentSystem
				 */
				foreach ($paymentSystems as $paymentSystem) {
					$classPrefix = objectProxyHelper::getClassPrefixByType($paymentSystem->getValue('payment_type_id'));
					objectProxyHelper::includeClass('emarket/classes/payment/systems/', $classPrefix);
					/**
					 * @var acquiropayPayment|dengionlinePayment|kupivkreditPayment|payanywayPayment|paypalPayment|roboxPayment $className
					 */
					$className = $classPrefix . 'Payment';
					$orderId = (int) $className::getOrderId();

					if ($orderId) {
						break;
					}
				}
			}
			return $orderId;
		}

		/**
		 * Конструктор
		 * @param umiObject $object объект-источник данных для способа оплаты
		 * @throws Exception
		 */
		public function __construct(umiObject $object) {
			$args = func_get_args();
			$payment = array_shift($args);

			if (!$payment instanceof umiObject) {
				throw new Exception('Payment expected for creating payment');
			}

			$order = array_shift($args);

			if (!$order instanceof order && $order !== null) {
				throw new Exception('Incorrect order given for creating payment');
			}

			parent::__construct($payment);
			$this->order = $order;
		}

		/**
		 * Возвращает строковой идентификатор типа способа оплаты
		 * @return string|bool
		 */
		public function getCodeName() {
			$objects = umiObjectsCollection::getInstance();
			$paymentTypeId = $this->getValue('payment_type_id');
			$paymentType = $objects->getObject($paymentTypeId);
			return ($paymentType instanceof iUmiObject) ? $paymentType->getValue('class_name') : false;
		}

		/**
		 * Возвращает идентификатор заказа из ответа платежной системы.
		 * @return int|bool
		 */
		public static function getOrderId() {
			return false;
		}

		/**
		 * Применима ли платежная система.
		 * На основании этого метода принимается решение
		 * о добавлени способа оплаты в список доступных способов.
		 * @see EmarketPurchasingStagesSteps::renderPaymentsList()
		 * @return bool
		 */
		abstract function validate();

		/**
		 * Возвращает данные для построения формы отправки
		 * данных заказа в платежную системы.
		 * Инициирует превращение корзины в заказ.
		 * @see EmarketPurchasingStages::payment()
		 * @param string|null $template имя шаблона (для tpl)
		 * @return mixed
		 */
		abstract function process($template = null);

		/**
		 * Принимает запрос от платежной системы.
		 * Чащего всего просто валидирует заказ от платежной системы
		 * и ставит заказу в UMI.CMS статус "Принят".
		 * @see emarket::gateway()
		 * Выводит ответ в буффер.
		 */
		abstract function poll();
	};
?>
