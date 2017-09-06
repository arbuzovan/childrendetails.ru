<?php
	/**
	 * Импортирует все фиды по срабатыванию системного крона
	 */
	$onCronNewsRead = new umiEventListener("cron", "news", "feedsImportListener");
	/**
	 * Активирует новости с подходящей датой публикации по срабатыванию системного крона
	 */
	$onCronActivateNews = new umiEventListener("cron", "news", "cronActivateNews");
?>