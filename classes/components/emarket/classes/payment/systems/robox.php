<?php
	/**
	 * Способ оплаты через платежную систему "Robokassa"
	 */
	class roboxPayment extends payment {

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
			$login = $this->object->getValue('login');
			$password = $this->object->getValue('password1');

			$amount = number_format($this->order->getActualPrice(), 2, '.', '');
			$sign = md5("{$login}:{$amount}:{$this->order->id}:{$password}:shp_orderId={$this->order->id}");

			$param = array();
			$param['formAction'] = $this->object->getValue('test_mode') ? "http://test.robokassa.ru/Index.aspx" : "https://merchant.roboxchange.com/Index.aspx";
			$param['MrchLogin']  = $login;
			$param['OutSum']  	 = $amount;
			$param['InvId']  	 = $this->order->id;
			$param['Desc']  	 = "Payment for order {$this->order->id}";
			$param['SignatureValue'] = $sign;
			$param['shp_orderId']    = $this->order->id;
			$param['IncCurrLabel'] = "";
			$param['Culture']  	 = strtolower(cmsController::getInstance()->getCurrentLang()->getPrefix());

			$this->order->setPaymentStatus('initialized');

			list($templateString) = emarket::loadTemplates(
				"emarket/payment/robokassa/" . $template,
				"form_block"
			);

			return emarket::parseTemplate($templateString, $param);
		}

		/**
		 * Валидирует заказ платежной системы.
		 * Если заказа валиден - заказу в UMI.CMS
		 * устанавливается номер платежного документа
		 * и статус оплаты "Принята".
		 * @throws coreException
		 */
		public function poll() {
			$amount  = getRequest("OutSum");
			$invoice = getRequest("InvId");
			$sign    = getRequest("SignatureValue");
			$orderId = getRequest("shp_orderId");
			$password = $this->object->getValue('password2');
			$checkSign = md5("{$amount}:{$invoice}:{$password}:shp_orderId={$orderId}");
			$amount = (float) $amount;
			$orderActualPrice = (float) $this->order->getActualPrice();

			/**
			 * @var HTTPDocOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType("text/plain");

			if ((strcasecmp($checkSign, $sign) == 0) && ($orderActualPrice == $amount)) {
				$this->order->setPaymentStatus("accepted");
				$this->order->payment_document_num = $invoice;
				$this->order->commit();
				$buffer->push("OK{$invoice}");
			} else {
				$buffer->push("failed");
			}

			$buffer->end();
		}
	};
?>