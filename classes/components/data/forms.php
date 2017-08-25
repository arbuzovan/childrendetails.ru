<?php
	/**
	 * Класс для работы с формами.
	 * Умеет:
	 * 1) Возвращать данные для формирования формы редактирования объекта;
	 * 2) Возвращать данные для формирования формы добавления объекта;
	 * 3) Сохранять изменненный объект;
	 * 4) Валидировать значения полей объекта, которые требуется сохранить.
	 * Применяется как в шаблонах клиентов, так и в шаблоне административной панели.
	 */
	class DataForms {
		/**
		 * @var data $module
		 */
		public $module;

		/**
		 * Выводит данные для построения формы редактирования объекта с указанным id.
		 * @param int $objectId id объекта
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param string $groupNames идентификаторы групп полей, разделенные пробелом
		 * @param bool $all выводить все возможные группы полей
		 * @return string
		 */
		public function getEditForm($objectId, $template = "default", $groupNames = "", $all = false) {
			return $this->getEditFormWithIgnorePermissions($objectId, $template, $groupNames, $all, false);
		}

		/**
		 * Выводит данные для построения формы редактирования объекта с указанным id, с возможностью проигнорировать разрешения.
		 * @param int $object_id id объекта
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param string $groups_names идентификаторы групп полей, разделенные пробелом
		 * @param bool $all выводить все возможные группы полей
		 * @param bool $ignorePermissions игнорировать права на редактирование объекта
		 * @return string
		 */
		public function getEditFormWithIgnorePermissions($object_id, $template = "default", $groups_names = "", $all = false, $ignorePermissions = false) {
			if (!$template) {
				$template = "default";
			}

			$b_allow = false;
			$permissions = permissionsCollection::getInstance();

			if ($permissions->isSv()) {
				$ignorePermissions = true;
			}

			if (!$ignorePermissions) {
				$b_allow = $permissions->isOwnerOfObject($object_id);
				$arr_helements = umiHierarchy::getInstance()->getObjectInstances($object_id);
				$auth = UmiCms\Service::Auth();

				foreach ($arr_helements as $i_element_id) {
					$arr_allow = $permissions->isAllowedObject($auth->getUserId(), $i_element_id);
					if (is_array($arr_allow) && count($arr_allow) > 1) {
						$b_allow = intval($arr_allow[1]);
						if ($b_allow) {
							break;
						}
					}
				}

				if (!$b_allow) {
					return data::parseTPLMacroses("%data_edit_foregin_object%");
				}
			}

			$groups_names = trim($groups_names);
			$groups_names = strlen($groups_names) ? explode(" ", $groups_names) : array();

			list(
				$template_block, $template_block_empty, $template_line
				) = data::loadTemplates("data/reflection/{$template}",
				"reflection_block", "reflection_block_empty", "reflection_group"
			);

			if (!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
				return $template_block_empty;
			}


			$object_type_id = $object->getTypeId();
			$groups_arr = $this->module->getTypeFieldGroups($object_type_id);

			$groups = Array();
			/**
			 * @var umiFieldsGroup $group
			 */
			foreach ($groups_arr as $group) {
				if (!$group->getIsActive()) {
					continue;
				}

				if (sizeof($groups_names)) {
					if (!in_array($group->getName(), $groups_names)) {
						continue;
					}
				} else {
					if (!$group->getIsActive() || (!$group->getIsVisible() && !$all)) {
						continue;
					}
				}

				$line_arr = Array();

				$fields_arr = $group->getFields();
				$fields = Array();
				foreach($fields_arr as $field) {
					if (!$field->getIsVisible() && !$all) {
						continue;
					}
					if ($field->getIsSystem()) {
						continue;
					}

					$fields[] = $this->renderEditField($template, $field, $object);
				}

				if (empty($fields)) {
					continue;
				}

				$line_arr['attribute:name'] = $group->getName();
				$line_arr['tip'] = $group->getTip();
				$line_arr['attribute:title'] = $group->getTitle();
				$line_arr['nodes:field'] = $line_arr['void:fields'] = $fields;

				$groups[] = data::parseTemplate($template_line, $line_arr);
			}

			$block_arr['nodes:group'] = $block_arr['void:groups'] = $groups;

			return data::parseTemplate($template_block, $block_arr, false, $object_id);
		}

		/**
		 * Выводит данные для построения формы добавления объекта с указанным идентификатором объектного типа данных.
		 * @param int $object_type_id идентификатор объектного типа данных
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param string $groups_names идентификаторы групп полей, разделенные пробелом
		 * @param bool $all выводить все возможные группы полей
		 * @return mixed|string
		 */
		public function getCreateForm($object_type_id, $template = "default", $groups_names = "", $all = false) {
			if (!$template) {
				$template = "default";
			}

			list(
				$template_block, $template_block_empty, $template_line
				) = data::loadTemplates("data/reflection/{$template}",
				"reflection_block", "reflection_block_empty", "reflection_group"
			);

			if (!umiObjectTypesCollection::getInstance()->getType($object_type_id) instanceof iUmiObjectType) {
				return $template_block_empty;
			}

			$groups_names = trim($groups_names);
			$groups_names = strlen($groups_names) ? explode(" ", $groups_names) : array();
			$groups_arr = $this->module->getTypeFieldGroups($object_type_id);

			if (!is_array($groups_arr)) {
				return "";
			}

			$groups = Array();
			/**
			 * @var umiFieldsGroup $group
			 */
			foreach($groups_arr as $group) {
				if (!$group->getIsActive()) {
					continue;
				}
				if ($group->getName() == "locks") {
					continue;
				}
				if (sizeof($groups_names)) {
					if (!in_array($group->getName(), $groups_names)) {
						continue;
					}

				} else {
					if (!$group->getIsActive() || (!$group->getIsVisible() && !$all)) {
						continue;
					}
				}

				$line_arr = Array();
				$fields_arr = $group->getFields();
				$fields = Array();

				foreach ($fields_arr as $field) {
					if (!$field->getIsVisible() && !$all) {
						continue;
					}
					if ($field->getIsSystem()) {
						continue;
					}

					$fields[] = $this->renderEditField($template, $field);
				}

				if (empty($fields)) {
					continue;
				}

				$line_arr['attribute:name'] = $group->getName();
				$line_arr['tip'] = $group->getTip();
				$line_arr['attribute:title'] = $group->getTitle();

				$line_arr['nodes:field'] = $line_arr['void:fields'] = $fields;

				$groups[] = data::parseTemplate($template_line, $line_arr);
			}

			$block_arr['nodes:group'] = $block_arr['void:groups'] = $groups;
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Сохраняет изменения объекта
		 * @param int $object_id идентификатор объекта
		 * @param bool $is_new является ли объект новым
		 * @param bool $b_force_owner игнорировать проверку прав объекта
		 * @param bool $all изменять все возможные группы полей
		 * @return mixed
		 */
		public function saveEditedObject($object_id, $is_new = false, $b_force_owner = false, $all = false) {
			return $this->saveEditedObjectWithIgnorePermissions($object_id, $is_new, false, $all);
		}

		/**
		 * Сохраняет изменения объекта с возможностью проигнорировать проверку прав
		 * @param int $object_id идентификатор объекта
		 * @param bool $is_new является ли объект новым
		 * @param bool $b_force_owner игнорировать проверку прав объекта
		 * @param bool $all изменять все возможные группы полей
		 * @return array|bool
		 * @throws coreException
		 */
		public function saveEditedObjectWithIgnorePermissions($object_id, $is_new = false, $b_force_owner = false, $all = false) {
			global $_FILES;
			$permissions = permissionsCollection::getInstance();

			if (!($object = umiObjectsCollection::getInstance()->getObject($object_id))) {
				return false;
			}

			if (!$b_force_owner && !$permissions->isOwnerOfObject($object_id)) {
				return false;
			}

			$object_type_id = $object->getTypeId();
			$object_type = umiObjectTypesCollection::getInstance()->getType($object_type_id);

			$key = ($is_new) ? "new" : $object_id;

			if (is_null(getRequest('data'))) {
				if (is_null($_FILES)) {
					return true;
				} else {
					$_REQUEST['data'][$key] = array();
				}
			}

			$data = isset($_REQUEST['data'][$key]) ? $_REQUEST['data'][$key] : Array();

			foreach ($_REQUEST as $skey => $value) {
				$real_key = substr($skey, 7);
				if (substr($skey, 0, 7)=='select_' && !isset($data[$real_key])) {
					$data[$real_key] = $value;
				}
			}

			if (isset($_FILES['data']['tmp_name'][$key])) {
				foreach ($_FILES['data']['tmp_name'][$key] as $filesKey => $filePath) {
					if ($filePath) {
						$data[$filesKey] = $filePath;
					}
				}
			}

			$data = $this->checkRequiredData($object_type, $data, $object_id, $is_new);
			$data = $this->checkAllowedData($object_type, $data, $object_id);

			foreach($data as $field_name => $field_value) {
				if (!($field_id = $object_type->getFieldId($field_name))) {
					continue;
				}

				$field = umiFieldsCollection::getInstance()->getField($field_id);
				$field_type = $field->getFieldType();
				$data_type  = $field_type->getDataType();
				$fldr_name  = $field_name . '/';

				switch ($data_type) {
					case "password": {
						if (isset($field_value[1])) {
							$field_value = ($field_value[0] == $field_value[1]) ? md5($field_value[0]) : NULL;
						} else {
							if(is_array($field_value)) {
								$field_value = ($field_value[0]) ? md5($field_value[0]) : NULL;
							} else {
								$field_value = ($field_value) ? md5($field_value) : NULL;
							}
						}
						break;
					}
					case "date" : {
						$oDate = new umiDate();
						$oDate->setDateByString($field_value);
						$field_value = $oDate;
						break;
					}
					case "img_file": {
						switch ($field_name) {
							case "menu_pic_ua" : {
								$destination_folder = USER_IMAGES_PATH . "/cms/menu/";
								break;
							}
							case "header_pic" : {
								$destination_folder = USER_IMAGES_PATH . "/cms/headers/";
								break;
							}
							case "menu_pic_a" : {
								$destination_folder = USER_IMAGES_PATH . "/cms/menu/";
								break;
							}
							default : {
								$destination_folder = USER_IMAGES_PATH . "/cms/data/";
								break;
							}
						}
						if ($value = umiImageFile::upload("data", $field_name, $destination_folder, $key)) {
							$field_value = $value;
						} else {
							$file_name = (substr($field_value, 0, 2) == "./") ? $field_value : ($destination_folder . $field_value);
							$field_value = new umiImageFile($file_name);
						}
						break;
					}
					case "video_file" :
					case "swf_file": {
						$destination_folder = USER_FILES_PATH . ((is_dir(USER_FILES_PATH . '/' . $fldr_name)) ? $fldr_name : '');
						if ($value = umiFile::upload("data", $field_name, $destination_folder, $key)) {
							$field_value = $value;
						} else {
							$oldvalue = $object->getValue($field_name);

							if ($oldvalue) {
								$destination_folder = $oldvalue->getDirName() . "/";
							}

							$file_name = (substr($field_value, 0, 2) == "./") ? $field_value : ($destination_folder . $field_value);
							$field_value = new umiFile($file_name);
						}
						break;
					}
					case "file": {
						$destination_folder = USER_FILES_PATH . ((is_dir(USER_FILES_PATH . "/" . $fldr_name)) ? $fldr_name : '');

						if ($value = umiFile::upload("data", $field_name, $destination_folder, $key)) {
							$field_value = $value;
						} else {
							$oldvalue = $object->getValue($field_name);

							if ($oldvalue) {
								$destination_folder = $oldvalue->getDirName() . "/";
							}

							$file_name = (substr($field_value, 0, 2) == "./") ? $field_value : ($destination_folder . $field_value);
							$field_value = new umiFile($file_name);
						}

						break;
					}
					case "multiple_image": {
						if (!is_array($field_value)) {
							$field_value = array();
							break;
						}
						foreach ($field_value as $key => $value) {
							if (!isset($value['src'])) {
								continue;
							}

							$image = new umiImageFile($value['src']);

							if (isset($value['alt'])) {
								$image->setAlt($value['alt']);
							}

							if (isset($value['ord'])) {
								$image->setOrder($value['ord']);
							}

							$field_value[$key] = $image;
						}
						break;
					}
				}
				$object->setValue($field_name, $field_value);
			}
			$object->commit();
			return $data;
		}

		/**
		 * Проверяет, допустимы ли данные для сохранения
		 * @param iUmiObjectType $objectType тип редактируемого объекта
		 * @param mixed $data входные данные
		 * @param mixed $objectId ID объекта, данные которого проверяются
		 * @return array
		 */
		public function checkAllowedData(iUmiObjectType $objectType, array $data, $objectId = false) {
			$userTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName('users', 'user')->getId();
			$controller = cmsController::getInstance();
			$isAdminMode = $controller->getCurrentMode() == 'admin';
			$isSv = permissionsCollection::getInstance()->isSv();
			/**
			 * @var umiObjectType $objectType
			 */
			$isObjectCustomer = $objectType->getGUID() == 'emarket-customer';
			$isObjectUser = $objectType->getHierarchyTypeId() == $userTypeId;
			/**
			 * @var users $usersModule
			 */
			$usersModule = $controller->getModule('users');

			if ($isObjectUser && $usersModule instanceof def_module) {
				if (isset($data['e-mail']) && !$usersModule->checkIsUniqueEmail($data['e-mail'], $objectId)) {
					$this->module->errorNewMessage("%error_users_non_unique_email%");
					$this->module->errorPanic();
				}

				if (isset($data['login']) && !$usersModule->checkIsUniqueLogin($data['login'], $objectId)) {
					$this->module->errorNewMessage("%err_users_user_exists%");
					$this->module->errorPanic();
				}
			}

			if (!$isSv && !$isAdminMode && ($isObjectCustomer || $isObjectUser)) {
				unset($data['bonus']);
				unset($data['spent_bonus']);
				unset($data['filemanager_directory']);
				unset($data['groups']);
			}

			return $data;
		}

		/**
		 * Проверяет все ли обязательные поля заполнены и
		 * корректно ли заполнены поля с правилами валидации
		 * @param iUmiObjectType $objectType тип данных, поля которого нужно проверить
		 * @param array $data массив значения полей [имя поля => значение поля]
		 * @param int $objectId идентификатор объекта, которому принадлежат поля
		 * @param bool $isNew является ли объект новым
		 * @return mixed
		 * @throws coreException
		 */
		public function checkRequiredData(iUmiObjectType $objectType, $data, $objectId, $isNew) {
			if (!is_array($data)) {
				return $data;
			}

			$cmsController = cmsController::getInstance();
			$admin = ($cmsController->getCurrentMode() == "admin");
			$wrongFieldsCount = 0;
			$fields = umiFieldsCollection::getInstance();

			foreach ($data as $fieldName => &$value) {
				$fieldId = $objectType->getFieldId($fieldName);
				$field = $fields->getField($fieldId);
				if ($field instanceof umiField == false) {
					continue;
				}
				if ($field->getIsRequired()) {
					if (is_null($value) || $value === false || $value === "") {
						$fieldTitle = $field->getTitle();
						$errstr = ($admin) ? "%errors_missed_field_value%" : getLabel('error-missed-field-value');
						$this->module->errorNewMessage($errstr . " \"{$fieldTitle}\"", false, 100, "input-missed-field");
						++$wrongFieldsCount;
					}
				}
				if ($restrictionId = $field->getRestrictionId()) {
					$restriction = baseRestriction::get($restrictionId);
					if ($restriction instanceof baseRestriction) {
						if ($restriction instanceof iNormalizeInRestriction) {
							$value = $restriction->normalizeIn($value);
						}
						if ($restriction->validate($value) == false) {
							$fieldTitle = $field->getTitle();
							$errstr = ($admin) ? "%errors_wrong_field_value%" : getLabel('error-wrong-field-value');
							$errstr .=  " \"{$fieldTitle}\" - " . $restriction->getErrorMessage();
							$this->module->errorNewMessage($errstr, false, 101, "input-wrong-field");
							++$wrongFieldsCount;
						}
					}
				}
			}
			if ($wrongFieldsCount > 0) {
				if ($isNew && $objectId) {
					$hierarchy = umiHierarchy::getInstance();
					$elementIds = $hierarchy->getObjectInstances($objectId);
					if (sizeof($elementIds)) {
						foreach ($elementIds as $elementId) {
							$hierarchy->delElement($elementId);
							$hierarchy->removeDeletedElement($elementId);
						}
					}
					umiObjectsCollection::getInstance()->delObject($objectId);
				}
				$this->module->errorPanic();
			}

			return $data;
		}

		/**
		 * Выводит данные поля для построения формы редактирования
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param umiField $field объект поля
		 * @param bool|iUmiObject $object объект, которому принадлежит поле
		 * @return mixed
		 */
		private function renderEditField($template, umiField $field, $object = false) {
			$field_type_id = $field->getFieldTypeId();
			$field_type = umiFieldTypesCollection::getInstance()->getFieldType($field_type_id);
			$is_multiple = $field_type->getIsMultiple();
			$data_type = $field_type->getDataType();

			switch($data_type) {
				case "counter":
				case "int": {
					$res = $this->renderEditFieldInt($field, $is_multiple, $object, $template);
					$data_type = "int";
					break;
				}
				case "link_to_object_type": {
					$res = $this->renderEditFieldInt($field, $is_multiple, $object, $template);
					break;
				}
				case "price": {
					$res = $this->renderEditFieldPrice($field, $is_multiple, $object, $template);
					break;
				}
				case "float": {
					$res = $this->renderEditFieldInt($field, $is_multiple, $object, $template);
					break;
				}
				case "color":
				case "string": {
					$res = $this->renderEditFieldString($field, $is_multiple, $object, $template);
					break;
				}
				case "date": {
					$res = $this->renderEditFieldDate($field, $is_multiple, $object, $template);
					break;
				}
				case "password": {
					$res = $this->renderEditFieldPassword($field, $is_multiple, $object, $template);
					break;
				}
				case "relation": {
					$res = $this->renderEditFieldRelation($field, $is_multiple, $object, $template);
					break;
				}
				case "symlink": {
					$res = $this->renderEditFieldSymlink($field, $is_multiple, $object, $template);
					break;
				}
				case "img_file": {
					$res = $this->renderEditFieldImageFile($field, $is_multiple, $object, $template);
					break;
				}
				case "video_file" :
				case "swf_file":
				case "file": {
					$res = $this->renderEditFieldFile($field, $is_multiple, $object, $template);
					break;
				}
				case "text": {
					$res = $this->renderEditFieldText($field, $is_multiple, $object, $template);
					break;
				}
				case "wysiwyg": {
					$res = $this->renderEditFieldWYSIWYG($field, $is_multiple, $object, $template);
					break;
				}
				case "boolean": {
					$res = $this->renderEditFieldBoolean($field, $is_multiple, $object, $template);
					break;
				}
				case "tags": {
					$res = $this->renderEditFieldTags($field, $is_multiple, $object, $template);
					break;
				}
				case "optioned": {
					$res = $this->renderEditFieldOptioned($field, $is_multiple, $object, $template);
					break;
				}
				case "multiple_image": {
					$res = $this->renderEditFieldMultipleImage($field, $object, $template);
					break;
				}
				default: {
					$res = "";
				}
			}

			if ($res === false) {
				return null;
			}

			if (!data::isXSLTResultMode()) {
				$required = $field->getIsRequired();
				$res = data::parseTemplate($res, array(
					'required' => ($required ? 'required' : ''),
					'required_asteriks' => ($required ? '*' : '')
				));
			} else {
				$res['attribute:type'] = $data_type;
				$res['attribute:id'] = $field->getId();

				if ($field->getIsRequired()) {
					$res['attribute:required'] = 'required';
				}
				if ($tip = $field->getTip()) {
					$res['attribute:tip'] = $tip;
				}
			}

			return $res;
		}

		/**
		 * Выводит данные поля типов "строка" и "цвет" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldString(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_string");
			$block_arr = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['node:value'] = ($object) ? $object->getValue($field->getName()) : "";

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}


			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "дата" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldDate(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block_string, $template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_string", "reflection_field_date");

			if (!$template_block) {
				$template_block = $template_block_string;
			}

			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();

			$block_arr['node:value'] = "";
			$block_arr['attribute:timestamp'] = 0;

			if($object) {
				$oDate = $object->getValue($field->getName());

				if ($oDate instanceof umiDate) {
					$block_arr['attribute:timestamp'] = $oDate->getDateTimeStamp();
					$block_arr['node:value'] = $oDate->getFormattedDate();
					$block_arr['attribute:formatted-date'] = $oDate->getFormattedDate("d.m.Y H:i");
				}

				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "простой текст" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldText(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_text");
			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['node:value'] = ($object) ? $object->getValue($field->getName()) : "";

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "HTML-текст" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldWYSIWYG(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_wysiwyg");
			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['node:value'] = ($object) ? $object->getValue($field->getName()) : "";

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типов "Число", "Счетчик", "Число с точкой" и "Ссылка на объектный тип" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldInt(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_int");
			$block_arr = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['node:value'] = ($object) ? $object->getValue($field->getName()) : "";

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "Цена" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $isMultiply может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldPrice(iUmiField $field, $isMultiply, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($templateBlock) = data::loadTemplates("data/reflection/{$template}", "reflection_field_int");
			$data = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$fieldName = $field->getName();
			$data['attribute:name'] = $fieldName;
			$data['attribute:title'] = $field->getTitle();
			$data['attribute:tip'] = $field->getTip();
			$data['attribute:field_id'] = $field->getId();
			$data['attribute:is_important'] = $field->isImportant();

			/**
			 * @var emarket $eMarket
			 */
			$eMarket = cmsController::getInstance()->getModule('emarket');

			if ($eMarket instanceof def_module) {
				$currency = $eMarket->getDefaultCurrency();
				/**
				 * @var iUmiObject|iUmiEntinty $currency
				 */
				if ($currency instanceof iUmiObject) {
					$data['attribute:currency_id'] = $currency->getId();
					$data['attribute:currency_code'] = $currency->getValue('codename');
					$data['attribute:currency_prefix'] = $currency->getValue('prefix');
					$data['attribute:currency_suffix'] = $currency->getValue('suffix');
				}
			}
			$data['node:value'] = '';

			if ($object instanceof iUmiObject) {
				$data['node:value'] = $object->getValue($field->getName());
				$data['void:object_id'] = $object->getId();
			}

			$data['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$fieldName}]" : "data[new][{$fieldName}]";
			return data::parseTemplate($templateBlock, $data);
		}

		/**
		 * Выводит данные поля типа "Кнопка-флажок" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldBoolean(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_boolean");
			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['attribute:checked'] = "";
			$block_arr['node:value'] = 0;

			if ($object) {
				$block_arr['node:value'] = (int) $object->getValue($field->getName());
				$block_arr['attribute:checked'] = (bool) $object->getValue($field->getName())? "checked" : "";
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "Пароль" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldPassword(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_password");
			$block_arr = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$block_arr['node:value'] = "";

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}][]" : "data[new][{$field_name}][]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типов "Выпадающие список" и "Выпадающие список со множественным выбором"
		 * для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldRelation(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			$controller = cmsController::getInstance();
			$objects = umiObjectsCollection::getInstance();
			$guide_items = array();

			$fieldName = $field->getName();
			if ($guide_id = $field->getGuideId()) {
				switch (true) {
					case ($controller->getCurrentMode() == "admin" && $object instanceof iUmiObject): {
						if ($object->getTypeGUID() == 'users-user') {
							$guide_items = $objects->getGuidedItems($guide_id);
							break;
						}
						$val = $object->getValue($fieldName);
						if (!$val) {
							break;
						}
						if (!is_array($val)) {
							$val = Array($val);
						}
						foreach ($val as $item_id) {
							$item = $objects->getObject($item_id);
							if (!$item instanceof iUmiObject) {
								continue;
							}
							$guide_items[$item_id] = $item->getName();
						}
						break;
					}
					default: {
						$guide_items = $objects->getGuidedItems($guide_id);
					}
				}
			}

			list(
				$template_block, $template_block_line, $template_block_line_a, $template_mul_block, $template_mul_block_line, $template_mul_block_line_a
				) = data::loadTemplates("data/reflection/{$template}",
				"reflection_field_relation", "reflection_field_relation_option", "reflection_field_relation_option_a", "reflection_field_multiple_relation",
				"reflection_field_multiple_relation_option", "reflection_field_multiple_relation_option_a"
			);

			$block_arr = array();
			$value = $object ? $object->getValue($fieldName) : array();

			if ($fieldName == 'publish_status' && $controller->getCurrentMode() != "admin") {
				return "";
			}
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$block_arr['attribute:name'] = $fieldName;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();

			if ($is_multiple) {
				$block_arr['attribute:multiple'] = "multiple";
			}

			if ($guide_id) {
				$block_arr['attribute:type-id'] = $guide_id;
				$guide = umiObjectTypesCollection::getInstance()->getType($guide_id);

				if ($guide instanceof umiObjectType) {
					if ($guide->getIsPublic()) {
						$block_arr['attribute:public-guide'] = true;
					}
				}
			}

			$isTpl = !$template_block_line && !data::isXSLTResultMode();
			$options = ($isTpl) ? '' : [];

			foreach ($guide_items as $item_id => $item_name) {
				$item_object = $objects->getObject($item_id);

				if (!is_object($item_object)) {
					continue;
				}

				if ($is_multiple) {
					$selected = (in_array($item_id, $value)) ? " selected" : "";
				} else {
					$selected = ($item_id == $value) ? " selected" : "";
				}

				if ($item_object->getValue("is_hidden") && !$selected) {
					continue;
				}

				if ($isTpl) {
					$options .= "<option value=\"{$item_id}\"{$selected}>{$item_name}</option>\n";
				} else {
					$line_arr = Array();
					$line_arr['attribute:id'] = $item_id;
					$line_arr['xlink:href'] = "uobject://" . $item_id;
					$line_arr['attribute:guid'] = $item_object->getGUID();
					$line_arr['node:name'] = $item_name;

					if ($selected) {
						$line_arr['attribute:selected'] = "selected";
						$line = $is_multiple ? $template_mul_block_line_a : $template_block_line_a;
					} else {
						$line = $is_multiple ? $template_mul_block_line : $template_block_line;
					}

					$options[] = data::parseTemplate($line, $line_arr, false, $item_id);
				}
			}

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['subnodes:values'] = $block_arr['void:options'] = $options;
			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$fieldName}]" . (($is_multiple) ? "[]" : "") : "data[new][{$fieldName}]" . (($is_multiple) ? "[]" : "");
			return data::parseTemplate((($is_multiple) ? $template_mul_block : $template_block), $block_arr);
		}

		/**
		 * Выводит данные поля типа "Ссылка не дерево" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldSymlink(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates(
				"data/reflection/{$template}",
				"reflection_field_relation",
				"reflection_field_relation_option",
				"reflection_field_relation_option_a",
				"reflection_field_multiple_relation",
				"reflection_field_multiple_relation_option",
				"reflection_field_multiple_relation_option_a"
			);
			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();

			$options = ($object) ? $object->getValue($field->getName()) : array();

			$block_arr['subnodes:values'] = $block_arr['void:options'] = $options;
			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}][]" : "data[new][{$field_name}][]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "Теги" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldTags(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_tags");
			$block_arr = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			$value = ($object) ? $object->getValue($field->getName()) : "";

			if (is_array($value)) {
				$value = implode(", ", $value);
			}

			$block_arr['node:value'] = $value;

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$block_arr['attribute:input_name'] = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типа "Составное" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldOptioned(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			$block_arr = array();
			$objects = umiObjectsCollection::getInstance();
			$hierarchy = umiHierarchy::getInstance();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();

			if ($guideId = $field->getGuideId()) {
				$block_arr['attribute:guide-id'] = $guideId;
			}

			$inputName = ($object) ? "data[" . $object->getId() . "][{$field_name}]" : "data[new][{$field_name}]";
			$values = ($object) ? $object->getValue($field->getName()) : Array();

			$values_arr = Array();
			foreach ($values as $value) {
				$value_arr = Array();
				foreach ($value as $type => $subValue) {
					switch($type) {
						case "tree": {
							$element = $hierarchy->getElement($subValue);
							if ($element instanceof umiHierarchyElement) {
								$value_arr['page'] = $element;
							}
							break;
						}

						case "rel": {
							$object = $objects->getObject($subValue);
							if ($object instanceof umiObject) {
								$value_arr['object'] = $object;
							}
							break;
						}

						default: {
							$value_arr['attribute:' . $type] = $subValue;
							break;
						}
					}
				}

				$values_arr[] = $value_arr;
			}

			$block_arr['values']['nodes:value'] = $values_arr;
			$block_arr['attribute:input_name'] = $inputName;
			return $block_arr;
		}

		/**
		 * Выводит данные поля типа "Набор изображений" для построения формы редактирования
		 * @param iUmiField $field
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldMultipleImage(iUmiField $field, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($fieldBlock, $valueBlock) = data::loadTemplates(
				'data/reflection/' . $template,
				'reflection_field_multiple_image_field',
				'reflection_field_multiple_image_value'
			);

			$fieldNode = array();
			/* @var iUmiField|umiEntinty $field*/
			$fieldName = $field->getName();
			$fieldNode['attribute:name'] = $fieldName;
			$fieldNode['attribute:title'] = $field->getTitle();
			$fieldNode['attribute:tip'] = $field->getTip();
			$fieldNode['attribute:maxsize'] = $this->module->getAllowedMaxFileSize("img");
			$fieldNode['attribute:field_id'] = $field->getId();
			$fieldNode['attribute:is_important'] = $field->isImportant();

			$inputName =  'data[new][' . $fieldName . ']';
			$values = array();

			if ($object instanceof umiEntinty && is_callable(array($object, 'getValue'))) {
				$inputName = 'data[' . $object->getId() . '][' . $fieldName . ']';
				$values = $object->getValue($fieldName);
			}

			$valuesNode = array();
			/* @var umiImageFile $value */
			foreach($values as $key => $value) {
				if (!$value instanceof umiImageFile || $value->getIsBroken()) {
					continue;
				}

				$valueNode = array();
				$valueNode['attribute:id'] = (int) $key;
				$valueNode['attribute:alt'] = $value->getAlt();
				$valueNode['attribute:order'] = $value->getOrder();
				$valueNode['attribute:relative-path'] = $value->getFilePath(true);
				$destinationFolder = USER_IMAGES_PATH . "/data/";

				$info = getPathInfo($value->getFilePath(true));
				$info['dirname'] = '.' . $info['dirname'];

				$relativePath = substr($info['dirname'], strlen($destinationFolder)) . '/' . $info['basename'];

				if (substr($relativePath, 0, 1) == '/') {
					$relativePath = substr($relativePath, 1);
				}

				$valueNode['node:value'] = $relativePath;
				$valueNode['attribute:destination-folder'] = $info['dirname'];
				$valuesNode[] = data::parseTemplate($valueBlock, $valueNode);
			}

			$fieldNode['values']['nodes:value'] = $valuesNode;
			$fieldNode['attribute:input_name'] = $inputName;
			return data::parseTemplate($fieldBlock, $fieldNode);
		}

		/**
		 * Выводит данные поля типа "Изображение" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldImageFile(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_img_file");

			$block_arr = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:maxsize'] = $this->module->getAllowedMaxFileSize("img");
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();

			$value = ($object) ? $object->getValue($field->getName()) : "";

			if ($value instanceof umiFile) {
				$block_arr['attribute:relative-path'] = $value->getFilePath(true);

				switch ($field_name) {
					case "menu_pic_ua" : {
						$destination_folder = USER_IMAGES_PATH . "/cms/menu/";
						break;
					}
					case "header_pic" : {
						$destination_folder = USER_IMAGES_PATH . "/cms/headers/";
						break;
					}
					case "menu_pic_a" : {
						$destination_folder = USER_IMAGES_PATH . "/cms/menu/";
						break;
					}
					default : {
						$destination_folder = USER_IMAGES_PATH . "/cms/data/";
						break;
					}
				}

				$info = getPathInfo ($value->getFilePath(true));
				$info['dirname'] = '.' . $info['dirname'];

				$relative_path = substr($info['dirname'], strlen ($destination_folder)) . "/" . $info['basename'];

				if (substr($relative_path, 0, 1) == "/") {
					$relative_path = substr($relative_path,1);
				}

				$block_arr['node:value'] = $relative_path;
				$destination_folder = $info['dirname'];
			} else {
				$block_arr['node:value'] = "";
				$folder_name = $field_name . '/';
				$general_name = USER_IMAGES_PATH . "/cms/";
				$destination_folder = $general_name . ((is_dir($general_name . $folder_name)) ? $folder_name : '');

				switch ($field_name) {
					case "menu_pic_ua" :
						$pFolder = "menu";
						break;

					case "header_pic" :
						$pFolder = "headers";
						break;

					case "menu_pic_a" :
						$pFolder = "menu";
						break;
					default :
						$pFolder = "data";
						break;
				}

				$destination_folder = $destination_folder . $pFolder;
			}

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$objectId = ($object) ?  $object->getId() : 'new';
			$block_arr['attribute:input_name'] = "data[" . $objectId . "][{$field_name}]";

			if (strpos($destination_folder, CURRENT_WORKING_DIR) === 0) {
				$destination_folder = str_replace(CURRENT_WORKING_DIR, '.', $destination_folder);
			}

			$block_arr['attribute:destination-folder'] = $destination_folder;

			return data::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит данные поля типов "Файл", "Видео" и "Flash" для построения формы редактирования
		 * @param iUmiField $field поле
		 * @param bool $is_multiple может ли поле хранит набор значений
		 * @param iUmiObject|bool $object объект, которому принадлежит поле
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		private function renderEditFieldFile(iUmiField $field, $is_multiple, $object, $template) {
			/**
			 * @var iUmiObject|iUmiEntinty|bool $object
			 */
			list($template_block) = data::loadTemplates("data/reflection/{$template}", "reflection_field_file");

			$regexp = "|^".CURRENT_WORKING_DIR."|";

			$block_arr = Array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			$field_name = $field->getName();
			$block_arr['attribute:name'] = $field_name;
			$block_arr['attribute:title'] = $field->getTitle();
			$block_arr['attribute:tip'] = $field->getTip();
			$block_arr['attribute:maxsize'] = $this->module->getAllowedMaxFileSize();
			$block_arr['attribute:field_id'] = $field->getId();
			$block_arr['attribute:is_important'] = $field->isImportant();
			/**
			 * @var iUmiFile $value
			 */
			$value = ($object) ? $object->getValue($field->getName()) : "";
			if ($value instanceof iUmiFile) {
				$block_arr['attribute:relative-path'] = $value->getFilePath(true);
				$block_arr['node:value'] = $value->getFilePath();
			} else {
				$block_arr['node:value'] = "";
			}

			if ($object) {
				$block_arr['void:object_id'] = $object->getId();
			}

			$objectId = ($object) ?  $object->getId() : 'new';
			$block_arr['attribute:input_name'] = "data[" . $objectId . "][{$field_name}]";

			$folder_name = $field_name . '/';
			$general_name = USER_FILES_PATH;

			if ($value instanceof umiFile && $value->getIsBroken() == false) {
				$value = false;
			}

			if ($value) {
				$destination_folder = "." . preg_replace($regexp, "", $value->getDirName());
			} else {
				$destination_folder = $general_name . ((is_dir($general_name . $folder_name)) ? $folder_name : '');
			}

			if (strpos($destination_folder, CURRENT_WORKING_DIR) === 0) {
				$destination_folder = str_replace(CURRENT_WORKING_DIR, '.', $destination_folder);
			}

			$block_arr['attribute:destination-folder'] = $destination_folder;
			return data::parseTemplate($template_block, $block_arr);
		}
	}