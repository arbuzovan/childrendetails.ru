<?php
	/**
	 * Класс пользовательских макросов
	 */
	class NewsCustomMacros {
		/**
		 * @var news $module
		 */
		public $module;
                
		/**
		 * Возвращает данные последних новостей.
		 * @link http://dev.docs.umi-cms.ru/spravochnik_makrosov_umicms/novosti/news_lastlist/
		 * @param string $path путь до ленты новостей, либо ID ленты новостей
		 * @param string $template имя шаблона, по которому следует вывести список последних новостей
		 * @param bool|int $per_page количество новостей на одной странице
		 * @param bool $ignore_paging игнорировать постраничную навигацию
		 * @param string $sDaysInterval строка, определяющая временной интервал по отношению к моменту начала отработки макроса,
		 * которым следует ограничить список выводимых новостей, а также порядок вывода новостей.
		 * По умолчанию (пустая строка), выводятся все новости, имеющиеся в ленте/лентах в порядке
		 * уменьшения даты публикации новости (чем новость более «свежая», тем «выше» ее позиция в списке),
		 * что соответствует также значению параметра, равному «+ -». Формат строки-значения параметра следующий: «[+-]?\d?[mhd]?\s?[+-]?\d?[mhd]?».
		 * Это два числа, разделенных пробелом, которым предшествуют знаки плюс или минус,
		 * и за которыми следует латинская буква m, h или d. Например: «+10d -5d».
		 * Эти два числа задают две границы временного интервала ограничения выборки.
		 * @param bool $bSkipOrderByTime Если параметр не указан, то новости сортируются
		 * по дате публикации (последняя — в начале списка). Если указать «1»,
		 * то новости выводятся в порядке их следования в иерархии
		 * @param int $level уровень вложенности искомых новостей, относительно родительской ленты новостей
		 * @return mixed
		 * @throws publicException
		 * @throws selectorException
		 */
		public function cusotm_lastlist(
			$path = '',
			$template = 'default',
			$per_page = false,
			$ignore_paging = false,
			$sDaysInterval = '',
			$bSkipOrderByTime = false,
			$level = 1
		) {
			if (!$per_page) {
				$per_page = $this->module->per_page;
			}

			$per_page = intval($per_page);

			if (strlen($sDaysInterval)) {
				$sStartDaysOffset = '';
				$sFinishDaysOffset = '';
				$arrDaysInterval = preg_split("/\s+/is", $sDaysInterval);
				if (isset($arrDaysInterval[0])) {
					$sStartDaysOffset = $arrDaysInterval[0];
				}
				if (isset($arrDaysInterval[1])) {
					$sFinishDaysOffset = $arrDaysInterval[1];
				}

				$iNowTime = time();

				if ($sStartDaysOffset === '+') {
					$iStartDaysOffset = (PHP_INT_MAX - $iNowTime);
				} elseif ($sStartDaysOffset === '-') {
					$iStartDaysOffset = (0 - PHP_INT_MAX + $iNowTime);
				} else {
					$iStartDaysOffset = intval($sStartDaysOffset);
					$sPostfix = substr($sStartDaysOffset, -1);

					if ($sPostfix === 'm') {
						$iStartDaysOffset *= (60);
					} elseif ($sPostfix === 'h' || $sPostfix === 'H') {
						$iStartDaysOffset *= (60*60);
					} else {
						$iStartDaysOffset *= (60*60*24);
					}
				}

				if ($sFinishDaysOffset === '+') {
					$iFinishDaysOffset = (PHP_INT_MAX - $iNowTime);
				} elseif ($sFinishDaysOffset === '-') {
					$iFinishDaysOffset = (0 - PHP_INT_MAX + $iNowTime);
				} else {
					$iFinishDaysOffset = intval($sFinishDaysOffset);
					$sPostfix = substr($sFinishDaysOffset, -1);

					if ($sPostfix === 'm') {
						$iFinishDaysOffset *= (60);
					} elseif ($sPostfix === 'h' || $sPostfix === 'H') {
						$iFinishDaysOffset *= (60*60);
					} else {
						$iFinishDaysOffset *= (60*60*24);
					}
				}

				$iPeriodStart = $iNowTime + $iStartDaysOffset;
				$iPeriodFinish = $iNowTime + $iFinishDaysOffset;
				$bPeriodOrder = ($iPeriodStart >= $iPeriodFinish ? false : true);
			} else {
				$iPeriodStart = false;
				$iPeriodFinish = false;
				$bPeriodOrder = false;
			}

			$moduleClass = $this->module;

			list($template_block, $template_block_empty, $template_line, $template_archive) = $moduleClass::loadTemplates(
				'news/' . $template,
				'lastlist_block',
				'lastlist_block_empty',
				'lastlist_item',
				'lastlist_archive'
			);
			$curr_page = (int) getRequest('p');

			if ($ignore_paging) {
				$curr_page = 0;
			}

			$parentId = $this->module->analyzeRequiredPath($path);

			if ($parentId === false && $path != KEYWORD_GRAB_ALL) {
				throw new publicException(getLabel('error-page-does-not-exist', null, $path));
			}

			$umiLinksHelper = umiLinksHelper::getInstance();
			$umiLinksHelper->loadLinkPartForPages([$parentId]);

			$month = (int) getRequest('month');
			$year = (int) getRequest('year');
			$day = (int) getRequest('day');

			$news = new selector('pages');
			$news->types('hierarchy-type')->name('news', 'item');
			if ($path != KEYWORD_GRAB_ALL) {
				$escapedLevel = (int) $level;
				$escapedLevel = ($escapedLevel === 0) ? 1 : $escapedLevel;

				if (is_array($parentId)) {
					foreach ($parentId as $parent) {
						$news->where('hierarchy')->page($parent)->level($escapedLevel);
					}
				} else {
					$news->where('hierarchy')->page($parentId)->level($escapedLevel);
				}
			}
			if (!empty($month) && !empty($year) && !empty($day)) {
				$date1 = mktime(0, 0, 0, $month, $day, $year);
				$date2 = mktime(23, 59, 59, $month, $day, $year);
				$news->where('publish_time')->between($date1, $date2);
			} elseif (!empty($month) && !empty($year)) {
				$date1 = mktime(0, 0, 0, $month, 1, $year);
				$date2 = mktime(23, 59, 59, $month+1, 0, $year);
				$news->where('publish_time')->between($date1, $date2);
			} elseif( !empty($year)) {
				$date1 = mktime(0, 0, 0, 1, 1, $year);
				$date2 = mktime(23, 59, 59, 12, 31, $year);
				$news->where('publish_time')->between($date1, $date2);
			} elseif ($iPeriodStart !== $iPeriodFinish) {
				if($iPeriodStart != false && $iPeriodFinish != false) {
					if($sDaysInterval && $sDaysInterval != '+ -') {
						if ($iPeriodStart < $iPeriodFinish) {
							$news->where('publish_time')->between($iPeriodStart, $iPeriodFinish);
						} else {
							$news->where('publish_time')->between($iPeriodFinish, $iPeriodStart);
						}
					}
				}
			}

			if (!$bSkipOrderByTime) {
				if ($bPeriodOrder === true) {
					$news->order('publish_time')->asc();
				} else {
					$news->order('publish_time')->desc();
				}
			}

			selectorHelper::detectFilters($news);
			$news->option('load-all-props')->value(true);
			$news->limit($curr_page * $per_page, $per_page);

			$result = $news->result();
			$total = $news->length();

			$umiHierarchy = umiHierarchy::getInstance();
			$moduleClass = $this->module;

			if (($sz = sizeof($result)) == 0) {
				return $template_block_empty;
			}

			$block_arr = [];
			$lines = [];

			foreach ($result as $element) {

				if (!$element instanceof umiHierarchyElement) {
					continue;
				}

				$element_id = $element->getId();

				$line_arr = [];
				$line_arr['attribute:id'] = $element_id;
				$line_arr['node:name'] = $element->getName();
				$line_arr['attribute:link'] = $umiLinksHelper->getLinkByParts($element);
				$line_arr['xlink:href'] = 'upage://' . $element_id;
				$line_arr['void:header'] = $lines_arr['name'] = $element->getName();

				if ($publish_time = $element->getValue('publish_time')) {
					$line_arr['attribute:publish_time'] = $publish_time->getFormattedDate('U');
				}

				$lent_name = '';
				$lent_link = '';
				$lent_id = $element->getParentId();

				if ($lent_element = $umiHierarchy->getElement($lent_id)) {
					$lent_name = $lent_element->getName();
					$lent_link = $umiLinksHelper->getLinkByParts($lent_element);
				}

				$line_arr['attribute:lent_id'] = $lent_id;
				$line_arr['attribute:lent_name'] = $lent_name;
				$line_arr['attribute:lent_link'] = $lent_link;

				$lines[] = $moduleClass::parseTemplate($template_line, $line_arr, $element_id);
				$moduleClass::pushEditable('news', 'item', $element_id);
				$umiHierarchy->unloadElement($element_id);
			}

			if (is_array($parentId)) {
				list($parentId) = $parentId;
			}

			$block_arr['subnodes:items'] = $block_arr['void:lines'] = $lines;
			$block_arr['archive'] = ($total > 0) ? $template_archive : '';
			$parent = $umiHierarchy->getElement($parentId);

			if ($parent instanceof umiHierarchyElement) {
				$block_arr['archive_link'] = $umiLinksHelper->getLinkByParts($parent);
			}

			$block_arr['total'] = $total;
			$block_arr['per_page'] = $per_page;
			$block_arr['category_id'] = $parentId;

			return $moduleClass::parseTemplate($template_block, $block_arr, $parentId);
		}
                
	}
?>