<?php
	/**
	 * Способ оплаты через платежную систему "PayPal"
	 */
	class paypalPayment extends payment {

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
			return (int) getRequest('shp_orderId');
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

			if ($currency == 'RUR'){
				$currency = 'RUB';
			}

			$amount = number_format($this->order->getActualPrice(), 2, '.', '');

			$param = array();
			$param['formAction']	 = $this->object->getValue('test_mode') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr";
			$param['paypalemail']	 = $this->object->getValue('paypalemail');
			$param['currency']		 = $currency;
			$param['order_id']		 = $this->order->getId();
			$param['total']			 = $amount;
			$param['return_success'] = $this->object->getValue('return_success');
			$param['cancel_return']	 = $this->object->getValue('cancel_return');

			$httpScheme = getSelectedServerProtocol();
			$param['notify_url'] = $httpScheme . '://' . $_SERVER['SERVER_NAME'] . '/emarket/gateway/';

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/paypal/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * {@inheritdoc}
		 */
		public function poll() {
			$amount			= getRequest("mc_gross");
			$mc_currency	= getRequest("mc_currency");
			$invoice		= getRequest("item_number");
			$paypalEmail	= getRequest("receiver_email");
			$txnType		= getRequest("txn_type");
			$paymentStatus	= getRequest("payment_status");

			$amount = (float) $amount;
			$orderActualPrice = (float) $this->order->getActualPrice();

			if (!$this->paypalIpn()) {
				return false;
			}

			if ($paypalEmail != $this->object->getValue('paypalemail') || $txnType != "web_accept") {
				return false;
			}
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

			if ($currency == 'RUR'){
				$currency = 'RUB';
			}

			if ($mc_currency != $currency) {
				return false;
			}

			/**
			 * @var HTTPOutputBuffer  $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType("text/plain");

			if ($orderActualPrice == $amount && $paymentStatus === 'Completed') {
				$this->order->setPaymentStatus("accepted");
				$this->order->payment_document_num = $invoice;
				$this->order->commit();
				$buffer->push("OK{$invoice}");
			} else {
				$buffer->push("failed");
			}

			$buffer->end();
		}

		/**
		 * Валидирует обмен данными с сервивом
		 * @return bool
		 */
		private function paypalIpn() {
			$raw_post_data = file_get_contents('php://input');
			$raw_post_array = explode('&', $raw_post_data);
			$myPost = array();

			foreach ($raw_post_array as $keyval) {
				$keyval = explode ('=', $keyval);
				if (count($keyval) == 2) {
					$myPost[$keyval[0]] = urldecode($keyval[1]);
				}
			}

			$req = 'cmd=_notify-validate';
			$get_magic_quotes_exists = false;

			if (function_exists('get_magic_quotes_gpc')) {
				$get_magic_quotes_exists = true;
			}

			foreach ($myPost as $key => $value) {
				if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
					$value = urlencode(stripslashes($value));
				} else {
					$value = urlencode($value);
				}
				$req .= "&$key=$value";
			}

			$paypal_url = $this->object->getValue('test_mode') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr";

			$ch = curl_init($paypal_url);

			if ($ch == FALSE) {
				return FALSE;
			}

			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: umicms'));

			$res = curl_exec($ch);

			if (curl_errno($ch) != 0) {
				curl_close($ch);
				return false;
			}

			curl_close($ch);

			if (strcmp ($res, "VERIFIED") == 0) {
				return true;
			} else if (strcmp ($res, "INVALID") == 0) {
				return false;
			}
		}
	};
?>