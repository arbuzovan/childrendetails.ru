<?php
	/**
	 * Группы прав на функционал модуля
	 */
	$permissions = [
		/**
		 * Права на оформление заказа
		 */
		'purchasing' => [
			/**
			 * Кастомизированное оформление заказа в 1 шаг
			 */
                        'custom_basket',
                        'one_step_order',
			'deliveryListCustom',
                        'getPersonalReccomendation',
                        'addNewAdress',
                        'oneClickOrder',
                        'backCallRequest',
                        'getOrderState'
		]
	];
?>
