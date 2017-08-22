<?php
/**
 * @var array $parameters параметры инициализации сервисов
 */
$parameters = [
	'ApiShipOrders' => 'ApiShipOrders'
];

/**
 * @var array $rules правила инициализации сервисов
 */
$rules = [
	'ApiShipOrders' => [
		'class' => 'UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\Collection',
		'arguments' => [
			new ParameterReference('ApiShipOrders'),
		],
		'calls' => [
			[
				'method' => 'setConnection',
				'arguments' => [
					new ParameterReference('connection')
				]
			],
			[
				'method' => 'setMap',
				'arguments' => [
					new InstantiableReference('UmiCms\Classes\Components\Emarket\Delivery\ApiShip\Orders\ConstantMap')
				]
			]
		]
	]
];