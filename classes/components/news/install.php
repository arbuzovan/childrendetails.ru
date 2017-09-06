<?php
	/**
	 * Установщик модуля
	 */

	/**
	 * @var array $INFO реестр модуля
	 */
	$INFO = array();
	$INFO['name'] = "news";
	$INFO['config'] = "1";
	$INFO['default_method'] = "rubric";
	$INFO['default_method_admin'] = "lists";
	$INFO['per_page'] = "10";
	$INFO['rss_per_page'] = "10";

	/**
	 * @var array $COMPONENTS файлы модуля
	 */
	$COMPONENTS = array();
	$COMPONENTS[] = "./classes/modules/components/news/admin.php";
	$COMPONENTS[] = "./classes/modules/components/news/calendar.php";
	$COMPONENTS[] = "./classes/modules/components/news/class.php";
	$COMPONENTS[] = "./classes/modules/components/news/customAdmin.php";
	$COMPONENTS[] = "./classes/modules/components/news/customMacros.php";
	$COMPONENTS[] = "./classes/modules/components/news/events.php";
	$COMPONENTS[] = "./classes/modules/components/news/handlers.php";
	$COMPONENTS[] = "./classes/modules/components/news/i18n.en.php";
	$COMPONENTS[] = "./classes/modules/components/news/i18n.php";
	$COMPONENTS[] = "./classes/modules/components/news/includes.php";
	$COMPONENTS[] = "./classes/modules/components/news/install.php";
	$COMPONENTS[] = "./classes/modules/components/news/lang.en.php";
	$COMPONENTS[] = "./classes/modules/components/news/lang.php";
	$COMPONENTS[] = "./classes/modules/components/news/macros.php";
	$COMPONENTS[] = "./classes/modules/components/news/permissions.php";
	$COMPONENTS[] = "./classes/modules/components/news/feeds.php";
?>
