<?php
	/**
	 * Способ оплаты через платежную систему "AcquiroPay"
	 */
	class acquiropayPayment extends payment {

		/**
		 * {@inheritdoc}
		 */
		public function validate() {
			return true;
		}

		/**
		 * {@inheritdoc}
		 * Устанавливает заказу статус оплаты "Инициализирована".
		 * Устанавливает номер платежного документа у заказа.
		 */
		public function process($template = null) {
			$merchant_id = $this->merchant_id;
			$product_id = $this->product_id;
			$secret_word = $this->secret_word;

			$cmsController = cmsController::getInstance();
			$protocol = getSelectedServerProtocol();
			$www = $protocol . '://' . $cmsController->getCurrentDomain()->getHost();

			$language = strtolower($cmsController->getCurrentLang()->getPrefix());

			switch ($language) {
				case 'ru': {
					$language = 'ru';
					break;
				}
				default: {
					$language = 'en';
					break;
				}
			}

			$this->order->order();
			$amount = $this->order->getActualPrice();
			$amount = number_format($amount, 2, '.', '');
			$token = md5($merchant_id . $product_id . $amount . $this->order->getId() . $secret_word);

			$successUrl = (!$this->ok_url) ? $www . '/emarket/purchase/result/successful/' : $this->_http($this->ok_url);
			$failUrl = (!$this->ko_url) ? $www . '/emarket/purchase/result/failed/' : $this->_http($this->ko_url);
			$answerUrl = $www . '/emarket/gateway/' . $this->order->getId();

			$param = array();
			$param["formAction"] = 'https://secure.acquiropay.com/';
			$param["product_id"] = $product_id;
			$param["amount"] = $amount;
			$param["language"] = $language;
			$param["order_id"] = $this->order->getId();
			$param["ok_url"] = $successUrl;
			$param["cb_url"] = $answerUrl;
			$param["ko_url"] = $failUrl;
			$param["token"] = $token;

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/acquiropay/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * {@inheritdoc)
		 * Потверждает валидность заказа в платежной системе.
		 * Записывает в заказ в UMI.CMS номер платежного документа
		 * и меняет его статус оплаты, в зависимости от результата валидации:
		 *
		 * "Принята"/"Отклонена"
		 */
		public function poll() {
			if (!getRequest('payment_id')) {
				return false;
			}

			$merchant_id = $this->getValue('merchant_id');
			$secret_word = $this->getValue('secret_word');

			$payment_id = getRequest('payment_id');
			$status = getRequest('status');
			$cf = getRequest('cf');
			$amount = getRequest('amount');

			$hashString = md5($merchant_id . $payment_id . $status . $cf . $secret_word);

			if (strcasecmp($hashString, getRequest('sign')) != 0 ) {
				return false;
			}

			if (($this->order->getActualPrice() - $amount) != 0 ) {
				return false;
			}

			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType('text/plain');

			try {
				$this->order->payment_document_num = $payment_id;
				$this->order->commit();

				if ($status == 'OK' ) {
					$this->order->setPaymentStatus('accepted');
					$buffer->push('success');
				} else {
					$this->order->setPaymentStatus('declined');
					$buffer->push('fail');
				}

			} catch (Exception $e) {
				$buffer->push('fail');
			}

			$buffer->end();
		}

		/**
		 * {@inheritdoc)
		 */
		public static function getOrderId() {
			return (int) getRequest('cf');
		}

		/**
		 * Добавляет к адресу страницы префикс с протоколом,
		 * если он не был добавлен ранее
		 * @param string $url адрес страницы
		 * @return string
		 */
		private function _http($url) {
			return strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0 ? $url : 'http://' . $url;
		}
	}
?>