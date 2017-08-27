<?php
	/**
	 * Группы прав на функционал модуля
	 */
	$permissions = [
		/**
		 * Права на оформление заказа
		 */
		'purchasing' => [
			'price',
			'stores',
			'basketaddlink',
			'basketaddfastlink',
			'fastpurchase',
			'applypricecurrency',
			'order',
			'basket',
			'cart',
			'yandex_market',
			'purchase',
			'gateway',
			'callback',
			'receipt',
			'removedeliveryaddress',
			'currencyselector',
			'getcustomerinfo',
			'selectcurrency',
			'discountinfo',
			'getpurchaselink',
			'getinvoicelink',
			'getinvoice',
			'getapishippointsbyproviderandcity',
			'getorderdeliveryaddress',
			'getapishipdeliveryoptions',
			'getapishipdeliverypointstypes',
			'getapishipprovidertariffbyid',
			'getapishipdeliverypointbyid',
			'resetapishipcredentials',
			/**
			 * Оформление заказа в 1 шаг
			 */
			'purchasing_one_step',
			'customerDeliveryList',
			'personalInfo',
			'paymentsList',
			'saveInfo',
			/**
			 * Оформление заказа в 1 клик
			 */
			'createform',
			'getoneclickorder',
			'validoneclickinfo',
		],
		/**
		 * Права на личный кабинет покупателя
		 */
		'personal' => [
			'personal',
			'orderslist',
		],
		/**
		 * Права на сравнение товаров
		 */
		'compare' => [
			'getcomparelist',
			'getcomparelink',
			'addtocompare',
			'removefromcompare',
			'resetcomparelist',
			'jsonaddtocomparelist',
			'jsonremovefromcompare',
			'jsonresetcomparelist',
		],
		/**
		 * Права на администрирование модуля
		 */
		'control' => [
			'orders',
			'ordersList',
			'del',
			'order_edit',
			'order_printable',
			'currency',
			'currency_add',
			'currency_edit',
			'delivery',
			'delivery_add',
			'delivery_edit',
			'delivery_address_edit',
			'discounts',
			'discount_add',
			'discount_edit',
			'getmodificators',
			'getrules',
			'payment',
			'payment_add',
			'payment_edit',
			'stores',
			'store_add',
			'store_edit',
			'stats',
			'realpayments',
			'setdaterange',
			'getdaterange',
			'getmostpopularproduct',
			'statrun',
			'order.edit',
			'currency.edit',
			'delivery.edit',
			'delivery_address.edit',
			'discount.edit',
			'payment.edit',
			'store.edit',
			'getapishipchosenproviderssettings',
			'refreshapishipordersstatuses',
			'getapishiporders',
			'cancelapishiporder',
			'saveapishipuser',
			'getapishiplabel',
			'getapishipwaybill',
			'getapishippointsbyproviderandcity',
			'getapishipdeliveryoptions',
			'sendapishiporderrequest',
			'getapishipproviderdeliverytypes',
			'getapishipproviderpickuptypes',
			'getapishipprovidertariffs',
			'getapishipprovidersettings',
			'setapishipprovidersettings',
			'getapishipsupporteddeliverytypes',
			'getapishipsupportedpickuptypes',
			'getapishipallproviders',
			'connecttoapishipprovider',
			'getapishipdeliverypointbyid',
			'getapishipprovidertariffbyid',
			'getapishipdeliverypointstypes',
			'getapishippointsbyprovider',
			'getapishipdatasetconfiguration',
			'flushdefaultstoreattributes',
			'isordersenttoapiship',
			'updateapishipproviderconnection',
			'sendapishipupdateorderrequest'
		],
		/**
		 * Права на оформление и изменение заказа от имени покупателя
		 */
		'order_editing' => [
			'editorderasuser',
			'actasuser'
		],
		/**
		 * Права на api для мобильного приложения
		 */
		'mobile_application_get_data' => [
			'getorderstatuses',
			'getordersbystatus',
			'getorder',
			'setorder',
			'addtoken',
			'removetoken'
		]
	];
?>
