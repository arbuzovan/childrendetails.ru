<?php

	/**
	 * Генератор номера заказа по умолчанию
	 */
	class defaultOrderNumber implements iOrderNumber {

		/** @var order $order заказ */
		protected $order;

		/** {@inheritdoc} */
		public function __construct(order $order) {
			$this->order = $order;
		}

		/**
		 * {@inheritdoc}
		 * @throws selectorException
		 */
		public function number() {
			$umiRegistry = regedit::getInstance();
			$lastOrderNumber = $umiRegistry->getVal('emarket/lastOrderNumber');

			if ($lastOrderNumber) {
				$number = $lastOrderNumber + 1;
				$umiRegistry->setVal('emarket/lastOrderNumber', $number);
				$this->setOrderNumber($number);
				return $number;
			}

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'order');
			$sel->order('number')->desc();
			$sel->limit(0, 1);
			$number = $sel->first ? ($sel->first->number + 1) : 1;

			$umiRegistry->setVal('emarket/lastOrderNumber', $number);
			$this->setOrderNumber($number);
			return $number;
		}

		/**
		 * Устанавливает номер заказа.
		 * @param int $number номер заказа
		 */
		private function setOrderNumber($number) {
			$this->order->setName(getLabel('order-name-prefix', 'emarket', $number));
			$this->order->setValue('number', $number);
			$this->order->commit();
		}

	}
