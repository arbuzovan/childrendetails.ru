<?php
	/**
	 * Класс модуля "Шаблоны данных".
	 *
	 * Модуль отвечает за:
	 *
	 * 1) Работу с типами данных и справочниками;
	 * 2) Работу с полями и группами полей;
	 * 3) Генерацию RSS и ATOM фидов;
	 * 4) Работу файлового менеджера;
	 * 5) Работу модуля "Корзина";
	 * 6) Генерацию данных для построения форм редактирования и создания объектов в клиентской части;
	 * 7) Генерацию данных для построения форм редактирования и создания объектов в административной части;
	 * 8) Получения значений полей и групп для tpl шаблонизатора;
	 * 9) Применение usel выборок для tpl шаблонизатора;
	 * @link http://help.docs.umi-cms.ru/rabota_s_modulyami/modul_shablony_dannyh/
	 */
	class data extends def_module {

		/**
		 * Конструктор
		 */
		public function __construct() {
			parent::__construct();

			if (cmsController::getInstance()->getCurrentMode() == "admin") {
				$commonTabs = $this->getCommonTabs();

				if ($commonTabs) {
					$commonTabs->add("types", array(
						"type_add",
						"type_edit"
					));
					$commonTabs->add("guides", array(
						"guide_add",
						"guide_items",
						"guide_item_edit",
						"guide_item_add"
					));
				}

				$configTabs = $this->getConfigTabs();

				if ($configTabs) {
					$configTabs->add("config");
				}

				$this->__loadLib("admin.php");
				$this->__implement("DataAdmin");

				$this->loadAdminExtension();

				$this->__loadLib("customAdmin.php");
				$this->__implement("DataCustomAdmin", true);
			}

			$this->__loadLib("macros.php");
			$this->__implement("DataMacros");

			$this->loadSiteExtension();

			$this->__loadLib("customMacros.php");
			$this->__implement("DataCustomMacros", true);

			$this->__loadLib("feeds.php");
			$this->__implement("DataFeeds");

			$this->__loadLib("fileManager.php");
			$this->__implement("DataFileManager");

			$this->__loadLib("forms.php");
			$this->__implement("DataForms");

			$this->loadCommonExtension();
			$this->loadTemplateCustoms();
		}

		/**
		 * Возвращает список правил валидации полей (только для xslt)
		 * @return array
		 * @throws tplOnlyException
		 * @throws xsltOnlyException
		 */
		public function getRestrictionsList() {
			$this->templatesMode('xslt');

			$block_arr = array();

			$restrictions = baseRestriction::getList();
			$items_arr = array();
			foreach($restrictions as $restriction) {
				if ($restriction instanceof baseRestriction) {
					$items_arr[] = $restriction;
				}
			}
			$block_arr['items']['nodes:item'] = $items_arr;
			return $block_arr;
		}

		/**
		 * Возвращает ссылку на редактирование сущности модуля
		 * @param int $objectId идентификатор сущности
		 * @param bool $type
		 * @return string
		 */
		public function getObjectEditLink($objectId, $type = false) {
			return $this->pre_lang . '/admin/data/guide_item_edit/' . $objectId . '/';
		}

		/**
		 * Выводит ссылку на добавление и редактирование объектного типа данных
		 * @param int $typeId идентификатор объектного типа данных
		 * @return array
		 */
		public function getObjectTypeEditLink($typeId) {
			return [
				'create-link'	=> $this->pre_lang . "/admin/data/type_add/" . $typeId . "/",
				'edit-link'		=> $this->pre_lang . "/admin/data/type_edit/" . $typeId . "/"
			];
		}

		/**
		 * Возвращает названия полей, разделенные запятой, по списку их идентификаторов
		 * @param array $errorFields список идентификаторов полей
		 * @return string
		 */
		public function assembleErrorFields($errorFields) {
			$result     = array();
			$collection = umiFieldsCollection::getInstance();
			foreach($errorFields as $fieldId){
				$field = $collection->getField($fieldId);
				$result[] = $field->getTitle();
			}
			return !empty($result) ? implode(', ', $result) : '';
		}

		/**
		 * Проверяет заданы ли значения всех обязательных полей
		 * для создания объекта
		 * @param int $typeId идентификатор объектного типа данных
		 * @return array|bool
		 * @throws coreException
		 */
		public function checkRequiredFields($typeId) {
			$type = umiObjectTypesCollection::getInstance()->getType($typeId);
			if (!$type instanceof umiObjectType) {
				throw new coreException(getLabel('label-cannot-detect-type'));
			}

			$allFields = $type->getAllFields();

			$inputData = getRequest('data');
			if ((!$inputData || !@is_array($inputData['new'])) && (!isset($_FILES['data']['name']['new'])) ) {
				$inputData = array();
			} else {
				$tmp = array();
				if (@is_array($inputData['new'])) {
					$tmp = array_merge($tmp, $inputData['new']);
				}
				if (isset($_FILES['data']['name']['new']) && is_array($_FILES['data']['name']['new'])) {
					$tmp = array_merge($tmp, $_FILES['data']['name']['new']);
				}
				$inputData = $tmp;
			}

			$errorFields = array();
			/**
			 * @var iUmiField|iUmiEntinty $field
			 */
			foreach($allFields as $field) {
				if ($field->getIsRequired()) {
					$fieldName = $field->getName();
					if (!isset($inputData[$fieldName]) || empty($inputData[$fieldName])) {
						$errorFields[] = $field->getId();
					}
				}
			}
			return !empty($errorFields) ? $errorFields : true;
		}

		/**
		 * Возвращает список групп полей объектного типа данных
		 * @param int|string $type_id идентификатор или guid объектного типа данных
		 * @return array|bool
		 * @throws coreException
		 */
		public function getTypeFieldGroups($type_id) {
			if (!is_numeric($type_id)) {
				$type_id = umiObjectTypesCollection::getInstance()->getTypeIdByGUID($type_id);
			}
			if ($type = umiObjectTypesCollection::getInstance()->getType($type_id)) {
				return $type->getFieldsGroupsList();
			} else {
				return false;
			}
		}

		/**
		 * Возвращает максимально возможные размер файла для загрузки
		 * @param bool $fileType использовать системное ограничение на изображения
		 * @return int|float
		 */
		public function getAllowedMaxFileSize($fileType = false) {
			$sizes = array();
			$sizes[] = $this->getMegaBytes(ini_get('upload_max_filesize'));
			$sizes[] =  $this->getMegaBytes(ini_get('post_max_size'));
			$sizes[] =  $this->getMegaBytes(ini_get('memory_limit'));

			if ($fileType) {
				$regedit = regedit::getInstance();
				if ($regedit->getVal("//settings/max_img_filesize")) {
					$sizes[] = $regedit->getVal("//settings/max_img_filesize");
				}
			}

			return min($sizes);
		}

		/**
		 * Переводит строкое представление веса файла в
		 * мегабайты
		 * @param string $val строкое представление веса
		 * @return float|int|string
		 */
		public function getMegaBytes($val) {
			$val = strtolower(trim($val));
			$last = substr($val, -1);
			$val = (int) $val;

			switch($last) {
				case 'g': {
					$val *= 1024;
					break;
				}
				case 'm': {
					$val = $val;
					break;
				}
				case 'k': {
					$val = $val / 1024;
					break;
				}
			}
			return $val;
		}

		/**
		 * Выводит ссылку на форму добавления и редактирования сущности модуля
		 * @param int $type_id идентификатор объектного типа данных
		 * @return array
		 */
		public function getEditLink($type_id) {
			$link_add = false;
			$link_edit = $this->pre_lang . "/admin/data/type_edit/{$type_id}/";
			return [$link_add, $link_edit];
		}
	};
?>
