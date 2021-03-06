<?php

	/**
	 * Класс функционала административной панели
	 */
	class CatalogAdmin {

		use baseModuleAdmin;
		/**
		 * @var catalog $module
		 */
		public $module;
		/**
		 * @var int $indexLimit сколько товаро индексировать за одну итерацию
		 */
		protected $indexLimit;
		/**
		 * @var string $filterIndexGroup guid группы полей разделов каталога, содержащей служебные поля для индексации
		 */
		protected $filterIndexGroup;
		/**
		 * @var bool $isAdvancedModeEnabled разрешено ли указание уровня вложенности при индексации
		 */
		protected $isAdvancedModeEnabled;

		/**
		 * Конструктор
		 */
		public function __construct() {
			$config = mainConfiguration::getInstance();
			$limit = (int) ($config->get('modules', 'catalog.index.limit'));
			$advancedMode = (boolean) $config->get('modules', 'catalog.index.advanced-mode');

			$this->indexLimit = $limit > 0 ? $limit : 5;
			$this->filterIndexGroup = 'filter_index';
			$this->isAdvancedModeEnabled = $advancedMode;
		}

		/**
		 * Возвращает список разделов и объектов каталога.
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function tree() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$currentPage = getRequest('p');
			$offset = $currentPage * $limit;

			$sel = new selector('pages');
			$sel->types('object-type')->name('catalog', 'category');
			$sel->types('object-type')->name('catalog', 'object');

			if (is_array(getRequest('rel')) && regedit::getInstance()->getVal('//modules/comments')) {
				$sel->types('object-type')->name('comments', 'comment');
			}

			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$data = $this->prepareData($sel->result(), "pages");
			$this->setData($data, $sel->length());
			$this->setDataRangeByPerPage($limit, $currentPage);
			$this->doData();
		}

		/**
		 * Возвращает список разделов каталога с индексом
		 * @throws coreException
		 * @throws selectorException
		 */
		public function filters() {
			$this->setDataType("list");
			$this->setActionType("view");

			$requestedLimit = (int) getRequest('per_page_limit');
			$defaultLimit = 10;
			$limit = $requestedLimit > 0 ? $requestedLimit : $defaultLimit;
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$categories = new selector('pages');
			$categories->types('object-type')->name('catalog', 'category');
			$categories->where(catalog::FILTER_INDEX_INDEXATION_NEEDED)->equals(true);
			$categories->limit($offset, $limit);
			selectorHelper::detectFilters($categories);

			$data = $this->prepareData($categories->result(), "pages");
			$this->setData($data, $categories->length());
			$this->setDataRangeByPerPage($limit, $curr_page);
			$this->doData();
		}

		/**
		 * Индексирует раздел каталога
		 * @param bool|int $parentId ID индексируемого раздела
		 * @param bool|int $level уровень глубины индексации
		 * @throws publicAdminException
		 */
		public function indexPosition($parentId = false, $level = false) {
			$parentId = ($parentId === false) ? getRequest('param0') : $parentId;
			$level = ($level === false) ? getRequest('param1') : $level;

			if (is_null($parentId) || is_null($level)) {
				throw new publicAdminException(__METHOD__ . ': Parent ID and nesting level are required params');
			}

			$indexGenerator = new FilterIndexGenerator($this->module->getProductHierarchyTypeId(), 'pages');
			$indexGenerator->setHierarchyCondition($parentId, $level);
			$indexGenerator->setLimit($this->indexLimit);

			$counter = 0;
			$error = '';
			$originalError = '';
			try {
				$counter = $indexGenerator->run();
			} catch (Exception $e) {
				$error = getLabel('indexing-uncaught-error');

				if ($e instanceof maxKeysCountExceedingException) {
					$error = getLabel('indexing-impossible-to-create-error');
				}
				if ($e instanceof noObjectsFoundForIndexingException) {
					$error = getLabel('indexing-items-not-found-error');
				}

				$originalError = $e->getMessage();
			}

			$data = [
					'index' =>
							[
									'indexed' => $counter,
									'isDone' => $indexGenerator->isDone(),
									'error' => $error,
									'originalError' => $originalError
							]
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Перезаписывает значение в поле, которое принадлежит группе,
		 * содержащей поля, относящиеся к индексации self::getIndexGroup()
		 * @param bool|int $categoryId ID раздела
		 * @param bool|string $fieldName имя (идентификатор) поля
		 * @param mixed $value новое значение
		 * @throws coreException
		 * @throws publicAdminException
		 */
		public function setValueForIndexField($categoryId = false, $fieldName = false, $value = false) {
			$categoryId = ($categoryId === false) ? getRequest('param0') : $categoryId;
			$fieldName = ($fieldName === false) ? getRequest('param1') : $fieldName;
			$value = ($value === false) ? getRequest('param2') : $value;
			/* @var iUmiFieldsGroup $filterIndexGroup */
			$filterIndexGroup = $this->getIndexGroup();

			$hasField = false;
			/* @var iUmiField $field */
			foreach ($filterIndexGroup->getFields() as $field) {
				if ($field->getName() === $fieldName) {
					$hasField = true;
					break;
				}
			}

			if (!$hasField) {
				throw new publicAdminException(__METHOD__ . ': Group "' . $this->filterIndexGroup .
						'" has no field "' . $fieldName . '"');
			}

			$element = umiHierarchy::getInstance()->getElement($categoryId);

			if (!$this->module->isCatalogCategory($element)) {
				throw new coreException('Element is not catalog category');
			}

			/* @var iUmiHierarchyElement|umiEntinty $element */
			$element->setValue($fieldName, $value);
			$element->commit();

			$data = [
					'success' => true
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Удаляет индексы
		 * @param bool|array $elements массив с ID элементов
		 * @throws publicAdminException
		 */
		public function deleteIndex($elements = false) {
			if (!$elements) {
				$elements = getRequest('elements');
			}

			if (!is_array($elements)) {
				$elements = [$elements];
			}

			$productHierarchyTypeId = $this->module->getProductHierarchyTypeId();

			foreach ($elements as $pageId) {
				$this->cleanGroupAllFields($pageId);
				$indexGenerator = new FilterIndexGenerator($productHierarchyTypeId, 'pages');
				$indexGenerator->setHierarchyCondition($pageId);
				$indexGenerator->dropTable();
				$indexGenerator->dropTable(true);
				$indexGenerator->deleteStoredOffset();
				$indexGenerator->deleteSavedFilteredFields();
				$indexGenerator = null;
			}

			$data = [
					'success' => true
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Очищает все значения полей группы, поля которой
		 * относятся к индексации self::getIndexGroup()
		 * @param bool|int $categoryId ID раздела каталога
		 * @throws coreException
		 * @throws publicAdminException
		 */
		public function cleanGroupAllFields($categoryId = false) {
			$categoryId = ($categoryId === false) ? getRequest('param0') : $categoryId;
			/* @var iUmiFieldsGroup $filterIndexGroup */
			$filterIndexGroup = $this->getIndexGroup();
			/* @var iUmiHierarchyElement|umiEntinty $element */
			$element = umiHierarchy::getInstance()->getElement($categoryId);

			if (!$this->module->isCatalogCategory($element)) {
				throw new coreException('Element is not catalog category');
			}

			/* @var iUmiField $field */
			foreach ($filterIndexGroup->getFields() as $field) {
				$element->setValue($field->getName(), '');
			}
			$element->commit();

			$data = [
					'success' => true
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает группу (с именем self::$filterIndexGroup),
		 * поля которой относятся к индексации
		 * @throws coreException
		 * @throws publicAdminException
		 * @return bool|umiFieldsGroup
		 */
		public function getIndexGroup() {
			$objectTypes = umiObjectTypesCollection::getInstance();
			$categoryBaseTypeId = $objectTypes->getTypeIdByHierarchyTypeName('catalog', 'category');
			/* @var iUmiObjectType $categoryBaseType */
			$categoryBaseType = $objectTypes->getType($categoryBaseTypeId);
			$filterIndexGroup = $categoryBaseType->getFieldsGroupByName($this->filterIndexGroup);

			/* @var iUmiFieldsGroup $filterIndexGroup */
			if (!$filterIndexGroup instanceof iUmiFieldsGroup) {
				throw new publicAdminException(__METHOD__ . ': Fields group ' .
						$this->filterIndexGroup . ' with index data is not found');
			}

			return $filterIndexGroup;
		}

		/**
		 * Возвращает настройки индексации
		 */
		public function getSettings() {
			$data = [
					'settings' => [
							'limit' => $this->indexLimit,
							'group' => $this->filterIndexGroup,
							'advancedMode' => $this->isAdvancedModeEnabled
					]
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для построения формы создания сущности модуля.
		 * Если передан ключевой параметр $_REQUEST['param2'] = do, то создает сущность
		 * и перенаправляет на страницу, где ее можно отредактировать.
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function add() {
			$parent = $this->expectElement("param0");
			$type = (string) getRequest("param1");
			$mode = (string) getRequest("param2");
			$this->setHeaderLabel("header-catalog-add-" . $type);

			$inputData = [
					"type" => $type,
					"parent" => $parent,
					'type-id' => getRequest('type-id'),
					"allowed-element-types" => [
							'category',
							'object'
					]
			];

			if ($mode == "do") {
				$element_id = $this->saveAddedElementData($inputData);
				/**
				 * @var iUmiHierarchyElement|iUmiEntinty $element
				 */
				$element = umiHierarchy::getInstance()->getElement($element_id);

				if ($element instanceof iUmiHierarchyElement) {
					$element->setValue('date_create_object', time());
					$element->commit();
				}

				$this->chooseRedirect("{$this->module->pre_lang}/admin/catalog/edit/{$element_id}/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "page");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для построения формы редактирования сущности модуля.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do, то сохраняет изменения сущности
		 * и осуществляет перенаправление. Адрес перенаправления зависит от режиме кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function edit() {
			$element = $this->expectElement('param0', true);
			$mode = (string) getRequest('param1');
			$objectTypeMethod = $this->getObjectTypeMethod($element->getObject());
			$this->setHeaderLabel("header-catalog-edit-" . $objectTypeMethod);

			$inputData = [
					'element' => $element,
					'allowed-element-types' => [
							'category',
							'object'
					]
			];

			if ($mode == "do") {
				$this->saveEditedElementData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, 'page');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Удаляет сущности модуля.
		 * @throws coreException
		 * @throws expectElementException
		 * @throws wrongElementTypeAdminException
		 */
		public function del() {
			$elements = getRequest('element');

			if (!is_array($elements)) {
				$elements = [$elements];
			}

			foreach ($elements as $elementId) {
				$element = $this->expectElement($elementId, false, true);

				$params = [
						"element" => $element,
						"allowed-element-types" => [
								'category',
								'object'
						]
				];

				$this->deleteElement($params);
			}

			$this->setDataType("list");
			$this->setActionType("view");
			$data = $this->prepareData($elements, "pages");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Изменяет активность сущностей модуля
		 * @throws coreException
		 * @throws expectElementException
		 * @throws requreMoreAdminPermissionsException
		 * @throws wrongElementTypeAdminException
		 */
		public function activity() {
			$elements = getRequest('element');

			if (!is_array($elements)) {
				$elements = [$elements];
			}

			$is_active = getRequest('active');

			foreach ($elements as $elementId) {
				$element = $this->expectElement($elementId, false, true);

				$params = [
						"element" => $element,
						"allowed-element-types" => [
								'category',
								'object'
						],
						"activity" => $is_active
				];

				$this->switchActivity($params);
				$element->commit();
			}

			$this->setDataType("list");
			$this->setActionType("view");
			$data = $this->prepareData($elements, "pages");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки модуля.
		 * Если передан ключевой параметр $_REQUEST['param0'] = do,
		 * то сохраняет настройки.
		 * @throws coreException
		 */
		public function config() {
			$umiRegistry = regedit::getInstance();

			$params = [
					"config" => [
							"int:per_page" => null
					]
			];

			$mode = getRequest("param0");

			if ($mode == "do") {
				$params = $this->expectParams($params);
				$umiRegistry->setVar("//modules/catalog/per_page", $params['config']['int:per_page']);
				$this->chooseRedirect();
			}

			$params['config']['int:per_page'] = (int) $umiRegistry->getVal("//modules/catalog/per_page");

			$this->setDataType("settings");
			$this->setActionType("modify");
			$data = $this->prepareData($params, "settings");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки табличного контрола
		 * @param string $param контрольный параметр
		 * @return array
		 */
		public function getDatasetConfiguration($param = '') {
			$loadMethod = 'tree';
			$typeMethod = 'object';

			if ($param !== '' && !is_null($param)) {
				$loadMethod = $param;
			}

			$defaultFields = 'name[400px]|price[250px]|photo[250px]';
			$stopList = [
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
					'rate_voters',
					'rate_sum',
					'stores_state'
			];

			if ($param === 'filters') {
				$defaultFields = 'name[400px]|index_state[300px]|index_date[250px]|index_level[250px]';
				$typeMethod = 'category';
				$categoriesFields = $this->module->getAllCatalogCategoriesFieldsGUIDs();

				if (isset($categoriesFields[catalog::FILTER_INDEX_INDEXATION_DATE])) {
					unset($categoriesFields[catalog::FILTER_INDEX_INDEXATION_DATE]);
				}

				if (isset($categoriesFields[catalog::FILTER_INDEX_NESTING_DEEP_FIELD_NAME])) {
					unset($categoriesFields[catalog::FILTER_INDEX_NESTING_DEEP_FIELD_NAME]);
				}

				if (isset($categoriesFields[catalog::FILTER_INDEX_INDEXATION_STATE])) {
					unset($categoriesFields[catalog::FILTER_INDEX_INDEXATION_STATE]);
				}

				$stopList = array_flip($categoriesFields);
			}

			return [
					'methods' => [
							[
									'title' => getLabel('smc-load'),
									'forload' => true,
									'module' => 'catalog',
									'#__name' => $loadMethod
							],
							[
									'title' => getLabel('smc-delete'),
									'module' => 'catalog',
									'#__name' => 'del',
									'aliases' => 'tree_delete_element,delete,del'
							],
							[
									'title' => 'Export csv',
									'#__name' => 'tree',
									'aliases' => 'export'
							],
							[
									'title' => getLabel('smc-activity'),
									'module' => 'catalog',
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
							]
					],
					'types' => [
							[
									'common' => 'true',
									'id' => $typeMethod
							]
					],
					'stoplist' => $stopList,
					'default' => $defaultFields
			];
		}
	}

?>
