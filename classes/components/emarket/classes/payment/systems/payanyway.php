<?php
	/**
	 * Способ оплаты через платежную систему "PayAnyWay"
	 * @link http://www.moneta.ru/doc/MONETA.MerchantAPI.v2.ru.pdf
	 */
	class payanywayPayment extends payment {
		/**
		 * @const int ready
		 * @const int delivery
		 * Заказ оплачен. Уведомление об оплате магазину доставлено
		 */
		const ready        = 200;
		const delivery	   = 200;
		/**
		 * @const int editing
		 * @const int waiting
		 * Заказ находится в обработке. Точный статус оплаты заказа определить невозможно.
		 */
		const editing	   = 302;
		const waiting	   = 302;
		/**
		 * @const int payment
		 * @const int accepted
		 * Заказ создан и готов к оплате. Уведомление об оплате магазину не доставлено.
		 */
		const payment	   = 402;
		const accepted	   = 402;
		/**
		 * @const int canceled
		 * @const int rejected
		 * Заказ не является актуальным в магазине (например, заказ отменен).
		 */
		const canceled	   = 500;
		const rejected	   = 500;

		/**
		 * {@inheritdoc}
		 */
		public function validate() {
			return true;
		}

		/**
		 * {@inheritdoc}
		 */
		public static function getOrderId() {
			return (int) getRequest('MNT_TRANSACTION_ID');
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
			$emarket = cmsController::getInstance()->getModule('emarket');
			/**
			 * @var iUmiObject $currency
			 */
			$currency = $emarket->getDefaultCurrency();
			$currency = ($currency instanceof iUmiObject) ? $currency->getValue('codename') : 'RUB';

			if ($currency == 'RUR'){
				$currency = 'RUB';
			}

			$amount				= number_format($this->order->getActualPrice(), 2, '.', '');
			$orderId			= $this->order->getId() . '.' . time();
			$merchantId			= $this->object->getValue('mnt_id');
			$dataIntegrityCode	= $this->object->getValue('mnt_data_integrity_code');
			$successUrl			= $this->object->getValue('mnt_success_url');
			$failUrl			= $this->object->getValue('mnt_fail_url');
			$testMode			= $this->object->getValue('mnt_test_mode');
			$systemUrl			= $this->object->getValue('mnt_system_url');

			if (empty($testMode)){
				$testMode = 0;
			}

			$signature	 = md5("{$merchantId}{$orderId}{$amount}{$currency}{$testMode}{$dataIntegrityCode}");

			$param = array();
			$param['formAction'] 		= "https://{$systemUrl}/assistant.htm";
			$param['mntId'] 			= $merchantId;
			$param['mnTransactionId']	= $orderId;
			$param['mntCurrencyCode'] 	= $currency;
			$param['mntAmount'] 	 	= $amount;
			$param['mntTestMode'] 	 	= $testMode;
			$param['mntSignature'] 		= $signature;
			$param['mntSuccessUrl'] 	= $successUrl;
			$param['mntFailUrl'] 	 	= $failUrl;

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/payanyway/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * {@inheritdoc}
		 * @throws coreException
		 */
		public function poll() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType('text/xml');
			$responseCode = payanywayPayment::canceled;

			if (
				!is_null(getRequest('MNT_ID')) &&
				!is_null(getRequest('MNT_TRANSACTION_ID')) &&
				!is_null(getRequest('MNT_AMOUNT')) &&
				!is_null(getRequest('MNT_CURRENCY_CODE')) &&
				!is_null(getRequest('MNT_TEST_MODE')) &&
				!is_null(getRequest('MNT_SIGNATURE')) &&
				$this->checkSignature()
			) {
				$amount = (float) getRequest('MNT_AMOUNT');
				$orderActualPrice = (float) $this->order->getActualPrice();

				if ((getRequest('MNT_COMMAND') === null || getRequest('MNT_COMMAND') != 'CHECK') && ($orderActualPrice == $amount) ) {
					$this->order->setPaymentStatus('accepted');
					$responseCode = payanywayPayment::ready;
				} else {
					$statusCode = $this->order->getCodeByStatus($this->order->getOrderStatus());
					$responseCode = constant('payanywayPayment::'.$statusCode);
				}
			}

			$buffer->push($this->getResponse($responseCode));
			$buffer->end();
		}

		/**
		 * Проверяет подпись заказа из платежной системы
		 * @return bool
		 */
		public function checkSignature() {
			$params = '';

			if (getRequest('MNT_COMMAND')) {
				$params .= getRequest('MNT_COMMAND');
			}

			$params .= getRequest('MNT_ID') . getRequest('MNT_TRANSACTION_ID');

			if (getRequest('MNT_OPERATION_ID')) {
				$params .= getRequest('MNT_OPERATION_ID');
			}

			if (getRequest('MNT_AMOUNT')) {
				$params .= getRequest('MNT_AMOUNT');
			}

			$params .= getRequest('MNT_CURRENCY_CODE') . getRequest('MNT_TEST_MODE');
			$signature = md5($params . $this->object->getValue('mnt_data_integrity_code'));

			if (strcasecmp($signature, getRequest('MNT_SIGNATURE') ) == 0) {
				return true;
			}

			return false;
		}

		/**
		 * Возвращает ответ для платежной системы
		 * @param string $resultCode код ответа
		 * @return string
		 */
		public function getResponse($resultCode) {
			$signature = md5($resultCode . getRequest('MNT_ID') . getRequest('MNT_TRANSACTION_ID') . $this->object->getValue('mnt_data_integrity_code'));
			$result = '<?xml version="1.0" encoding="UTF-8" ?>';
			$result .= '<MNT_RESPONSE>';
			$result .= '<MNT_ID>' . getRequest('MNT_ID') . '</MNT_ID>';
			$result .= '<MNT_TRANSACTION_ID>' . getRequest('MNT_TRANSACTION_ID') . '</MNT_TRANSACTION_ID>';
			$result .= '<MNT_RESULT_CODE>' . $resultCode . '</MNT_RESULT_CODE>';
			$result .= '<MNT_SIGNATURE>' . $signature . '</MNT_SIGNATURE>';
			$result .= '</MNT_RESPONSE>';
			return $result;
		}
	};
?>