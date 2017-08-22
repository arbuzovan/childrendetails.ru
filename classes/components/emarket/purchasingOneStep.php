<?php
	use UmiCms\Classes\Components\Emarket\Delivery\Address;
	/**
	 * Класс функционала оформления заказа в 1 шаг
	 */
	class EmarketPurchasingOneStep {
		/**
		 * @var emarket $module
		 */
		public $module;

		/**
		 * Возвращает данные всех этапов оформления заказа для построения единой формы
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 * @throws publicException
		 */
		public function purchasing_one_step($template = 'onestep'){
			/**
			 * @var emarket|EmarketPurchasingOneStep|EmarketPurchasingStagesSteps $module
			 */
			$module = $this->module;
			list($purchasing_one_step) = emarket::loadTemplates(
				"emarket/onestep/{$template}.tpl",
				'purchasing_one_step'
			);

			$order = $this->module->getBasketOrder();

			if ($order->isEmpty()) {
				throw new publicException('%error-market-empty-basket%');
			}

			$result = array();

			if (!permissionsCollection::getInstance()->isAuth()){
				$result['onestep']['customer'] = $this->personalInfo($template);
				if (emarket::isXSLTResultMode()) {
					$result['onestep']['customer']['@id'] = customer::get()->getId();
				}
			}

			if ($module->isDeliveryAvailable()) {
				$result['onestep']['delivery'] = $module->customerDeliveryList($template);
				$result['onestep']['delivery_choose'] = $module->renderDeliveryList($order, $template);
			}

			if ($module->isPaymentAvailable()) {
				$result['onestep']['payment'] = $module->paymentsList($template);
			}

			return emarket::parseTemplate($purchasing_one_step, $result);
		}

		/**
		 * Возвращает список адресов пользователя
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function customerDeliveryList($template = 'default') {
			/**
			 * @var emarket|EmarketPurchasingOneStep|EmarketPurchasingStagesSteps $module
			 */
			$module = $this->module;
			$order = $module->getBasketOrder();
			return $module->renderDeliveryAddressesList($order, $template);
		}

		/**
		 * Выводит информацию для построения формы заполнения данных покупателя
		 * @param string $template имя шаблона (для tpl)
		 * @return string
		 */
		public function personalInfo($template = 'onestep') {
			if (permissionsCollection::getInstance()->isAuth()){
				return '';
			}

			$customerId = customer::get()->getId();
			$cmsController = cmsController::getInstance();
			/**
			 * @var DataForms $data
			 */
			$data = $cmsController->getModule('data');

			return $data->getEditForm($customerId, '../../emarket/customer/' . $template);
		}

		/**
		 * Возвращает список способ оплаты
		 * @param string $template имя шаблона (для tpl)
		 * @return array|mixed
		 */
		public function paymentsList($template = 'onestep') {
			/**
			 * @var emarket|EmarketPurchasingOneStep|EmarketPurchasingStagesSteps $module
			 */
			$module = $this->module;
			$order = $module->getBasketOrder(false);
			list($tpl_block, $tpl_item) = emarket::loadTemplates(
				"emarket/payment/{$template}.tpl",
				'payment_block',
				'payment_item'
			);

			$paymentsIds = payment::getList();
			$items = array();
			$currentPaymentId = $order->getValue('payment_id');
			$umiObjectsCollection = umiObjectsCollection::getInstance();

			foreach ($paymentsIds as $paymentId) {
				$payment = payment::get($paymentId, $order);

				/**
				 * @var payment $payment
				 */
				if ($payment->validate($order) == false) {
					continue;
				}

				$paymentObject = $payment->getObject();
				$paymentTypeId = $paymentObject->getValue('payment_type_id');
				$paymentTypeName = $umiObjectsCollection->getObject($paymentTypeId)->getValue('class_name');

				if ($paymentTypeName == 'social') {
					continue;
				}

				$item = array(
					'attribute:id' => $paymentObject->getId(),
					'attribute:name' => $paymentObject->getName(),
					'attribute:type-name' => $paymentTypeName,
					'xlink:href' => $paymentObject->xlink
				);

				if ($paymentId == $currentPaymentId) {
					$item['attribute:active'] = 'active';
				}

				$items[] = emarket::parseTemplate($tpl_item, $item, false, $paymentObject->getId());
			}

			if ($tpl_block && !emarket::isXSLTResultMode()) {
				return emarket::parseTemplate($tpl_block, array(
					'items' => $items
				));
			}

			return array(
				'items' => array(
					'nodes:item'=> $items
				)
			);
		}

		/**
		 * Принимает данные от единой формы оформления заказа и оформляет заказ.
		 * Завершает работу перенаправлением на шаг оплаты или страницу успешного оформления.
		 * @param bool $addressRequired является ли выбор адреса обязательным
		 * @param bool $deliveryRequired является ли выбор способа доставки
		 * @param bool $paymentRequired является ли выбор способа оплаты обязательным
		 * @throws coreException
		 */
		public function saveInfo($addressRequired = false, $deliveryRequired = false, $paymentRequired = true) {

			/**
			 * @var emarket|EmarketPurchasingOneStep $module
			 */
			$module = $this->module;
			$order = $module->getBasketOrder(false);

			$cmsController = cmsController::getInstance();
			$data = $cmsController->getModule('data');
			/**
			 * @var DataForms $data
			 */
			$data->saveEditedObject(customer::get()->getId(), false, true);

			if ($module->isDeliveryAvailable()) {
				$addressId = getRequest('delivery-address');

				try {
					$module->saveAddress($order, $addressId);
				} catch (publicException $exception) {
					if ($addressRequired) {
						$module->errorNewMessage($exception->getMessage());
						$module->errorPanic();
					}
				}

				$deliveryId = getRequest('delivery-id');

				try {
					$module->saveDelivery($order, $deliveryId);
				} catch (publicException $exception) {
					if ($deliveryRequired) {
						$module->errorNewMessage($exception->getMessage());
						$module->errorPanic();
					}
				}
			}

			$payment = null;

			if ($module->isPaymentAvailable()) {
				$paymentId = getRequest('payment-id');

				try {
					$payment = $module->savePayment($order, $paymentId);
				} catch (publicException $exception) {

					if ($paymentRequired) {
						$module->errorNewMessage($exception->getMessage());
						$module->errorPanic();
					}
				}
			}

			$order->refresh();

			if ($payment instanceof payment) {
				$method = "/purchase/payment/{$payment->getCodeName()}/";
			} else {
				$order->order();
				$method = '/purchase/result/successful/';
			}

			$prefix = "{$module->pre_lang}/{$cmsController->getUrlPrefix()}";
			$url = $prefix . get_class($module) . $method;

			$module->redirect($url);
		}

		/**
		 * Сохраняет выбранный адрес доставки в заказ или создает новый
		 * @param order $order заказ
		 * @param int|string $addressId идентификатор заказа или ключевое слово "new", если нужно создать адрес
		 * @return Address\iAddress выбранный адрес
		 * @throws publicException
		 */
		public function saveAddress(order $order, $addressId) {
			$umiObjects = umiObjectsCollection::getInstance();
			$addressTypeId = umiObjectTypesCollection::getInstance()
				->getTypeIdByHierarchyTypeName("emarket", "delivery_address");

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

			try {
				$address = Address\AddressFactory::createByObjectId($addressId);
			} catch (\expectObjectException $e) {
				$address = null;
			}

			if (!$address instanceof Address\iAddress) {
				throw new publicException(getLabel('error-emarket-choose-address'));
			}

			$order->setValue('delivery_address', $addressId);
			$order->commit();
			return $address;
		}

		/**
		 * Сохраняет выбранный способ доставки в заказ
		 * @param order $order заказ
		 * @param int $deliveryId идентификатор способа доставки
		 * @return delivery выбранный способ доставки
		 * @throws publicException если передан некорректный идентификатор
		 */
		public function saveDelivery(order $order, $deliveryId) {
			try {
				$delivery = delivery::get($deliveryId);
			} catch (coreException $exception) {
				$delivery = null;
			}

			if (!$delivery instanceof delivery){
				throw new publicException(getLabel('error-emarket-choose-delivery'));
			}

			$deliveryPrice = (float) $delivery->getDeliveryPrice($order);
			$order->setValue('delivery_id', $deliveryId);
			$order->setValue('delivery_price', $deliveryPrice);
			$order->commit();

			return $delivery;
		}

		/**
		 * Сохраняет выбранный способ оплаты в заказ
		 * @param order $order заказ
		 * @param int $paymentId идентификатор способа оплаты
		 * @return payment выбранный способ оплаты
		 * @throws publicException если передан некорректный идентификатор
		 */
		public function savePayment(order $order, $paymentId) {
			try {
				$payment = payment::get($paymentId, $order);
			} catch (coreException $exception) {
				$payment = null;
			}

			if (!$payment instanceof payment) {
				throw new publicException(getLabel('error-emarket-choose-payment'));
			}

			$order->setValue('payment_id', $paymentId);
			$order->commit();

			return $payment;
		}
	}