<?php

	/** Класс функционала оформления заказа в 1 клик */
	class EmarketPurchasingOneClick {

		/** @var emarket|EmarketPurchasingOneClick|EmarketMacros $module */
		public $module;

		/**
		 * Выводит данные для построения формы создания заказа в клик
		 * @param int $objectType идентификатор типа данных заказа в клик
		 * @return mixed
		 */
		public function createForm($objectType) {
			$languages = langsCollection::getInstance();
			$lang = getRequest('lang');
			$lang = $languages->getLangId($lang);

			if (!$lang) {
				$lang = $languages->getDefaultLang()->getId();
			}

			$cmsController = cmsController::getInstance();
			$cmsController->setCurrentLang($languages->getLang($lang));

			/** @var DataForms $data */
			$data = $cmsController->getModule('data');
			$form = $data->getCreateForm($objectType);

			if (array_key_exists('nodes:group', $form) && count($form['nodes:group']) > 0) {
				$form['nodes:group'][0]['attribute:lang'] = $languages->getLang($lang)->getPrefix();
			}

			return $form;
		}

		/**
		 * Принимает данные формы создания заказа в 1 клик и формирует на их основе заказ.
		 * Если указаны оба параметра - добавляет товар в корзину перед тем, как оформить заказ.
		 * Возвращает данные созданного заказа.
		 *
		 * @param mixed $addProductToCart Флаг добавления товара в корзину
		 * @param bool|int $elementId Идентификатор товара (объекта каталога)
		 * @return array
		 * @throws publicException
		 */
		public function getOneClickOrder($addProductToCart = false, $elementId = false) {
			/** @var data $dataModule */
			$dataModule = cmsController::getInstance()->getModule('data');
			$oneClickOrderType = $this->getOneClickOrderType();
			$requiredFieldsErrors = $dataModule->checkRequiredFields($oneClickOrderType->getId());

			if ($requiredFieldsErrors !== true) {
				throw new publicException(
					getLabel('error-required_one_click_list') . $dataModule->assembleErrorFields($requiredFieldsErrors)
				);
			}

			$validationErrors = $this->validateOneClickInfo();

			if (count($validationErrors) > 0) {
				return $validationErrors;
			}

			if ($addProductToCart && $elementId) {
				$_REQUEST['no-redirect'] = 1;
				$this->module->basket('put', 'element', $elementId);
			}

			$order = $this->module->getBasketOrder();
			$this->saveOneClickInfo($order);

			if ($order->getTotalAmount() < 1) {
				throw new publicException('%error-market-empty-basket%');
			}

			$order->order();

			return [
				'orderId' => $order->getNumber(),
			];
		}

		/**
		 * Валидирует данные формы создания заказа в один клик и возвращает полученные ошибки
		 * @return array
		 */
		public function validateOneClickInfo() {
			$dataForm = getRequest('data');
			$oneClickOrderType = $this->getOneClickOrderType();
			$errors = [];

			foreach ($oneClickOrderType->getAllFields() as $field) {
				$value = $dataForm['new'][$field->getName()];
				$restriction = baseRestriction::get($field->getRestrictionId());

				if (!$restriction) {
					continue;
				}

				if ($restriction instanceof iNormalizeInRestriction) {
					$value = $restriction->normalizeIn($value);
				}

				if ($restriction->validate($value)) {
					continue;
				}

				$fieldTitle = $field->getTitle();
				$errorMessage = getLabel('error-wrong-field-value');
				$errorMessage .= " \"{$fieldTitle}\" - " . $restriction->getErrorMessage();
				$errors['nodes:error'][] = $errorMessage;

				if (count($errors) > 0) {
					return $errors;
				}
			}

			return $errors;
		}

		/**
		 * Создает заказ в один клик, заполняет его и покупателя данными из формы
		 * @param order $order текущая корзина
		 * @throws coreException
		 */
		public function saveOneClickInfo(order $order) {
			$umiObjects = umiObjectsCollection::getInstance();

			$oneClickOrderType = $this->getOneClickOrderType();
			$oneClickCustomerId = $umiObjects->addObject($order->getName(), $oneClickOrderType->getId());
			$oneClickCustomer = $umiObjects->getObject($oneClickCustomerId);

			$this->saveCustomer($oneClickCustomer);
			$oneClickCustomer->commit();

			$regularCustomer = customer::get();

			if (!$regularCustomer->isFilled()) {
				$this->saveCustomer($regularCustomer);
				$regularCustomer->commit();
			}

			$order->setValue('purchaser_one_click', $oneClickCustomerId);
			$order->commit();
		}

		/**
		 * Сохраняет информацию о покупателе из формы заказа в один клик
		 * @param iUmiObject|umiObjectProxy $customer объект покупателя
		 */
		private function saveCustomer($customer) {
			$oneClickOrderType = $this->getOneClickOrderType();
			$dataForm = getRequest('data');

			foreach ($oneClickOrderType->getAllFields() as $field) {
				$value = $dataForm['new'][$field->getName()];
				$customer->setValue($field->getName(), $value);
			}
		}

		/**
		 * Возвращает тип данных "Заказ в один клик"
		 * @return iUmiObjectType
		 */
		private function getOneClickOrderType() {
			return umiObjectTypesCollection::getInstance()
				->getTypeByGUID('emarket-purchase-oneclick');
		}

		/** @deprecated */
		public function validOneClickInfo() {
			return $this->validateOneClickInfo();
		}

	}
