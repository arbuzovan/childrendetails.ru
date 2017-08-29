<?php
	/**
	 * Группы прав на функционал модуля
	 */
	$permissions = Array(
		/**
		 * Права на просмотр меню
		 */
		'view' => Array(
			'draw'
		),
		/**
		 * Права на администрирование модуля
		 */
		'lists' => Array(
			'lists',
			'add',
			'edit',
			'del',
			'activity',
			'item_element.edit',
			'publish'
		)
	);
?>