<?php
	/**
	 * Класс функционала административной панели
	 */
	class MenuAdmin {

		use baseModuleAdmin;
		/**
		 * @var menu $module
		 */
		public $module;

		/**
		 * Возращает список меню
		 * @return bool
		 * @throws coreException
		 * @throws selectorException
		 */
		public function lists() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = getRequest('p');
			$offset = $curr_page * $limit;

			$sel = new selector('objects');
			$sel->types('object-type')->name('menu', 'item_element');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->setDataRangeByPerPage($limit, $curr_page);
			$this->doData();
		}

		/**
		 * Возвращает данные для построения формы добавления меню.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то добавляет меню и перенаправляет на страницу со списком меню.
		 * @throws coreException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function add() {
			$type = (string) getRequest('param0');
			$mode = (string) getRequest('param1');
			$this->setHeaderLabel("header-menu-add-" . $type);

			$inputData = array(
				'type'					=> $type,
				'type-id' 				=> getRequest('type-id'),
				'allowed-element-types'	=> array(
					'menu',
					'item_element'
				)
			);

			if ($mode == "do") {
				$object = $this->saveAddedObjectData($inputData);
				$object->commit();
				$this->chooseRedirect($this->module->pre_lang . '/admin/menu/edit/' . $object->getId() . '/');
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для построения формы редактирования меню.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения меню и перенаправляет на страницу со списком меню.
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function edit() {
			$object = $this->expectObject("param0", true);
			$mode = (string) getRequest('param1');
			$this->setHeaderLabel("header-menu-edit-" . $this->getObjectTypeMethod($object));

			$inputData = Array(
				"object"	=> $object,
				'allowed-element-types'	=> array(
					'menu',
					'item_element'
				)
			);

			if ($mode == "do") {
				$object = $this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$oldJSON = $object->getValue('menuhierarchy');
			$values = json_decode($oldJSON);
			$values = $this->module->editLinkMenu($values);
			$newJSON = json_encode($values);

			if ($oldJSON != $newJSON) {
				$object->setValue('menuhierarchy', $newJSON);
				$object->commit();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Удаляет меню
		 * @throws coreException
		 * @throws expectObjectException
		 * @throws wrongElementTypeAdminException
		 */
		public function del() {
			$objects = getRequest('element');

			if (!is_array($objects)) {
				$objects = Array($objects);
			}

			foreach ($objects as $objectId) {
				$object = $this->expectObject($objectId, false, true);
				$params = Array(
					'object'		=> $object,
					'allowed-element-types' => Array(
						'menu',
						'item_element'
					)
				);
				$this->deleteObject($params);
			}

			$this->setDataType("list");
			$this->setActionType("view");
			$data = $this->prepareData($objects, "objects");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Изменяет активность меню
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function activity() {
			$objects = getRequest('object');

			if (!is_array($objects)) {
				$objects = Array($objects);
			}

			$is_active = (bool) getRequest('active');

			foreach ($objects as $objectId) {
				$object = $this->expectObject($objectId, false, true);
				$object->setValue("is_active", $is_active);
				$object->commit();
			}

			$this->setDataType("list");
			$this->setActionType("view");
			$data = $this->prepareData($objects, "objects");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки табличного контрола
		 * @param string $param контрольный параметр
		 * @return array
		 */
		public function getDatasetConfiguration($param = '') {
			return array(
				'methods' => array(
					array(
						'title' 	=> getLabel('smc-load'),
						'forload'	=> true,
						'module'	=> 'menu',
						'#__name'	=> 'lists'
					),
					array(
						'title'		=> getLabel('smc-delete'),
						'module'	=> 'menu',
						'#__name'	=> 'del',
						'aliases'	=> 'tree_delete_element,delete,del'
					),
					array(
						'title'		=> getLabel('smc-activity'),
						'module'	=> 'menu',
						'#__name'	=> 'activity',
						'aliases'	=> 'tree_set_activity,activity'
					)
				),
				'types' => array(
					array(
						'common'	=> 'true',
						'id'		=> 'item_element'
					)
				),
				'stoplist' => array(
					'menuhierarchy'
				),
				'default' => 'name[400px]'
			);
		}
	}
?>