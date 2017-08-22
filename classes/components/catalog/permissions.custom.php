<?php
	/**
	 * Группы прав на функционал модуля
	 */
	$permissions = [
		/**
		 * Права на администрирование каталога
		 */
		'tree' => [],
		/**
		 * Права на просмотр каталога
		 */
		'view' => [
                    'getCountSubCategries',
                    'getCountItems',
                    'getCountItemsPostfix'
                ]
	];
?>