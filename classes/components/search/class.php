<?php
	/**
	 * Базовый класс модуля "Поиск".
	 *
	 * Модуль отвечает за:
	 *
	 * 1) Индексацию страниц;
	 * 2) Поиск по индексу;
	 * 3) Шаблонизация результата поиска;
	 * 4) Поиск и замену строк в контенте страниц;
	 * @link http://help.docs.umi-cms.ru/rabota_s_modulyami/modul_poisk/
	 */
	class search extends def_module {

		/**
		 * @var array список имен модулей, по страницам которых может осуществляться поиск
		 */
		public static $permissibleTypesModules = array(
			'catalog',
			'news',
			'blogs20',
			'forum',
			'faq',
			'content'
		);

		/**
		 * Конструктор
		 */
		public function __construct() {
			parent::__construct();
			$cmsController = cmsController::getInstance();
			$this->per_page = regedit::getInstance()->getVal("//modules/search/per_page");

			if ($cmsController->getCurrentMode() == "admin") {
				$commonTabs = $this->getCommonTabs();

				$umiConfig = mainConfiguration::getInstance();
				$controlTabName = ($umiConfig->get('modules', 'search.using-sphinx')) ? 'sphinx_control' : 'index_control';

				if ($commonTabs) {
					$commonTabs->add($controlTabName);
					$commonTabs->add('search_replace');
				}

				$configTabs = $this->getConfigTabs();

				if ($configTabs) {
					$configTabs->add("config");
				}

				$this->__loadLib("admin.php");
				$this->__implement("SearchAdmin");

				$this->loadAdminExtension();

				$this->__loadLib("customAdmin.php");
				$this->__implement("SearchCustomAdmin", true);
			}

			$this->__loadLib("macros.php");
			$this->__implement("SearchMacros");

			$this->loadSiteExtension();

			$this->__loadLib("customMacros.php");
			$this->__implement("SearchCustomMacros", true);

			$this->__loadLib("sphinx.php");
			$this->__implement("SphinxSearch");

			$this->loadCommonExtension();
			$this->loadTemplateCustoms();
		}


		/**
		 * Возвращает данные о иерархическом типе найденного элемента
		 * @param iUmiHierarchyElement $element объект найденного элемента
		 * @return array
		 */
		public function getTypeInfo(iUmiHierarchyElement $element) {
			/**
			 * @var iUmiHierarchyType|umiHierarchyType|iUmiEntinty $type
			 */
			$type = $element->getHierarchyType();

			if (!$type instanceof iUmiHierarchyType) {
				return array();
			}

			$labelPrefix = 'module-';
			$moduleTitle = getLabel($labelPrefix . $type->getModule());

			return array(
				'@name' => $type->getTitle(),
				'@module' => $moduleTitle,
				'@id'	=> $type->getId()
			);
		}

		/**
		 * Возвращает список ID иерархических типов, по которым будет осуществляться поиск
		 * @param string $typesFromMacro иерархические типы, которые были переданы в макрос
		 * @return array|mixed
		 */
		public function getSearchTypes($typesFromMacro = '') {
			$types = ($typesFromMacro) ? $typesFromMacro : (string) getRequest('search_types');

			if (!$types)  {
				$types = regedit::getInstance()->getVal('//modules/search/search-types');
			}

			$types = rawurldecode($types);

			if (strlen($types)) {
				$types = preg_split("/[\s,]+/", $types);
				$types = array_map('intval', $types);
			}

			return $types;
		}

		/**
		 * Возвращает список иерархических типов, по страницах которых доступен поиск
		 * @return array
		 */
		public function getPermissibleTypes() {
			$hierarchyTypes = umiHierarchyTypesCollection::getInstance();
			return $hierarchyTypes->getTypesByModules(self::$permissibleTypesModules);
		}

		/**
		 * Возвращает список выбранных иерархических типов для поиска
		 * @param array $savedTypes ID выбранных иерархических типов
		 * @return array
		 */
		public function getSelectedTypes($savedTypes) {
			return array_filter($this->getPermissibleTypes(), function($type) use ($savedTypes) {
				/**
				 * @var iUmiEntinty $type
				 */
				return in_array($type->getId(), $savedTypes);
			});
		}

		/**
		 * Очищает поисковый индекс
		 */
		public function truncate() {
			searchModel::getInstance()->truncate_index();
			$this->redirect($this->pre_lang . "/admin/search/");
		}

		/**
		 * Запускает одну итерацию переиндексации
		 */
		public function reindex() {
			searchModel::getInstance()->index_all();
			$this->redirect($this->pre_lang . "/admin/search/");
		}

		/**
		 * Возвращает данные иерархических типов, по которым будет производиться поиск
		 * @return array
		 */
		public function getSearchTypesOption() {
			$regEdit = regedit::getInstance();
			$searchTypesString = $regEdit->getVal('//modules/search/search-types');
			$searchTypes = explode(umiObjectPropertyRelation::DELIMITER_ID, $searchTypesString);

			$searchTypesIds = array_map(function($typeId) {
				return intval(trim($typeId));
			}, $searchTypes);

			$searchTypes = $this->getSelectedTypes($searchTypesIds);
			$permissibleTypes = $this->getPermissibleTypes();
			$items = array();

			/**
			 * @var int $typeId
			 * @var umiHierarchyType $type
			 */
			foreach ($permissibleTypes as $typeId => $type) {
				$item = array();

				if (isset($searchTypes[$typeId])) {
					$item['@selected'] = 'selected';
				}

				$item['node:value'] = $type->getTitle();
				$item['@id'] = $type->getId();
				$items[] = $item;
			}

			return array('nodes:item' => $items);
		}

		/**
		 * Производит замену вхождений строки
		 * @param string $content область поиска
		 * @param string $search искомое значение
		 * @param string $replace значение, на которое нужно заменить найденные вхождения искомой строки
		 * @param int $mode режим работы:
		 * 		1 - замена в url'ах, 2 - замена только в тексте, все остальное - замена везде
		 * @return string
		 */
		public function replaceText($content, $search, $replace, $mode){
			switch ($mode) {
				case 1: {
					preg_match_all('#href=[\"| \'](.*?)[\"| \']#', $content, $matches);
					$urls = array();

					foreach ($matches[0] as $url) {
						$urls[] = array(
							'source' => $url,
							'result' => str_replace($search, $replace, $url)
						);
					}

					foreach ($urls as $url) {
						$content = str_replace($url['source'], $url['result'], $content);
					}

					return $content;
				}
				case 2: {
					preg_match_all('#href=[\"| \'](.*?)[\"| \']#', $content, $matches);

					foreach ($matches[1] as $key => $url){
						$content = str_replace($url, '[URL-' . $key . ']', $content);
					}

					$content = str_replace($search, $replace, $content);

					foreach ($matches[1] as $key => $url) {
						$content = preg_replace('/(\[URL\-' . $key . '+\])/', $url, $content);
					}

					return $content;
				}
 				default: {
					return str_replace($search, $replace, $content);
				}
			}
		}

		/**
		 * Оформляет вхождение строки в контент поля
		 * @param string $content область поиска
		 * @param string $searchString искомое значение
		 * @param string $type название поля
		 * @param int $mode режим работы:
		 * 		1 - вхождения в url'ах, 2 - вхождения только в тексте, все остальное - вхождения везде
		 * @param string $modeLabel идентификатор типа вхождения (link|text)
		 * @return string
		 */
		public function searchWithSnippet($content, $searchString, $type, $mode, $modeLabel){
			$result = false;

			switch ($mode) {
				case 1: {
					preg_match_all('#href=[\"| \'](.*?)[\"| \']#', $content, $matches);

					foreach($matches[1] as $url) {
						$result .= $this->searchWithSnippet($url, $searchString, $type, 0, $modeLabel);
					}

					return $result;
				}
				case 2: {
					$content = preg_replace('#<a[^>]+>#i', '', $content);
					break;
				}
			}

			while (strpos($content, $searchString) !== false){
				$position = mb_strpos($content, $searchString);
				$snippet = mb_substr($content, ($position - 50 < 0) ? 0 : $position - 50, mb_strlen($searchString) + 100);
				$snippet = htmlspecialchars($snippet);
				$searchString = htmlspecialchars($searchString);
				$result .= $type .'(' . $modeLabel .')' .': ...' . str_replace($searchString,'<span style="background: yellow;">' . $searchString .'</span>', $snippet) . '... <br />';
				$content = substr_replace($content,'', strpos($content, $searchString), strlen($searchString));
			}

			return $result;
		}
	};
?>
