<?php
	/**
	 * Внутренний способ оплаты "Наличными курьеру"
	 */
	class courierPayment extends payment {

		/**
		 * {@inheritdoc}
		 */
		public function validate() {
			return true;
		}

		/**
		 * {@inheritdoc}
		 * Перенаправляет на страницу успешного оформления заказа
		 */
		public function process($template = null) {
			$order = $this->order;
			$order->order();
			$controller = cmsController::getInstance();
			$module = $controller->getModule("emarket");

			if ($module) {
				$module->redirect($controller->getPreLang() . '/emarket/purchase/result/successful/');
			}

			return null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function poll() {
			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->clear();
			$buffer->contentType('text/plain');
			$buffer->push('Sorry, but this payment system doesn\'t support server polling.' . getRequest('param0'));
			$buffer->end();
		}
	};
?>
