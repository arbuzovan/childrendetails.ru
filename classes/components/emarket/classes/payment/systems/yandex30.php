<?php

	/**
	 * Способ оплаты через платежную систему "Яндекс.Касса"
	 */
	class yandex30Payment extends payment {
		/**
		 * Коды статуса ответа от сервиса (см. документацию сервиса)
		 * @const int STATUS_SUCCESS
		 * @const int STATUS_AUTHERROR
		 * @const int STATUS_SUCCESS_WITH_CHANGES
		 * @const int STATUS_DECLINE
		 * @const int STATUS_REQUESTERROR
		 * @const int STATUS_INTERNALERROR
		 */
		const STATUS_SUCCESS = 0;
		const STATUS_AUTHERROR = 1;
		const STATUS_SUCCESS_WITH_CHANGES = 2;
		const STATUS_DECLINE = 100;
		const STATUS_REQUESTERROR = 200;
		const STATUS_INTERNALERROR = 1000;

		/**
		 * Адреса для запросов к Яндекс.Касса
		 * @const string DEV_REQUEST_URL тестовый режим
		 * @const string PROD_REQUEST_URL боевой режим
		 */
		const DEV_REQUEST_URL = 'https://demomoney.yandex.ru/eshop.xml';
		const PROD_REQUEST_URL = 'https://money.yandex.ru/eshop.xml';

		/**
		 * @const int MAX_CUSTOMER_CONTACT_LENGTH максимальная длина контакта покупателя (0 - первый символ)
		 */
		const MAX_CUSTOMER_CONTACT_LENGTH = 63;
		/**
		 * @const int MAX_ORDER_ITEM_NAME_LENGTH максимальная длина названия товара (0 - первый символ)
	     */
		const MAX_ORDER_ITEM_NAME_LENGTH = 127;
		/**
		 * @var array $validTaxIdList список поддерживаемых идентификаторов ставок НДС
		 */
		private $validTaxIdList = [
			1, 2, 3, 4, 5, 6
		];

		/**
		 * {@inheritdoc}
		 */
		public function validate() {
			return true;
		}

		/**
		 * {@inheritdoc}
		 * Устанавливает заказу статус оплаты "Инициализирована"
		 */
		public function process($template = null) {
			$order = $this->order;
			$object = $this->object;

			$shopId = $object->getValue('shop_id');
			$scid = $object->getValue('scid');

			if (!strlen($shopId) || !strlen($scid)) {
				throw new publicException(getLabel('error-payment-wrong-settings'));
			}

			list($templateString, $modeItem) = emarket::loadTemplates(
				'emarket/payment/yandex30/' . $template,
				'form_block',
				'mode_type_item'
			);

			$modeTypeItems = [];

			foreach ($this->getAvailablePaymentTypes() as $payment) {
				$modeTypeItems[] = emarket::parseTemplate($modeItem, $payment);
			}

			$requestUrl = $object->getValue('demo_mode') ? self::DEV_REQUEST_URL : self::PROD_REQUEST_URL;

			$templateData = [
				'shopId' => $shopId,
				'scid' => $scid,
				'Sum' => (float) $order->getActualPrice(),
				'BankId' => $object->getValue('bank_id'),
				'CustomerNumber' => $order->getId(),
				'formAction' => $requestUrl,
				'orderId' => $order->getId(),
				'subnodes:items' => $modeTypeItems,
				'void:mode_type_list' => $modeTypeItems
			];

			if ($object->getValue('receipt_data_send_enable')) {
				$templateData['ym_merchant_receipt'] = $this->getReceiptInfo($order);
			}

			$order->order();
			$order->setPaymentStatus('initialized');

			return emarket::parseTemplate($templateString, $templateData);
		}

		/**
		 * Возвращает список поддерживаемых типов оплаты
		 * @return array
		 */
		public function getAvailablePaymentTypes() {
			$payments = [];

			if ($this->object->getValue('yandex_pc')) {
				$payments[] = [
					'id' => 0,
					'type' => 'PC',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-pc')
				];
			}

			if ($this->object->getValue('yandex_ac')) {
				$payments[] = [
					'id' => 1,
					'type' => 'AC',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-ac')
				];
			}

			if ($this->object->getValue('yandex_mc')) {
				$payments[] = [
					'id' => 2,
					'type' => 'MC',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-mc')
				];
			}

			if ($this->object->getValue('yandex_gp_svzny')) {
				$payments[] = [
					'id' => 3,
					'type' => 'GP',
					'subtype' => 'SVZNY',
					'label' => getLabel('label-yandex-payment-gp-svzny')
				];
			}

			if ($this->object->getValue('yandex_gp_eurst')) {
				$payments[] = [
					'id' => 4,
					'type' => 'GP',
					'subtype' => 'EURST',
					'label' => getLabel('label-yandex-payment-gp-eurst')
				];
			}

			if ($this->object->getValue('yandex_gp_other')) {
				$payments[] = [
					'id' => 5,
					'type' => 'GP',
					'subtype' => 'OTHER',
					'label' => getLabel('label-yandex-payment-gp-other')
				];
			}

			if ($this->object->getValue('yandex_wm')) {
				$payments[] = [
					'id' => 6,
					'type' => 'WM',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-wm')
				];
			}

			if ($this->object->getValue('yandex_sb')) {
				$payments[] = [
					'id' => 7,
					'type' => 'SB',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-sb')
				];
			}

			if ($this->object->getValue('yandex_mp')) {
				$payments[] = [
					'id' => 8,
					'type' => 'MP',
					'subtype' => '',
					'label' => getLabel('label-yandex-payment-mp')
				];
			}

			return $payments;
		}

		/**
		 * {@inheritdoc}
		 *  В зависимости от типа запроса либо валидирует заказ с установлением соответствующего статуса оплаты
		 * (Проверена/Отклонена), либо переводит оплату заказа в статус "Принята".
		 */
		public function poll() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType('text/xml');
			$action = getRequest('action');
			$shopId = getRequest('shopId');
			$invoiceId = getRequest('invoiceId');

			if (!$this->checkSignature()) {
				$responseCode = yandex30Payment::STATUS_AUTHERROR;
			} elseif (is_null($shopId) || is_null($invoiceId)) {
				$responseCode = yandex30Payment::STATUS_REQUESTERROR;
			} else {
				switch (strtolower($action)) {
					case 'checkorder'  : {
						$responseCode = $this->checkDetails();
						break;
					}

					case 'paymentaviso' : {
						$responseCode = $this->acceptPaymentResult();
						break;
					}

					default  : {
						$responseCode = yandex30Payment::STATUS_REQUESTERROR;
					}
				}
			}

			$this->order->payment_document_num = $invoiceId;
			$this->order->commit();

			$buffer->push($this->getResponseXML($action, $responseCode, $shopId, $invoiceId));
			$buffer->end();
		}

		/**
		 * Проверяет подпись в запросе
		 * @return bool
		 */
		public function checkSignature() {
			$password = (string) $this->object->getValue('shop_password');

			if (!strlen($password)) {
				return false;
			}

			$hashPieces = [];
			$hashPieces[] = getRequest('action');
			$hashPieces[] = getRequest('orderSumAmount');
			$hashPieces[] = getRequest('orderSumCurrencyPaycash');
			$hashPieces[] = getRequest('orderSumBankPaycash');
			$hashPieces[] = getRequest('shopId');
			$hashPieces[] = getRequest('invoiceId');
			$hashPieces[] = getRequest('customerNumber');
			$hashPieces[] = $password;
			$hashString = md5(implode(';', $hashPieces));

			if (strcasecmp($hashString, getRequest('md5')) == 0) {
				return true;
			}

			return false;
		}

		/**
		 * Формирует и возвращает xml для ответа на сервер Яндекс денег
		 * @param string $action код запроса, на которое выполняется ответ
		 * @param int $code код результата
		 * @param int $shopId идентификатор магазина
		 * @param int $invoiceId идентификатор транзакции
		 * @return int
		 */
		public function getResponseXML($action, $code, $shopId, $invoiceId) {
			$dateTime = date('c');
			$result = '<' . "?xml version=\"1.0\" encoding=\"windows-1251\" ?" . '>' . <<<XML
<{$action}Response performedDatetime="{$dateTime}" code="{$code}" shopId="{$shopId}" invoiceId="{$invoiceId}"/>
XML;
			return $result;
		}

		/**
		 * Валидирует данные заказа платежной системы и возвращает код
		 * статуса ответа для нее.
		 * Устанавливает соответствующий статус оплаты заказа (Проверена/Отклонена)
		 * @return int
		 */
		private function checkDetails() {
			$orderSumAmount = (float) getRequest('orderSumAmount');
			try {
				$actualPrice = (float) $this->order->getActualPrice();

				if ($orderSumAmount != $actualPrice) {
					$this->order->setPaymentStatus('declined');
					$resultCode = yandex30Payment::STATUS_DECLINE;
				} else {
					$this->order->setPaymentStatus('validated');
					$resultCode = yandex30Payment::STATUS_SUCCESS;
				}
			} catch (Exception $e) {
				$resultCode = yandex30Payment::STATUS_INTERNALERROR;
			}

			return $resultCode;
		}

		/**
		 * Принимает результат платежной транзакции и код
		 * статуса ответа для платежной системы.
		 * Устанавливает статус оплаты заказа "Принята".
		 * @return int
		 */
		private function acceptPaymentResult() {
			$resultCode = yandex30Payment::STATUS_SUCCESS;

			try {
				$this->order->setPaymentStatus('accepted');
			} catch (Exception $e) {
				$resultCode = yandex30Payment::STATUS_INTERNALERROR;
			}

			return $resultCode;
		}

		/**
		 * Возвращает данные для печати чека.
		 * @link https://tech.yandex.ru/money/doc/payment-solution/payment-form/payment-form-receipt-docpage/
		 * @param order $order $order заказ
		 * @return string json
		 * @see yandex30Payment::getCustomerEmail() и yandex30Payment::getOrderItemInfoList()
		 * @throws publicException
		 */
		private function getReceiptInfo(order $order) {
			$customerId = $order->getCustomerId();
			$customerSource = selector::get('object')
				->id($customerId);

			if (!$customerSource instanceof iUmiObject) {
				throw new publicException(getLabel('error-payment-wrong-customer-id'));
			}

			$customer = new customer($customerSource);

			$receiptInfo = array_merge(
				$this->getCustomerEmail($customer),
				$this->getOrderItemInfoList($order)
			);

			if (!is_array($receiptInfo)) {
				throw new publicException(getLabel('error-payment-wrong-receipt-info'));
			}

			return json_encode($receiptInfo);
		}

		/**
		 * Возвращает контакт почтовый ящик покупателя
		 * @param customer $customer покупатель
		 * @return array
		 *
		 * [
		 *      'customerContact' => почтовый ящик покупателя
		 * ]
		 *
		 * @throws publicException
		 */
		private function getCustomerEmail(customer $customer) {

			$email = $customer->getEmail();

			if (!is_string($email) || empty($email) || !umiMail::checkEmail($email)) {
				throw new publicException(getLabel('error-payment-wrong-customer-email'));
			}

			$trimmedEmail = substr($email, 0, self::MAX_CUSTOMER_CONTACT_LENGTH);

			return [
				'customerContact' => $trimmedEmail
			];
		}

		/**
		 * Возвращает информацию о списке товарных наименований
		 * @param order $order заказ
		 * @return array
		 *
		 * [
		 *      'items' =>  [
		 *          # => @see yandex30Payment::getOrderItemInfo()
		 *      ]
		 * ]
		 *
		 * @throws publicException
		 */
		private function getOrderItemInfoList(order $order) {
			$orderItemList = $order->getItems();

			if (!is_array($orderItemList) || empty($orderItemList)) {
				throw new publicException(getLabel('error-payment-empty-order'));
			}

			$orderItemDataList = [];

			foreach ($orderItemList as $orderItem) {
				$orderItemDataList[] = $this->getOrderItemInfo($orderItem);
			}

			try {
				$deliveryInfo = $this->getDeliveryInfo($order);
				$orderItemDataList[] = $deliveryInfo;
			} catch (expectObjectException $e) {
				//nothing
			}

			return [
				'items' => $orderItemDataList
			];
		}


		/**
		 * Возвращает информацию о доставке заказа
		 * @param order $order заказ
		 * @return array
		 * @see yandex30Payment::getOrderItemInfo()
		 * @throws expectObjectException
		 */
		private function getDeliveryInfo(order $order) {
			$delivery = selector::get('object')->id(
				$order->getValue('delivery_id')
			);

			if (!$delivery instanceof iUmiObject) {
				throw new expectObjectException(getLabel('error-unexpected-exception'));
			}

			return [
				'quantity' => sprintf('%.3f', 1),
				'price' => [
					'amount' => sprintf('%.2f', $order->getValue('delivery_price'))
				],
				'tax' => $this->getEntityTaxRateId($delivery),
				'text' => substr($delivery->getName(), 0, self::MAX_ORDER_ITEM_NAME_LENGTH)
			];
		}

		/**
		 * Возвращает информацию о товарном наименовании
		 * @param orderItem $orderItem товарное наименование
		 * @return array
		 *
		 * [
		 *      'quantity' => Количество товара
		 *      'price' => [
		 *          'amount' => Цена за единицу товара
		 *      ],
		 *      'tax' => id ставки НДС
		 *      'text' => Название товара
		 * ]
		 */
		private function getOrderItemInfo(orderItem $orderItem) {
			return [
				'quantity' => sprintf('%.3f', $orderItem->getAmount()),
				'price' => [
					'amount' => sprintf('%.2f', $orderItem->getValue('item_price'))
				],
				'tax' => $this->getEntityTaxRateId($orderItem->getObject()),
				'text' => substr($orderItem->getName(), 0, self::MAX_ORDER_ITEM_NAME_LENGTH)
			];
		}

		/**
		 * Возвращает идентификатор ставки НДС
		 * @param iUmiObject $object сущность со ставкой (способ доставки или наименование заказа)
		 * @return int
		 * @throws publicException
		 */
		private function getEntityTaxRateId(iUmiObject $object) {
			$taxRate = selector::get('object')
				->id($object->getValue('tax_rate_id'));

			if (!$taxRate instanceof iUmiObject) {
				throw new publicException(getLabel('error-payment-order-item-empty-tax'));
			}

			$taxRateId = (int) $taxRate->getValue('yandex_id');

			if (!in_array($taxRateId, $this->validTaxIdList)) {
				throw new publicException(getLabel('error-payment-order-item-empty-tax'));
			}

			return $taxRateId;
		}
	}
