<?php

	/**
	 * Способ оплаты через платежную систему "PayOnline System"
	 */
	class payonlinePayment extends payment {

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
			$this->order->order();
			/**
			 * @var emarket $emarket
			 */
			$cmsController = cmsController::getInstance();
			$emarket = $cmsController->getModule('emarket');
			/**
			 * @var iUmiObject $currency
			 */
			$currency = $emarket->getDefaultCurrency();
			$currency = ($currency instanceof iUmiObject) ? $currency->getValue('codename') : 'RUB';

			if (!in_array($currency, ['RUB', 'EUR', 'USD'])) {
				$currency = 'RUB';
			}

			$merchantId = $this->object->getValue('merchant_id');
			$privateKey = $this->object->getValue('private_key');
			$orderId = $this->order->getId();

			$amount = number_format($this->order->getActualPrice(), 2, '.', '');
			$keyString = "MerchantId={$merchantId}&OrderId={$orderId}&Amount={$amount}&Currency={$currency}&PrivateSecurityKey={$privateKey}";
			$securityKey = md5($keyString);
			$formAction = "?MerchantId={$merchantId}&OrderId={$orderId}&Amount={$amount}&Currency={$currency}&SecurityKey={$securityKey}&order-id={$orderId}";
			$formAction = "https://secure.payonlinesystem.com/ru/payment/" . $formAction;

			$param = [];
			$param['formAction'] = $formAction;
			$param['MerchantId'] = $merchantId;
			$param['OrderId'] = $orderId;
			$param['Amount'] = $amount;
			$param['Currency'] = $currency;
			$param['SecurityKey'] = $securityKey;
			$param['orderId'] = $orderId;
			$param['ReturnUrl'] = getSelectedServerProtocol() . '://' . $cmsController->getCurrentDomain()->getHost();

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/payonline/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * {@inheritdoc}
		 */
		public function poll() {
			$dateTime = getRequest('DateTime');
			$transactionId = getRequest('TransactionID');
			$orderId = getRequest('OrderId');
			$amount = getRequest('Amount');
			$currency = getRequest('Currency');
			$securityKey = getRequest('SecurityKey');
			$privateKey = $this->object->getValue('private_key');
			$keyString = "DateTime={$dateTime}&TransactionID={$transactionId}&OrderId={$orderId}&Amount={$amount}&Currency={$currency}&PrivateSecurityKey={$privateKey}";
			$checkKey = md5($keyString);

			$amount = (float) $amount;
			$orderActualPrice = (float) $this->order->getActualPrice();

			if ((strcasecmp($checkKey, $securityKey) == 0) && ($orderActualPrice == $amount)) {
				$this->order->setPaymentStatus('accepted');
			}

			$this->order->payment_document_num = $transactionId;
			$this->order->commit();

			$cmsController = cmsController::getInstance();

			if ($emarket = $cmsController->getModule("emarket")) {
				$host = getSelectedServerProtocol() . '://' . $cmsController->getCurrentDomain()->getHost();

				if (umiHierarchy::getInstance()->getIdByPath("resultpayonline")) {
					$host .= "/resultpayonline/";
				}

				$emarket->redirect($host);
			}
		}

	}
