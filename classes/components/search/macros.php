<?php

	/**
	 * Класс макросов, то есть методов, доступных в шаблоне
	 */
	class SearchMacros {
		/**
		 * @var search $module
		 */
		public $module;

		/**
		 * Возвращает результат работы поиска
		 * @param string $template имя шаблона (для tpl)
		 * @param string $searchString поисковая строка
		 * @param string $types список идентификаторов иерархических типов для поиска
		 * (указываются через пробел)
		 * @param string $branches Список разделов в которых будет осуществляться поиск
		 * (указываются через пробел).
		 * @param int $perPage количество выводимых результатов в рамках пагинации
		 * @return mixed
		 */
		public function search_do($template = 'default', $searchString = '', $types = '', $branches = '', $perPage = 0) {
			$searchString = $searchString ?: (string) getRequest('search_string');
			$perPage = $perPage ?: (int) getRequest('per_page');
			$perPage = $perPage ?: $this->module->per_page;
			$currentPage = (int) getRequest('p');

			$config = mainConfiguration::getInstance();
			$searchEngine = $config->get('modules', 'search.using-sphinx');

			if ($searchEngine) {
				return $this->sphinxSearch($template, $searchString, $perPage, $currentPage);
			}

			list(
				$templateBlock,
				$templateLine,
				$templateEmptyResult,
				$templateLineQuant
				) = search::loadTemplates('search/' . $template,
				'search_block',
				'search_block_line',
				'search_empty_result',
				'search_block_line_quant'
			);

			$variables = [];
			$variables['last_search_string'] = htmlspecialchars($searchString);

			$searchString = urldecode($searchString);
			$searchString = htmlspecialchars($searchString);
			$searchString = str_replace('. ', ' ', $searchString);
			$searchString = trim($searchString, " \t\r\n%");
			$searchString = str_replace(['"', "'"], '', $searchString);

			$orMode = (bool) getRequest('search-or-mode');

			if (!$searchString) {
				return $this->insert_form($template);
			}

			$searchParentsIds = [];
			$branches = $branches ?: (string) getRequest('search_branches');
			$branches = trim(rawurldecode($branches));

			if (strlen($branches)) {
				$arrBranches = preg_split("/[\s,]+/", $branches);

				foreach ($arrBranches as $iBranch => $vBranch) {
					$arrBranches[$iBranch] = $this->module->analyzeRequiredPath($vBranch);
				}

				$arrBranches = array_map('intval', $arrBranches);
				$searchParentsIds = array_merge($searchParentsIds, $arrBranches);

				$sel = new selector('pages');

				foreach ($arrBranches as $parentId) {
					$sel->where('hierarchy')->page($parentId)->level(100);
				}

				$sel->option('return')->value('id');
				$pageIds = $sel->result();

				foreach ($pageIds as $info) {
					$searchParentsIds[] = $info['id'];
				}
			}

			$types = $this->module->getSearchTypes($types);

			$searchModel = searchModel::getInstance();
			$pageIds = $searchModel->runSearch($searchString, $types, $searchParentsIds, $orMode);
			$total = count($pageIds);
			$pageIds = array_slice($pageIds, $perPage * $currentPage, $perPage);

			$umiLinksHelper = umiLinksHelper::getInstance();

			$i = $perPage * $currentPage;
			$umiHierarchy = umiHierarchy::getInstance();
			$umiHierarchy->loadElements($pageIds);
			$lines = [];

			foreach ($pageIds as $index => $pageId) {
				$page = $umiHierarchy->getElement($pageId);

				if (!$page instanceof iUmiHierarchyElement) {
					continue;
				}

				$itemVariables = [];
				$itemVariables['type'] = $this->module->getTypeInfo($page);
				$itemVariables['void:num'] = ++$i;
				$itemVariables['attribute:id'] = $pageId;
				$itemVariables['attribute:name'] = $page->getName();
				$itemVariables['attribute:link'] = $umiLinksHelper->getLink($page);
				$itemVariables['xlink:href'] = 'upage://' . $pageId;
				$itemVariables['node:context'] = $searchModel->getContext($pageId, $searchString);
				$itemVariables['void:quant'] = ($index < count($pageIds) - 1 ? search::parseTemplate($templateLineQuant, []) : '');
				$lines[] = search::parseTemplate($templateLine, $itemVariables, $pageId);

				search::pushEditable(false, false, $pageId);
				$umiHierarchy->unloadElement($pageId);
			}

			$variables['subnodes:items'] = $variables['void:lines'] = $lines;
			$variables['total'] = $total;
			$variables['per_page'] = $perPage;

			return search::parseTemplate(($total > 0 ? $templateBlock : $templateEmptyResult), $variables);
		}

		/**
		 * Возвращает данные для построения формы поиска
		 * @param string $template имя шаблона (для tpl)
		 * @return mixed
		 */
		public function insert_form($template = 'default') {
			list($template_block) = search::loadTemplates('search/' . $template, 'search_form');

			$search_string = (string) getRequest('search_string');
			$search_string = strip_tags($search_string);
			$search_string = trim($search_string, " \t\r\n%");
			$search_string = htmlspecialchars(urldecode($search_string));
			$search_string = str_replace(['"', "'"], '', $search_string);

			$orMode = (bool) getRequest('search-or-mode');

			$block_arr = [];
			$block_arr['last_search_string'] = ($search_string) ? $search_string : '%search_input_text%';

			if ($orMode) {
				$block_arr['void:search_mode_and_checked'] = '';
				$block_arr['void:search_mode_or_checked'] = ' checked';
			} else {
				$block_arr['void:search_mode_and_checked'] = ' checked';
				$block_arr['void:search_mode_or_checked'] = '';
			}

			return search::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Возвращает подсказки для поисковой фразы
		 * @param string $template имя шаблона (для tpl)
		 * @param bool|string $string поисковая строка
		 * @param int $limit ограничение на количество
		 * @return mixed
		 */
		public function suggestions($template = 'default', $string = false, $limit = 10) {
			if ($string == false) {
				$string = getRequest('suggest-string');
			}

			list($template_block, $template_line, $template_block_empty) = search::loadTemplates(
					'tpls/search/' . $template, 'suggestion_block', 'suggestion_block_line', 'suggestion_block_empty'
			);

			$search = searchModel::getInstance();
			$words = $search->suggestions($string, $limit);
			$total = sizeof($words);

			if ($total == 0) {
				return search::parseTemplate($template_block_empty, []);
			}

			$items_arr = [];
			foreach ($words as $word) {
				$item_arr = [
						'attribute:count' => $word['cnt'],
						'node:word' => $word['word']
				];

				$items_arr[] = search::parseTemplate($template_line, $item_arr);
			}

			$block_arr = [
					'words' => ['nodes:word' => $items_arr],
					'total' => $total
			];

			return search::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Возвращает результат работы поиска с помощью Sphinx
		 * @param string $template имя шаблона (для tpl)
		 * @param string $searchString поисковая фраза
		 * @param int $perPage количество элементов на странице
		 * @param int $p номер страницы
		 * @return mixed|string
		 */
		public function sphinxSearch($template = 'default', $searchString = '', $perPage = 0, $p) {
			if (!$searchString) {
				return $this->insert_form($template);
			}

			list(
				$templateBlock,
				$templateLine,
				$templateEmptyResult,
				$templateLineQuant
				) = search::loadTemplates('search/' . $template,
				'search_block',
				'search_block_line',
				'search_empty_result',
				'search_block_line_quant'
			);


			$index = '*';
			$limitResult = 1000;
			$config = mainConfiguration::getInstance();
			$sphinxHost = $config->get('sphinx', 'sphinx.host');
			$sphinxPort = (int) $config->get('sphinx', 'sphinx.port');

			$sphinx = new SphinxClient;
			$sphinx->SetServer($sphinxHost, $sphinxPort);
			if (!$sphinx->Open()) {
				return;
			}

			/**
			 * @var search|SphinxSearch $search
			 */
			$search = $this->module;
			$resultSphinx = $search->findResult($searchString, $limitResult, $index, $sphinx);

			if (empty($resultSphinx) || !array_key_exists('matches', $resultSphinx)) {
				return;
			}

			$resultMatches = $resultSphinx['matches'];
			$total = count($resultMatches);
			$resultMatches = array_slice($resultMatches, $perPage * $p, $perPage);

			$umiLinksHelper = umiLinksHelper::getInstance();
			$i = $perPage * $p;
			$items = [];

			foreach ($resultMatches as $num => $element) {
				$item = [];
				/** @var umiHierarchyElement $element */
				$page_weight = $element['weight'];
				$element = $element['page'];

				if (!$element) {
					continue;
				}

				$content = $element->getValue('content');
				$pattern = '/%[^\s](.*?)[^\s]%/i';
				$content = preg_replace($pattern, '', $content);

				$item['type'] = $search->getTypeInfo($element);
				$item['void:num'] = ++$i;
				$item['attribute:id'] = $element->getId();
				$item['attribute:name'] = $element->getName();
				$item['attribute:weight'] = $page_weight;
				$item['attribute:link'] = $umiLinksHelper->getLink($element);
				$item['xlink:href'] = 'upage://' . $element->getId();
				$item['node:context'] = '<p>' . $search->highlighter([$content], $searchString, $sphinx) . '</p>';
				$item['void:quant'] = ($num < count($resultMatches) - 1 ? search::parseTemplate($templateLineQuant, []) : '');
				$items[] = search::parseTemplate($templateLine, $item, $element->getId());

				search::pushEditable(false, false, $element->getId());
				umiHierarchy::getInstance()->unloadElement($element->getId());
			}

			$result = [];
			$result['subnodes:items'] = $result['void:lines'] = $items;
			$result['total'] = $total;
			$result['per_page'] = $perPage;
			$result['last_search_string'] = '';

			return search::parseTemplate(($total > 0 ? $templateBlock : $templateEmptyResult), $result);
		}
	}

?>