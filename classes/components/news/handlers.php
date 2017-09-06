<?php
	/**
	 * Класс обработчиков событий
	 */
	class NewsHandlers {
		/**
		 * @var news $module
		 */
		public $module;

		/**
		 * Обработчик события срабатывания системного крона.
		 * Активирует новости с подходящей датой начала активности.
		 * @param iUmiEventPoint $event событие срабатывания системного крона
		 * @throws selectorException
		 */
		public function cronActivateNews(iUmiEventPoint $event) {
			$pages = new selector('pages');
			$pages->types('hierarchy-type')->name('news', 'item');
			$pages->where('is_active')->notequals(true);
			$pages->where('begin_time')->eqless(time());
			$pages->option('no-length')->value(true);

			if (!$pages->first) {
				return;
			}

			/**
			 * @var iUmiHierarchyElement|iUmiEntinty $page
			 */
			foreach ($pages as $page) {
				$page->setIsActive(true);
				$page->commit();
			}
		}

		/**
		 * Обработчик события срабатывания системного крона.
		 * Импортирует все RSS-фиды.
		 * @param iUmiEventPoint $event событие срабатывания системного крона
		 * @return boolean
		 */
		public function feedsImportListener(iUmiEventPoint $event) {
			$counter = &$event->getRef("counter");
			$buffer = &$event->getRef("buffer");
			$counter++;

			try {
				/** @var NewsFeeds $newsFeeds */
				$newsFeeds = $this->module->getImplementedInstance('NewsFeeds');
			} catch (coreException $e) {
				return false;
			}

			$buffer[ __METHOD__] = $newsFeeds->import_feeds();
			return true;
		}
	}
?>