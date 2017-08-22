<?php
	/**
	 * Способ оплаты через платежную систему "RBK Money"
	 */
	class rbkPayment extends payment {
		/**
		* Статусы платежа (см. документацию по подключению RBK Money)
		*/
		const STATUS_INPROCESS = 3;
		const STATUS_ACCEPTED  = 5;

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
			$currency = ($currency instanceof iUmiObject) ? $currency->getValue('codename') : 'RUR';
			$amount = number_format($this->order->getActualPrice(), 2, '.', '');

			$param = array();
			$param["formAction"] = "https://rbkmoney.ru/acceptpurchase.aspx";
			$param["eshopId"] = $this->object->getValue('eshopId');
			$param["orderId"] = $this->order->getId();
			$param["recipientAmount"] = $amount;
			$param["recipientCurrency"] = $currency;
			$param["version"] = "2";

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/rbk/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * {@inheritdoc}
		 * В зависимости от статуса платежа либо валидирует заказ с установлением соответствующего статуса оплаты
		 * (Проверена/Отклонена), либо переводит оплату заказа в статус "Принята".
		 */
		public function poll() {
			/**
			 * @var HTTPOutputBuffer  $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType("text/plain");

			if (!$this->checkSignature()) {
				$buffer->push("failed");
				$buffer->end();
			}

			$status = getRequest("paymentStatus");

			switch ($status) {
				case rbkPayment::STATUS_INPROCESS : {
					$recipientAmount = (float) getRequest("recipientAmount");
					$checkAmount = (float) $this->order->getActualPrice();

					if (($recipientAmount - $checkAmount) < (float) 0.001) {
						$this->order->setPaymentStatus('validated');
						$buffer->push("OK");
					} else {
						$this->order->setPaymentStatus('declined');
						$buffer->push("failed");
					}

					break;
				}
				case rbkPayment::STATUS_ACCEPTED  : {
					$this->order->setPaymentStatus('accepted');
					$buffer->push("OK");
					break;
				}
			}

			$buffer->end();
		}

		/**
		 * Проверяет подпись заказа из платежной системы
		 * @return bool
		 */
		private function checkSignature() {
			$eshopId = getRequest('eshopId');
			$orderId = getRequest('orderId');
			$serviceName = getRequest('serviceName');
			$eshopAccount = getRequest('eshopAccount');
			$recipientAmount = getRequest('recipientAmount');
			$recipientCurrency = getRequest('recipientCurrency');
			$paymentStatus = getRequest('paymentStatus');
			$userName = getRequest('userName');
			$userEmail = getRequest('userEmail');
			$paymentDate = getRequest('paymentData');
			$secretKey = $this->object->secretKey;
			$hash  = getRequest("hash");
			$check = md5("{$eshopId}::{$orderId}::{$serviceName}::{$eshopAccount}::{$recipientAmount}::{$recipientCurrency}::{$paymentStatus}::{$userName}::{$userEmail}::{$paymentDate}::{$secretKey}");
			return (bool)(strcasecmp($hash, $check) == 0);
		}
	};
?>
