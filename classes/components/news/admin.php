<?php

	/**
	 * Класс функционала административной панели
	 */
	class NewsAdmin {

		use baseModuleAdmin;
		/**
		 * @var news $module
		 */
		public $module;

		/**
		 * Возвращает настройки модуля.
		 * Если передан ключевой параметр $_REQUEST['param0'] = do,
		 * то сохраняет настройки.
		 * @throws coreException
		 */
		public function config() {
			$regEdit = regedit::getInstance();

			$params = [
				'config' => [
					'int:per_page' => null,
					'int:rss_per_page' => null
				]
			];

			$mode = getRequest('param0');

			if ($mode == 'do') {
				$params = $this->expectParams($params);
				$regEdit->setVar('//modules/news/per_page', (int) $params['config']['int:per_page']);
				$regEdit->setVar('//modules/news/rss_per_page', (int) $params['config']['int:rss_per_page']);
				$this->chooseRedirect();
			}

			$params['config']['int:per_page'] = (int) $regEdit->getVal('//modules/news/per_page');
			$params['config']['int:rss_per_page'] = (int) $regEdit->getVal('//modules/news/rss_per_page');
			$params['config']['int:rss_per_page'] = $params['config']['int:rss_per_page'] > 0 ? $params['config']['int:rss_per_page'] : 10;

			$this->setDataType('settings');
			$this->setActionType('modify');

			$data = $this->prepareData($params, 'settings');

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Устанавливает данные списка элементов модуля
		 * @return bool
		 * @throws coreException
		 * @throws selectorException
		 */
		public function lists() {
			$this->setDataType('list');
			$this->setActionType('view');

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$currentPage = getRequest('p');
			$offset = $currentPage * $limit;

			$sel = new selector('pages');
			$sel->types('object-type')->name('news', 'rubric');
			$sel->types('object-type')->name('news', 'item');

			if (is_array(getRequest('rel')) && regedit::getInstance()->getVal('//modules/comments')) {
				$sel->types('object-type')->name('comments', 'comment');
			}

			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$data = $this->prepareData($sel->result(), 'pages');
			$this->setData($data, $sel->length());
			$this->setDataRangeByPerPage($limit, $currentPage);
			$this->doData();
		}

		/**
		 * Устанавливает данные списка сюжетов публикации
		 * @throws coreException
		 */
		public function subjects() {
			$objectTypesCollection = umiObjectTypesCollection::getInstance();
			$objectsCollection = umiObjectsCollection::getInstance();
			$type_id = $objectTypesCollection->getTypeIdByHierarchyTypeName('news', 'subject');

			$mode = (string) getRequest('param0');

			if ($mode == 'do') {
				$params = [
					'type_id' => $type_id
				];

				$this->saveEditedList('objects', $params);
				$this->chooseRedirect();
			}

			$per_page = 25;
			$curr_page = getRequest('p');

			$subjects_guide = $objectsCollection->getGuidedItems($type_id);

			$subjects = array_keys($subjects_guide);
			$total = count($subjects);

			$this->setDataType('list');
			$this->setActionType('modify');
			$this->setDataRange($per_page, $curr_page * $per_page);
			$data = $this->prepareData($subjects, 'objects');
			$this->setData($data, $total);
			$this->doData();
		}

		/**
		 * Добавлят элемент модуля
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function add() {
			$parent = $this->expectElement('param0');
			$type = (string) getRequest('param1');
			$mode = (string) getRequest('param2');
			$this->setHeaderLabel('header-news-add-' . $type);

			$inputData = [
				'type' => $type,
				'parent' => $parent,
				'type-id' => getRequest('type-id'),
				'allowed-element-types' => [
					'rubric',
					'item'
				]
			];

			if ($mode == 'do') {
				$element_id = $this->saveAddedElementData($inputData);

				if ($type == 'item') {
					umiHierarchy::getInstance()->moveFirst($element_id, ($parent instanceof umiHierarchyElement) ? $parent->getId() : 0);
				}

				$this->chooseRedirect();
			}

			$this->setDataType('form');
			$this->setActionType('create');
			$data = $this->prepareData($inputData, 'page');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Редактирует элемент модуля
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function edit() {
			$element = $this->expectElement('param0', true);
			$mode = (string) getRequest('param1');
			$this->setHeaderLabel('header-news-edit-' . $this->getObjectTypeMethod($element->getObject()));

			$inputData = [
				'element' => $element,
				'allowed-element-types' => [
					'rubric',
					'item'
				]
			];

			if ($mode == 'do') {
				$this->saveEditedElementData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType('form');
			$this->setActionType('modify');
			$data = $this->prepareData($inputData, 'page');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Удаляет элемент модуля
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function del() {
			$elements = getRequest('element');
			$elements = (array) $elements;

			foreach ($elements as $elementId) {
				$element = $this->expectElement($elementId, false, true);

				$params = [
					'element' => $element,
					'allowed-element-types' => [
						'rubric',
						'item'
					]
				];

				$this->deleteElement($params);
			}

			$this->setDataType('list');
			$this->setActionType('view');
			$data = $this->prepareData($elements, 'pages');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Изменяет активность элементов модуля
		 * @throws coreException
		 * @throws expectElementException
		 * @throws requreMoreAdminPermissionsException
		 * @throws wrongElementTypeAdminException
		 */
		public function activity() {
			$elements = getRequest('element');
			$elements = (array) $elements;
			$isActive = getRequest('active');

			foreach ($elements as $elementId) {
				$element = $this->expectElement($elementId, false, true);

				$params = [
					'element' => $element,
					'allowed-element-types' => [
						'rubric',
						'item'
					],
					'activity' => $isActive
				];

				$this->switchActivity($params);
				$element->commit();
			}

			$this->setDataType('list');
			$this->setActionType('view');
			$data = $this->prepareData($elements, 'pages');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Устанавливает данные списка RSS-фидов для импорта
		 * @throws coreException
		 */
		public function rss_list() {
			$typesCollection = umiObjectTypesCollection::getInstance();
			$objectsCollection = umiObjectsCollection::getInstance();
			$typeId = $typesCollection->getTypeIdByGUID('12c6fc06c99a462375eeb3f43dfd832b08ca9e17');
			$result = $objectsCollection->getGuidedItems($typeId);

			$mode = (string) getRequest('param0');

			if ($mode == 'do') {

				$params = [
					'type_id' => $typeId
				];

				$this->saveEditedList('objects', $params);
				/**
				 * @var news|NewsFeeds $module
				 */
				$module = $this->module;
				$module->import_feeds();
				$this->chooseRedirect();
			}

			$result = array_keys($result);
			$total = count($result);

			$this->setDataType('list');
			$this->setActionType('modify');
			$this->setDataRange($total, 0);

			$data = $this->prepareData($result, 'objects');
			$this->setData($data, $total);
			$this->doData();
		}

		/**
		 * Возвращает информацию об объектах для неудаленных рубрик новостей
		 * @return array
		 */
		public function getObjectNamesForRubrics() {
			$rubrics = new selector('pages');
			$rubrics->types('hierarchy-type')->name('news', 'rubric');
			$rubrics->where('is_deleted')->equals(0);
			$items = [];

			/** @var umiHierarchyElement $page */
			foreach ($rubrics as $page) {
				$object = $page->getObject();

				$items[] = [
					'attribute:id' => $object->getId(),
					'node:name' => $object->getName()
				];
			}

			return [
				'items' => [
					'nodes:item' => array_unique($items, SORT_REGULAR)
				]
			];
		}

		/**
		 * Возвращает настройки табличного контрола
		 * @param string $param контрольный параметр (чаще всего - название текущей вкладки
		 * административной панели)
		 * @return array
		 */
		public function getDatasetConfiguration($param = '') {
			return [
				'methods' => [
					[
						'title' => getLabel('smc-load'),
						'forload' => true,
						'module' => 'news',
						'#__name' => 'lists'
					],
					[
						'title' => getLabel('smc-delete'),
						'module' => 'news',
						'#__name' => 'del',
						'aliases' => 'tree_delete_element,delete,del'
					],
					[
						'title' => getLabel('smc-activity'),
						'module' => 'news',
						'#__name' => 'activity',
						'aliases' => 'tree_set_activity,activity'
					],
					[
						'title' => getLabel('smc-copy'),
						'module' => 'content',
						'#__name' => 'tree_copy_element'
					],
					[
						'title' => getLabel('smc-move'),
						'module' => 'content',
						'#__name' => 'move'
					],
					[
						'title' => getLabel('smc-change-template'),
						'module' => 'content',
						'#__name' => 'change_template'
					],
					[
						'title' => getLabel('smc-change-lang'),
						'module' => 'content',
						'#__name' => 'move_to_lang'
					],
					[
						'title' => getLabel('smc-change-lang'),
						'module' => 'content',
						'#__name' => 'copy_to_lang_old'
					],
				],
				'types' => [
					[
						'common' => 'true',
						'id' => 'item'
					]
				],
				'stoplist' => [
					'title',
					'h1',
					'meta_keywords',
					'meta_descriptions',
					'menu_pic_ua',
					'menu_pic_a',
					'header_pic',
					'more_params',
					'robots_deny',
					'is_unindexed',
					'store_amounts',
					'locktime',
					'lockuser',
					'anons',
					'content',
					'rate_voters',
					'rate_sum',
					'begin_time',
					'end_time'
				],
				'default' => 'name[400px]|publish_time[250px]'
			];
		}
	}
