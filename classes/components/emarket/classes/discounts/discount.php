<?php
	/**
	 * Базовый класс скидки абстракного типа.
	 * Одновременно является родительским классом для всех классов типов скидок
	 * и предоставляет интерфейс для создания и получения скидки конкретного типа.
	 *
	 * По умолчанию в системе существуют следующие типы скидок:
	 *
	 * 1) "Накопительная скидка";
	 * 2) "Скидка на товары каталога";
	 * 3) "Скидка на заказ";
	 *
	 * Типы скидок влияют на применимость скидки к тем или иным сущностям.
	 *
	 * Каждая может содержать набор условий (discountRule), при которых скидка применяется.
	 * Каждая скидка содержит модификатор цены (discountModificator), который отвечает за правило расчета значения скидки.
	 */
	abstract class discount extends umiObjectProxy {
		/**
		 * @var umiObject $object объект-источник данных для скидки
		 */
		protected $object;
		/**
		 * @var discountModificator $modificator модификатор цены скидки
		 */
		protected $modificator;
		/**
		 * @var array|discountRule[] $rules правила скидки
		 */
		protected $rules = array();

		/**
		 * Конструктор
		 * @param umiObject $object объект-источник данных для скидки
		 */
		protected function __construct(umiObject $object) {
			parent::__construct($object);

			if ($modificatorId = $object->getValue('discount_modificator_id')) {
				$this->modificator = discountModificator::get($modificatorId, $this);
			}
			
			if ($rules = $object->getValue('discount_rules_id')) {
				foreach ($rules as $ruleId) {
					$this->rules[] = discountRule::get($ruleId, $this);
				}
			}
		}

		/**
		 * Возвращает название скидки
		 * @return string
		 */
		public function getName() {
			return $this->object->getName();
		}

		/**
		 * Устанавливает название скидки
		 * @param string $name название скидки
		 */
		public function setName($name) {
			$this->object->setName($name);
		}

		/**
		 * Возвращает список правил скидки
		 * @return discountRule[]
		 */
		public function getDiscountRules() {
			return $this->rules;
		}

		/**
		 * Добавляет правило к остальным правилим скидки
		 * @param discountRule $discountRule
		 */
		public function appendDiscountRule(discountRule $discountRule) {
			foreach ($this->rules as $rule) {
				if ($rule->getId() == $discountRule->getId()) {
					return;
				}
			}

			$this->rules[] = $discountRule;
			$discountRulesIds = array();

			foreach ($this->rules as $rule) {
				$discountRulesIds[] = $rule->getId();
			}

			$this->object->setValue('discount_rules_id', $discountRulesIds);
			$this->object->commit();
		}

		/**
		 * Возвращает модификатор цены скидки
		 * @return discountModificator
		 */
		public function getDiscountModificator() {
			return $this->modificator;
		}

		/**
		 * Устанавливает модификатор цены скидки
		 * @param discountModificator $discountModificator модификатор цены скидки
		 */
		public function setDiscountModificator(discountModificator $discountModificator) {
			$this->modificator = $discountModificator;
			$this->object->setValue('discount_modificator_id', $discountModificator->getId());
			$this->object->commit();
		}

		/**
		 * Применяет к цене скидки модификатор и возвращает модифицированную цену
		 * @param float $price цена скидки
		 * @return mixed
		 * @throws coreException
		 */
		public function recalcPrice($price) {
			$modificator = $this->getDiscountModificator();

			if (!$modificator instanceof discountModificator) {
				throw new coreException("Discount modificator couldn't be loaded");
			}

			return $modificator->recalcPrice($price);
		}

		/**
		 * Возвращает скидку по идентификатору объекта-источника данных для скидки
		 * @param $discountId
		 * @return bool|null|bonusDiscount|itemDiscount|orderDiscount
		 */
		public static function get($discountId) {
			static $cache = array();
			
			if (!$discountId) {
				return null;
			}

			if (isset($cache[$discountId])) {
				return $cache[$discountId];
			}
			
			$objects = umiObjectsCollection::getInstance();
			$discountObject = $objects->getObject($discountId);

			if (!$discountObject instanceof iUmiObject) {
				return false;
			}
			
			$discountTypeId = $discountObject->getValue('discount_type_id');
			$discountTypeObject = $objects->getObject($discountTypeId);

			if ($discountTypeObject instanceof iUmiObject == false) {
				return null;
			}
			
			$className = $discountTypeObject->codename . 'Discount';
			self::includeDiscount($className);
			
			return $cache[$discountId] = new $className($discountObject);
		}

		/**
		 * Создает скидку заданного типа
		 * @param string $discountName название скидки
		 * @param int $discountTypeId идентификатор типа скидки
		 * @return bonusDiscount|bool|itemDiscount|null|orderDiscount
		 * @throws Exception
		 * @throws coreException
		 * @throws valueRequiredException
		 */
		public static function add($discountName, $discountTypeId) {
			$objects = umiObjectsCollection::getInstance();
			$objectTypes = umiObjectTypesCollection::getInstance();
			
			$objectTypeId = $objectTypes->getTypeIdByHierarchyTypeName('emarket', 'discount');
			$objectId = $objects->addObject($discountName, $objectTypeId);
			$object = $objects->getObject($objectId);
			
			try {
				$object->setValue('discount_type_id', $discountTypeId);
				$object->commit();
			} catch (valueRequiredException $e) {
				$object->delete();
				throw $e;
			}

			return self::get($objectId);
		}

		/**
		 * Возвращает идентификатор типа скидки по его строковму идентификатору
		 * @param string $discountCode строковой идентификатор типа скидки
		 * @return int|bool
		 * @throws selectorException
		 */
		public static function getTypeId($discountCode) {
			static $typeId = array();

			if (isset($typeId[$discountCode])) {
				return $typeId[$discountCode];
			}

			$sel = new selector('objects');
			$sel->types('hierarchy-type')->name('emarket', 'discount_type');
			$sel->where('codename')->equals($discountCode);
			$sel->option('no-length')->value(true);
			$sel->option('return')->value('id');
			$result = $sel->result();
			$discountTypeId = (count($result) > 0 && isset($result[0]['id'])) ? $result[0]['id'] : false;

			return $typeId[$discountCode] = $discountTypeId;
		}

		/**
		 * Подключает файлы с реализациями типов скидки
		 */
		public static function init() {
			self::includeDiscount('itemDiscount');
			self::includeDiscount('orderDiscount');
			self::includeDiscount('bonusDiscount');
		}

		/**
		 * Подключает файл с реализацией типа скидки и возвращает результат операции
		 * @param string $name имя файла с реализацией скидки
		 * @return bool
		 */
		private static function includeDiscount($name) {
			static $cache = Array();

			if (isset($cache[$name])) {
				return $cache[$name];
			}
			
			$filePath = dirname(__FILE__) . '/discounts/' . $name . '.php';

			if (is_file($filePath)) {
				/**
				 * @noinspection PhpIncludeInspection
				 */
				require_once $filePath;
				return $cache[$name] = true;
			}

			return $cache[$name] = false;
		}
	};
?>