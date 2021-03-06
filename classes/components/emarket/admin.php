<?php

	/**
	 * Класс функционала административной панели
	 */
	class EmarketAdmin {

		use baseModuleAdmin;
		/**
		 * @var emarket $module
		 */
		public $module;

		/**
		 * Возвращает список валют
		 * @throws coreException
		 * @throws selectorException
		 */
		public function currency() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'currency');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы добавления валюты,
		 * если передан $_REQUEST['param0'] = do, то создает валюту
		 * и перенаправляет страницу, где ее можно отредактировать.
		 * @throws coreException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function currency_add() {
			$mode = (string) getRequest('param0');

			$inputData = [
				'type' => 'currency',
				'allowed-element-types' => [
					'currency'
				]
			];

			if ($mode == "do") {
				$object = $this->saveAddedObjectData($inputData);
				$this->chooseRedirect($this->module->pre_lang . "/admin/emarket/currency_edit/{$object->getId()}/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования валюты.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения валюты и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function currency_edit() {
			$object = $this->expectObject('param0');
			$mode = (string) getRequest('param1');

			$inputData = [
				'object' => $object,
				'allowed-element-types' => [
					'currency'
				]
			];

			if ($mode == "do") {
				$this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");

			$data = $this->prepareData($inputData, "object");

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает список способов доставки
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function delivery() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'delivery');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы добавления способа доставки,
		 * если передан $_REQUEST['param0'] = do, то создает способ доставки
		 * и перенаправляет страницу, где его можно отредактировать.
		 * @throws coreException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function delivery_add() {
			$mode = (string) getRequest('param0');

			$inputData = [
				'type' => 'delivery',
				'type-id' => getRequest('type-id'),
				'allowed-element-types' => ['delivery']
			];

			if ($mode == "do") {
				$data = getRequest("data");
				$deliveryType = $data["new"]["delivery_type_id"];

				if ($typeObject = umiObjectsCollection::getInstance()->getObject($deliveryType)) {
					$inputData['type-id'] = umiObjectTypesCollection::getInstance()->getTypeIdByGUID($typeObject->delivery_type_guid);
				}

				$object = $this->saveAddedObjectData($inputData);
				$this->chooseRedirect($this->module->pre_lang . "/admin/emarket/delivery_edit/{$object->getId()}/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования способа доставки.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения способа доставки и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function delivery_edit() {
			$object = $this->expectObject('param0');
			$mode = (string) getRequest('param1');

			$inputData = [
				'object' => $object,
				'allowed-element-types' => [
					'delivery'
				]
			];

			if ($mode == "do") {
				$this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования адреса доставки.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения адреса и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function delivery_address_edit() {
			$object = $this->expectObject('param0');
			$mode = (string) getRequest('param1');

			$inputData = [
				'object' => $object,
				'allowed-element-types' => [
					'delivery_address'
				]
			];

			if ($mode == "do") {
				$this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Врозвращает список скидок
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function discounts() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'discount');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Изменяет активность скидок
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function activity() {
			$objects = getRequest('object');

			if (!is_array($objects)) {
				$objects = [$objects];
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
		 * Возвращает данные для создания формы добавления скидки,
		 * если передан $_REQUEST['param0'] = do, то создает скидку,
		 * применяет к ней модификатор, правила скидки
		 * и перенаправляет страницу, где скидку можно отредактировать.
		 * @throws coreException
		 * @throws expectObjectException
		 * @throws publicAdminException
		 */
		public function discount_add() {
			$inputData = [
				"type" => "discount"
			];

			$mode = (string) getRequest('param0');

			if ($mode == "do") {
				$data = getArrayKey(getRequest('data'), 'new');
				$discountName = getRequest('name');
				$discountTypeId = getArrayKey($data, 'discount_type_id');
				$modificatorId = getArrayKey($data, 'discount_modificator_id');
				$rulesId = getArrayKey($data, 'discount_rules_id');

				try {
					$discount = discount::add($discountName, $discountTypeId);

					try {
						$modificatorTypeObject = $this->expectObject($modificatorId, true, true);
					} catch (publicAdminException $e) {
						$modificatorTypeObject = null;

						if ($discount instanceof discount) {
							$discount->delete();
						}

						$this->module->errorNewMessage(getLabel('error-modificator-required'));
						$this->module->errorPanic();
					}

					$modificatorObject = discountModificator::create($discount, $modificatorTypeObject);
					$discount->setDiscountModificator($modificatorObject);

					foreach ($rulesId as $ruleId) {
						$ruleTypeObject = $this->expectObject($ruleId, true, true);
						$ruleObject = discountRule::create($discount, $ruleTypeObject);

						if (!$ruleObject instanceof discountRule) {
							$discount->delete();
							throw new publicAdminException("discountRule #{$ruleId} \"{$ruleTypeObject->name}\" class not found");
						}

						$discount->appendDiscountRule($ruleObject);
					}

					$discount->commit();
				} catch (valueRequiredException $e) {
					$discount = null;
					$this->module->errorNewMessage($e->getMessage());
					$this->module->errorPanic();
				}

				$this->chooseRedirect($this->module->pre_lang . "/admin/emarket/discount_edit/" . $discount->getId() . "/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования скидки.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения скидки и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function discount_edit() {
			$object = $this->expectObject("param0");
			$mode = (string) getRequest('param1');

			if ($mode == "do") {
				$this->saveEditedObjectData($object);

				unset($_REQUEST['type-id']);
				unset($_REQUEST['name']);

				$subObjectsId = [];
				if ($object->discount_modificator_id) {
					$subObjectsId[] = $object->discount_modificator_id;
				}
				if ($object->discount_rules_id) {
					$subObjectsId = array_merge($subObjectsId, $object->discount_rules_id);
				}

				foreach ($subObjectsId as $id) {
					$subObject = $this->expectObject($id, true, true);
					$this->saveEditedObjectData($subObject);
				}

				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($object, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает список модификаторов для скидки
		 * @param bool|int $discountTypeId идентификатор типа скидки
		 * @param bool|int $discountId идентификатор скидки
		 * @return array
		 */
		public function getModificators($discountTypeId = false, $discountId = false) {
			$items = discountModificator::getList($discountTypeId);
			$discount = discount::get($discountId);

			if ($discount instanceof discount) {
				$discountModId = $discount->getDiscountModificator()->getObject()->modificator_type_id;

				foreach ($items as $i => $mod) {
					$items[$i] = [
						'attribute:id' => $mod->id,
						'attribute:name' => $mod->name
					];

					if ($mod->id == $discountModId) {
						$items[$i]['attribute:selected'] = 'selected';
					}
				}
			}

			return [
				'items' => [
					'nodes:item' => $items
				]
			];
		}

		/**
		 * Возвращает список правил для скидки
		 * @param bool|int $discountTypeId идентификатор типа скидки
		 * @param bool|int $discountId идентификатор скидки
		 * @return array
		 */
		public function getRules($discountTypeId = false, $discountId = false) {
			$items = discountRule::getList($discountTypeId);
			$discount = discount::get($discountId);

			if ($discount instanceof discount) {
				$discount = discount::get($discountId);
				$discountRules = $discount->getDiscountRules();
				$discountRulesId = [];

				/**
				 * @var discountRule $rule
				 */
				foreach ($discountRules as $rule) {
					$discountRulesId[] = $rule->getObject()->rule_type_id;
				}

				foreach ($items as $i => $rule) {
					$items[$i] = [
						'attribute:id' => $rule->id,
						'attribute:name' => $rule->name
					];

					if (in_array($rule->id, $discountRulesId)) {
						$items[$i]['attribute:selected'] = 'selected';
					}
				}
			}

			return [
				'items' => ['nodes:item' => $items]
			];
		}

		/**
		 * Возвращает список заказов
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function orders() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'order');
			$sel->where('number')->isnotnull();
			$sel->where('name')->notequals('dummy');
			$sel->limit($offset, $limit);

			if (!getRequest('order_filter')) {
				$sel->order('order_date')->desc();
			}

			selectorHelper::detectFilters($sel);
			$domains = getRequest('domain_id');

			if (is_array($domains) && sizeof($domains)) {
				$domainsCollection = domainsCollection::getInstance();
				if (sizeof($domainsCollection->getList()) > 1) {
					$sel->where('domain_id')->equals($domains[0]);
				}
			}

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования заказа.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения заказа и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function order_edit() {
			/**
			 * @var iUmiObject|iUmiEntinty $object
			 */
			$object = $this->expectObject("param0", true);
			$mode = (string) getRequest('param1');
			$this->setHeaderLabel("header-users-edit-" . $this->getObjectTypeMethod($object));

			$inputData = [
				"object" => $object,
				"allowed-element-types" => [
					'emarket', 'order'
				]
			];

			if ($mode == "do") {
				$oldDeliveryPrice = $object->getValue('delivery_price');
				$object = $this->saveEditedObjectData($inputData);
				$newDeliveryPrice = $object->getValue('delivery_price');
				$order = order::get($object->getId());

				$itemsAmounts = getRequest('order-amount-item');
				$itemsToDelete = getRequest('order-del-item');
				$itemsDiscountValues = getRequest('item-discount-value');
				$orderDiscountValue = getRequest('order-discount-value');
				$itemsWeight = getRequest('order-weight-item');

				$isChanged = false;

				if (is_array($itemsAmounts)) {
					foreach ($itemsAmounts as $itemId => $amount) {
						$item = $order->getItem($itemId);

						if (!$item instanceof orderItem) {
							continue;
						}

						$amount = (int) $amount;

						if ($item->getAmount() == $amount) {
							continue;
						}

						$item->setAmount($amount);
						$item->commit();
						$isChanged = true;
					}
				}

				if (is_array($itemsDiscountValues)) {
					foreach ($itemsDiscountValues as $itemId => $itemDiscountValue) {
						$item = $order->getItem($itemId);

						if (!$item instanceof orderItem) {
							continue;
						}

						$itemDiscountValue = (float) $itemDiscountValue;

						if ($item->getDiscountValue() == $itemDiscountValue) {
							continue;
						}

						$item->setDiscountValue($itemDiscountValue);
						$item->commit();
						$isChanged = true;
					}
				}

				if (is_array($itemsWeight)) {
					foreach ($itemsWeight as $itemId => $itemWeight) {
						$item = $order->getItem($itemId);

						if (!$item instanceof orderItem) {
							continue;
						}

						$escapedWeight = (float) $itemWeight;

						if ($item->getWeight() == $escapedWeight) {
							continue;
						}

						$item->setWeight($escapedWeight);
						$item->commit();
						$isChanged = true;
					}
				}

				if (isset($orderDiscountValue)) {
					if ($order->getDiscountValue() != $orderDiscountValue) {
						$order->setDiscountValue($orderDiscountValue);
						$isChanged = true;
					}
				}

				if (is_array($itemsToDelete)) {
					foreach ($itemsToDelete as $itemId) {
						$item = orderItem::get($itemId);

						if (!$item instanceof orderItem) {
							continue;
						}

						$order->removeItem($item);
						$isChanged = true;
					}
				}

				if ($isChanged) {
					$order->refresh();
					$order->saveTotalProperties();
					$order->commit();
				}

				if ($oldDeliveryPrice != $newDeliveryPrice && !$isChanged) {
					$originalPrice = $object->getValue('total_original_price');
					$totalPrice = $originalPrice;

					$discount = $order->getDiscount();
					if ($discount instanceof discount) {
						$totalPrice = $discount->recalcPrice($originalPrice);
					}

					$totalPrice += $newDeliveryPrice;
					$object->setValue('total_price', $totalPrice);
					$object->commit();
				}

				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");

			$order = order::get($object->getId());
			$paymentId = $order->getValue('payment_id');

			if ($paymentId) {
				$payment = payment::get($paymentId, $order);
				if (method_exists($payment, "admin" . $mode)) {
					/**
					 * @var kupivkreditPayment $payment
					 */
					$arrayBlock = call_user_func([$payment, "admin" . $mode]);
					$arrayBlock["@type"] = $payment->getCodeName();
					$data["payment"] = $arrayBlock;
				}
			}

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки способа оплаты заказа.
		 * @throws expectObjectException
		 */
		public function order_payment() {
			$object = $this->expectObject("param0", true);
			$action = (string) getRequest('param1');

			if (!$action) {
				$this->module->redirect("/admin/emarket/order_edit/" . $object->getId() . "/");
			}

			$order = order::get($object->getId());
			cmsController::getInstance()->errorUrl = "/admin/emarket/order_edit/" . $object->getId() . "/";
			$paymentId = $order->getValue('payment_id');

			if (!$paymentId) {
				$this->module->errorNewMessage(getLabel("error-no-payment"));
				$this->module->errorPanic();
			}

			$payment = payment::get($paymentId, $order);

			if (method_exists($payment, "admin_" . $action)) {
				/**
				 * @var kupivkreditPayment $payment
				 */
				$response = call_user_func([$payment, "admin_" . $action]);
				$this->setData($response);

				if ($response["status"] != "OK") {
					if (!getRequest("xmlMode") && !getRequest("jsonMode")) {
						$this->module->errorNewMessage(getLabel("error-payment-system", false, $response['result']));
						$this->module->errorPanic();
					}
				} else {
					if (!getRequest("xmlMode") && !getRequest("jsonMode")) {
						$this->module->redirect("/admin/emarket/order_edit/" . $object->getId() . "/");
					}
				}
			}

			$this->doData();
		}

		/**
		 * Выводит в буффер печатную форму заказа
		 * @throws coreException
		 * @throws expectObjectException
		 * @throws wrongElementTypeAdminException
		 */
		public function order_printable() {
			$object = $this->expectObject("param0", true);
			$typeId = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName('emarket', 'order');

			if ($object->getTypeId() != $typeId) {
				throw new wrongElementTypeAdminException(getLabel("error-unexpected-element-type"));
			}

			$orderId = $object->getId();
			$uri = "uobject://{$orderId}/?transform=sys-tpls/emarket-order-printable.xsl";
			$result = file_get_contents($uri);

			/**
			 * @var HTTPOutputBuffer $buffer
			 */
			$buffer = outputBuffer::current();
			$buffer->charset('utf-8');
			$buffer->contentType('text/html');
			$buffer->clear();
			$buffer->push($result);
			$buffer->end();
		}

		/**
		 * Возвращает список способов оплаты
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function payment() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'payment');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы добавления способа оплаты,
		 * если передан $_REQUEST['param0'] = do, то создает способ оплаты
		 * и перенаправляет страницу, где его можно отредактировать.
		 * @throws coreException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function payment_add() {
			$mode = (string) getRequest('param0');

			$inputData = [
				'type' => 'payment',
				'type-id' => getRequest('type-id'),
				'allowed-element-types' => [
					'payment'
				]
			];

			if ($mode == "do") {
				$data = getRequest("data");
				$paymentType = $data["new"]["payment_type_id"];

				if ($typeObject = umiObjectsCollection::getInstance()->getObject($paymentType)) {
					$inputData['type-id'] = umiObjectTypesCollection::getInstance()->getTypeIdByGUID($typeObject->payment_type_guid);
				}

				$object = $this->saveAddedObjectData($inputData);
				$this->chooseRedirect($this->module->pre_lang . "/admin/emarket/payment_edit/{$object->getId()}/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования способа оплаты.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения способа оплаты и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function payment_edit() {
			$object = $this->expectObject('param0');
			$mode = (string) getRequest('param1');

			$inputData = [
				'object' => $object,
				'allowed-element-types' => [
					'payment'
				]
			];

			if ($mode == "do") {
				$this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает список складов
		 * @return bool|void
		 * @throws coreException
		 * @throws selectorException
		 */
		public function stores() {
			$this->setDataType("list");
			$this->setActionType("view");

			if ($this->module->ifNotXmlMode()) {
				$this->setDirectCallError();
				$this->doData();
				return true;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $curr_page * $limit;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'store');
			$sel->limit($offset, $limit);
			selectorHelper::detectFilters($sel);

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы добавления склада,
		 * если передан $_REQUEST['param0'] = do, то создает склад
		 * и перенаправляет страницу, где его можно отредактировать.
		 * @throws coreException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function store_add() {
			$mode = (string) getRequest('param0');

			$inputData = [
				'type' => 'store',
				'allowed-element-types' => [
					'store'
				]
			];

			if ($mode == "do") {
				if (!empty($_REQUEST['data']['new']['primary'])) {
					$this->module->clearPrimary();
				}

				$object = $this->saveAddedObjectData($inputData);
				$this->chooseRedirect($this->module->pre_lang . "/admin/emarket/store_edit/{$object->getId()}/");
			}

			$this->setDataType("form");
			$this->setActionType("create");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает данные для создания формы редактирования склада.
		 * Если передан ключевой параметр $_REQUEST['param1'] = do,
		 * то сохраняет изменения склада и производит перенаправление.
		 * Адрес перенаправление зависит от режима кнопки "Сохранить".
		 * @throws coreException
		 * @throws expectObjectException
		 */
		public function store_edit() {
			$object = $this->expectObject('param0');
			$mode = (string) getRequest('param1');

			$inputData = [
				'object' => $object,
				'allowed-element-types' => [
					'store'
				]
			];

			if ($mode == "do") {
				if (!empty($_REQUEST['data'][$object->getId()]['primary'])) {
					$this->module->clearPrimary();
				}

				$this->saveEditedObjectData($inputData);
				$this->chooseRedirect();
			}

			$this->setDataType("form");
			$this->setActionType("modify");
			$data = $this->prepareData($inputData, "object");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает список заказов со статусом "Готов",
		 * созданных за заданные период времени.
		 * @throws coreException
		 * @throws selectorException
		 */
		public function realpayments() {
			$fields_filter = getRequest('fields_filter');
			$fromDate = '';
			$toDate = '';

			if (!empty($fields_filter) && array_key_exists('order_date', $fields_filter)) {
				$string = $fields_filter['order_date']['gt'];
				$pattern = '/\./';
				$varDate = preg_split($pattern, $string);
				$fromDate = mktime(0, 0, 0, $varDate[1], $varDate[0], $varDate[2]);

				$string = $fields_filter['order_date']['lt'];
				$pattern = '/\./';
				$varDate = preg_split($pattern, $string);
				$toDate = mktime(0, 0, 0, $varDate[1], $varDate[0], $varDate[2]);
			}

			$range = $this->getDateRange($fromDate, $toDate);

			if ($this->module->ifNotXmlMode()) {
				$data = [
					'@fromDate' => $range['fromDate'],
					'@toDate' => $range['toDate'],
				];

				$this->setData($data);
				$this->doData();
				return;
			}

			$limit = getRequest('per_page_limit');
			$curr_page = (int) getRequest('p');
			$offset = $limit * $curr_page;

			$sel = new selector('objects');
			$sel->types('object-type')->guid('emarket-orderstatus');
			$sel->where('codename')->equals('ready');
			$ready = $sel->first;

			$sel = new selector('objects');
			$sel->types('object-type')->name('emarket', 'order');
			$sel->where('status_id')->equals($ready);
			$sel->where('order_date')->between($range['fromDate'], $range['toDate']);
			$sel->where('name')->notequals('dummy');
			$sel->limit($offset, $limit);
			if (!getRequest('order_filter')) {
				$sel->order('order_date')->desc();
			}
			selectorHelper::detectFilters($sel);

			$domains = getRequest('domain_id');
			if (is_array($domains) && sizeof($domains)) {
				$domainsCollection = domainsCollection::getInstance();
				if (sizeof($domainsCollection->getList()) > 1) {
					$sel->where('domain_id')->equals($domains[0]);
				}
			}

			$this->setDataRange($limit, $offset);
			$data = $this->prepareData($sel->result(), "objects");
			$data = array_merge($data, [
				'@fromDate' => $range['fromDate'],
				'@toDate' => $range['toDate'],
			]);

			$this->setData($data, $sel->length());
			$this->doData();
		}

		/**
		 * Устанавливает период времени создания заказов
		 */
		public function setDateRange() {
			$range = $this->getDateRange(getRequest('param0'), getRequest('param1'));

			$data = [
				'@fromDate' => $range['fromDate'],
				'@toDate' => $range['toDate'],
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает из сессии период времени создания заказов.
		 * Если передан параметры - запишет их в сессию.
		 * @param int|bool $fromDate timestamp даты начала периода
		 * @param int|bool $toDate timestamp даты конца периода
		 * @return array
		 */
		public function getDateRange($fromDate, $toDate) {
			$fromDate = (int) $fromDate;
			$toDate = (int) $toDate;
			$session = \UmiCms\Service::Session();

			if (empty($fromDate) && !$session->isExist('orderFromDate')) {
				$fromDate = mktime(0, 0, 0, date('m'), date('d'), date('y'));
			}

			if (!empty($fromDate)) {
				$session->set('orderFromDate', $fromDate);
			}

			if (empty($toDate) && !$session->isExist('orderToDate')) {
				$toDate = strtotime("+1 day", mktime(0, 0, 0, date('m'), date('d'), date('y')));
			}

			if (!empty($toDate)) {
				$session->set('orderToDate', $toDate);
			}

			return [
				'fromDate' => $session->get('orderFromDate'),
				'toDate' => $session->get('orderToDate')
			];
		}

		/**
		 * Возвращает статистические данные магазина
		 * @throws coreException
		 */
		public function stats() {
			$this->setDataType("list");
			$this->setActionType("view");
			$range = $this->getDateRange(getRequest('fromDate'), getRequest('toDate'));
			$umiConfig = mainConfiguration::getInstance();

			$params = [
				"stats" => [],
				"popular" => [
					'@max-popular' => $umiConfig->get('modules', 'emarket.popular.max-items'),
					'@currency' => $umiConfig->get('system', 'default-currency'),
				]
			];

			foreach ($this->module->allowedReports as $stat) {
				$params['stats'][$stat . ":stat-" . $stat] = null;
			}

			$data = $this->prepareData($params, "settings");
			$data = array_merge($data, [
				'@fromDate' => $range['fromDate'],
				'@toDate' => $range['toDate'],
			]);

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Переиндексирует заказы для сбора статистики.
		 * Вызывается итерационно.
		 * @return bool|void
		 */
		public function partialRecalc() {
			if (isDemoMode()) {
				return false;
			}

			$this->setDataType("settings");
			$this->setActionType("view");
			$page = (int) getRequest("page");
			$umiRegistry = regedit::getInstance();

			$emarketTop = new emarketTop();

			if ($page == 0) {
				$emarketTop->clearTableTop();
				$umiRegistry->setVal('//modules/emarket/last-reindex-result', false);
				$umiRegistry->setVal('//modules/emarket/last-reindex-date', date('Y-m-d'));
			}

			$config = mainConfiguration::getInstance();
			$total = (int) $emarketTop->allOrdersRecalculate();
			$limit = $config->get('modules', 'emarket.reindex.max-items');

			if ($limit == 0) {
				$limit = 5;
			}

			$result = $emarketTop->recalculation($limit, $page);

			if ($result['current'] >= $total) {
				$umiRegistry->setVal('//modules/emarket/last-reindex-result', true);
			}

			$data = [
				'index-items' => [
					'attribute:current' => $result['current'],
					'attribute:total' => $total,
					'attribute:page' => $result['page']
				]
			];

			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает дату и результат последней переиндексации.
		 */
		public function getLastReindexDate() {
			$this->setDataType("list");
			$this->setActionType("view");

			$umiRegistry = regedit::getInstance();
			$reindexDate = $umiRegistry->getVal('//modules/emarket/last-reindex-date');
			$reindexResult = (bool) $umiRegistry->getVal('//modules/emarket/last-reindex-result');

			$this->setData([
				'reindexDate' => $reindexDate,
				'reindexResult' => $reindexResult
			], 2);

			$this->doData();
		}

		/**
		 * Удаляет сущности модуля
		 * @throws coreException
		 * @throws expectObjectException
		 * @throws publicAdminException
		 * @throws wrongElementTypeAdminException
		 */
		public function del() {
			$objects = getRequest('element');

			if (!is_array($objects)) {
				$objects = [$objects];
			}

			$config = mainConfiguration::getInstance();
			$defaultCurrencyCode = $config->get('system', 'default-currency');

			foreach ($objects as $objectId) {
				$object = $this->expectObject($objectId, false, true);

				if ($object->getMethod() == 'currency' && ($object->getValue('codename') == $defaultCurrencyCode)) {
					throw new publicAdminException(getLabel('error-delete-default-currency'));
				}

				$params = [
					'object' => $object
				];

				$this->deleteObject($params);
			}

			$this->setDataType("list");
			$this->setActionType("view");
			$data = $this->prepareData($objects, "objects");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Авторизует пользователя в качестве другого пользователя или незарегистрированного покупателя.
		 * @param bool|int $userId идентификатор пользователя, под которым происходит авторизация
		 * @param bool|int $orderId идентификатор заказа, который редактировался в админ. панели
		 * @return bool
		 */
		public function actAsUser($userId = false, $orderId = false) {
			if (!$userId) {
				$userId = getRequest('param0');
			}

			$objects = umiObjectsCollection::getInstance();
			$user = $objects->getObject($userId);

			if (!$user instanceof umiObject) {
				return false;
			}

			$auth = UmiCms\Service::Auth();
			$loginSuccessful = $auth->loginAndStorePreviousUserId($user->getId());
			$cookieJar = \UmiCms\Service::CookieJar();



			if (!$loginSuccessful &&$user->getTypeGUID() == 'emarket-customer') {

				$cookieJar->set('customer-id', $userId, time() + customer::$defaultExpiration);
				} else {
				$cookieJar->remove('customer-id');
			}

			if ($loginSuccessful && $orderId) {
				\UmiCms\Service::Session()->set('admin-editing-order', $orderId);
			}

			$this->chooseRedirect("/");
		}

		/**
		 * Устанавливает у заказа статус "Редактируется" и
		 * авторизует текущего пользователя под покупателем
		 * данного заказа.
		 * @param bool|int $orderId идентификатор заказа
		 * @return bool
		 */
		public function editOrderAsUser($orderId = false) {
			if (!$orderId) {
				$orderId = getRequest('param0');
			}

			$objects = umiObjectsCollection::getInstance();
			$order = $objects->getObject($orderId);

			if (!$order instanceof umiObject) {
				return false;
			}

			if ($order->getTypeGUID() !== 'emarket-order') {
				return false;
			}

			$statusId = order::getStatusByCode('editing');
			$order->setValue('status_id', $statusId);
			$order->commit();

			$this->actAsUser($order->getValue('customer_id'), $orderId);
		}

		/**
		 * Возвращает список заказов покупателя
		 * @param int $customerId идентификатор покупателя
		 * @return array
		 * @throws selectorException
		 */
		public function ordersList($customerId) {
			static $cache = [];

			if (isset($cache[$customerId])) {
				return $cache[$customerId];
			}

			$sel = new selector('objects');
			$sel->option('search-in-related-object')->value(false);
			$sel->types('object-type')->name('emarket', 'order');
			$sel->where('customer_id')->equals($customerId);
			$sel->where('status_id')->isNull(false);
			$sel->option('no-length')->value(true);
			$sel->option('load-all-props')->value(true);
			$sel->order("id")->desc();

			return $cache[$customerId] = [
				'items' => [
					'nodes:item' => $sel->result()
				]
			];
		}

		/**
		 * Возвращает основные настройки модуля.
		 * Если передан ключевой параметр $_REQUEST['param0'] = do,
		 * то сохраняет настройки и перенаправляет на страницу настроек.
		 * @throws coreException
		 */
		public function config() {
			$config = mainConfiguration::getInstance();
			$umiRegistry = regedit::getInstance();

			$params = [
				'emarket-options' => [
					'int:max_compare_items' => null,
					'boolean:currency' => null,
					'boolean:stores' => null,
					'boolean:payment' => null,
					'boolean:delivery' => null,
					'boolean:discounts' => null,
					'boolean:delivery-with-address' => null,
					'boolean:purchasing-one-step' => null,
					'int:max_popular_items' => null,
					'int:max_reindex_items' => null,
				]
			];

			$mode = (string) getRequest('param0');

			if ($mode == "do") {
				$params = $this->expectParams($params);
				$max_comp = &$params['emarket-options']['int:max_compare_items'];
				$max_comp = floor($max_comp);

				if (!$max_comp || $max_comp <= 1) {
					$config->set('modules', 'emarket.compare.max-items', 2);
					$this->module->errorNewMessage("%error-compare-wrong-data%");
					$this->module->errorPanic();
				}

				$max_popular = $params['emarket-options']['int:max_popular_items'];
				$max_popular = floor($max_popular);

				if (!$max_popular || $max_popular <= 1) {
					$config->set('modules', 'emarket.popular.max-items', 2);
					$this->module->errorNewMessage("%error-popular-wrong-data%");
					$this->module->errorPanic();
				}

				$max_reindex = $params['emarket-options']['int:max_reindex_items'];
				$max_reindex = floor($max_reindex);

				if (!$max_reindex || $max_reindex < 1) {
					$config->set('modules', 'emarket.reindex.max-items', 1);
					$this->module->errorNewMessage("%error-reindex-wrong-data%");
					$this->module->errorPanic();
				}

				$config->set('modules', 'emarket.compare.max-items', $params['emarket-options']['int:max_compare_items']);
				$config->set('modules', 'emarket.popular.max-items', $params['emarket-options']['int:max_popular_items']);
				$config->set('modules', 'emarket.reindex.max-items', $params['emarket-options']['int:max_reindex_items']);
				$umiRegistry->setVar('//modules/emarket/enable-discounts', $params['emarket-options']['boolean:discounts']);
				$umiRegistry->setVar('//modules/emarket/enable-currency', $params['emarket-options']['boolean:currency']);
				$umiRegistry->setVar('//modules/emarket/enable-stores', $params['emarket-options']['boolean:stores']);
				$umiRegistry->setVar('//modules/emarket/enable-payment', $params['emarket-options']['boolean:payment']);
				$umiRegistry->setVar('//modules/emarket/enable-delivery', $params['emarket-options']['boolean:delivery']);
				$umiRegistry->setVar('//modules/emarket/delivery-with-address', $params['emarket-options']['boolean:delivery-with-address']);
				$umiRegistry->setVar('//modules/emarket/purchasing-one-step', $params['emarket-options']['boolean:purchasing-one-step']);

				$this->switchGroupsActivity('order_delivery_props', $params['emarket-options']['boolean:delivery']);
				$this->switchGroupsActivity('order_discount_props', $params['emarket-options']['boolean:discounts']);
				$this->switchGroupsActivity('order_payment_props', $params['emarket-options']['boolean:payment']);
				$this->chooseRedirect();
			}

			$params['emarket-options']['int:max_compare_items'] = $config->get('modules', 'emarket.compare.max-items');
			$params['emarket-options']['int:max_popular_items'] = $config->get('modules', 'emarket.popular.max-items');
			$params['emarket-options']['int:max_reindex_items'] = $config->get('modules', 'emarket.reindex.max-items');
			$params['emarket-options']['boolean:discounts'] = $umiRegistry->getVal('//modules/emarket/enable-discounts');
			$params['emarket-options']['boolean:currency'] = $umiRegistry->getVal('//modules/emarket/enable-currency');
			$params['emarket-options']['boolean:stores'] = $umiRegistry->getVal('//modules/emarket/enable-stores');
			$params['emarket-options']['boolean:payment'] = $umiRegistry->getVal('//modules/emarket/enable-payment');
			$params['emarket-options']['boolean:delivery'] = $umiRegistry->getVal('//modules/emarket/enable-delivery');
			$params['emarket-options']['boolean:delivery-with-address'] = $umiRegistry->getVal('//modules/emarket/delivery-with-address');
			$params['emarket-options']['boolean:purchasing-one-step'] = $umiRegistry->getVal('//modules/emarket/purchasing-one-step');

			$this->setDataType('settings');
			$this->setActionType('modify');
			$data = $this->prepareData($params, 'settings');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки почтовых уведомлений.
		 * Если передан ключевой параметр $_REQUEST['param0'] = do,
		 * то сохраняет настройки и перенаправляет на страницу настроек.
		 * @throws coreException
		 */
		public function mail_config() {
			$umiRegistry = regedit::getInstance();
			$domains = domainsCollection::getInstance()->getList();
			$umiNotificationInstalled = cmsController::getInstance()
				->isModule('umiNotifications');

			$params = [
				'status-notifications' => [
					'boolean:no-order-status-notification' => $umiRegistry->getVal('//modules/emarket/no-order-status-notification'),
					'boolean:no-payment-status-notification' => $umiRegistry->getVal('//modules/emarket/no-payment-status-notification'),
					'boolean:no-delivery-status-notification' => $umiRegistry->getVal('//modules/emarket/no-delivery-status-notification')
				]
			];

			if ($umiNotificationInstalled) {
				$params['status-notifications']['boolean:use-umiNotifications'] = $umiRegistry->getVal('//modules/emarket/use-umiNotifications');
			}

			/**
			 * @var domain $domain
			 */
			foreach ($domains as $domain) {
				$domain_id = $domain->getId();
				$domain_name = $domain->getHost();

				$seo_info = [];
				$seo_info['status:domain'] = $domain_name;

				if (
					$domain->getIsDefault() &&
					!$umiRegistry->getVal("//modules/emarket/from-email/{$domain_id}") &&
					!$umiRegistry->getVal("//modules/emarket/from-name/{$domain_id}") &&
					!$umiRegistry->getVal("//modules/emarket/manager-email/{$domain_id}")
				) {
					$seo_info['string:email-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/from-email");
					$seo_info['string:name-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/from-name");
					$seo_info['string:manageremail-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/manager-email");
				} else {
					$seo_info['string:email-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/from-email/{$domain_id}");
					$seo_info['string:name-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/from-name/{$domain_id}");
					$seo_info['string:manageremail-' . $domain_id] = $umiRegistry->getVal("//modules/emarket/manager-email/{$domain_id}");
				}

				$params[$domain_name] = $seo_info;
			}

			$mode = (string) getRequest('param0');

			if ($mode == "do") {
				$params = $this->expectParams($params);

				foreach ($domains as $domain) {
					$domain_id = $domain->getId();
					$domain_name = $domain->getHost();

					$email = $params[$domain_name]['string:email-' . $domain_id];
					$name = $params[$domain_name]['string:name-' . $domain_id];
					$manageremail = $params[$domain_name]['string:manageremail-' . $domain_id];

					$umiRegistry->setVar("//modules/emarket/from-email/{$domain_id}", $email);
					$umiRegistry->setVar("//modules/emarket/from-name/{$domain_id}", $name);
					$umiRegistry->setVar("//modules/emarket/manager-email/{$domain_id}", $manageremail);
				}

				$umiRegistry->setVar(
					'//modules/emarket/no-order-status-notification',
					$params['status-notifications']['boolean:no-order-status-notification']
				);

				$umiRegistry->setVar(
					'//modules/emarket/no-payment-status-notification',
					$params['status-notifications']['boolean:no-payment-status-notification']
				);

				$umiRegistry->setVar(
					'//modules/emarket/no-delivery-status-notification',
					$params['status-notifications']['boolean:no-delivery-status-notification']
				);

				if ($umiNotificationInstalled) {
					$umiRegistry->setVar(
						'//modules/emarket/use-umiNotifications',
						$params['status-notifications']['boolean:use-umiNotifications']
					);
				}

				$this->chooseRedirect();
			}

			$this->setDataType('settings');
			$this->setActionType('modify');
			$data = $this->prepareData($params, 'settings');
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки интеграции с Яндекс.Маркет.
		 * Если передан ключевой параметр $_REQUEST['param0'] = do,
		 * то сохраняет настройки.
		 * @throws coreException
		 */
		public function yandex_market_config() {
			$umiRegistry = regedit::getInstance();
			$params = [];
			$domains = domainsCollection::getInstance()->getList();

			/**
			 * @var domain $domain
			 */
			foreach ($domains as $domain) {
				$domain_id = $domain->getId();
				$domain_name = $domain->getHost();
				$config = [];
				$config["string:clientId-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/clientId");
				$config["string:password-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/password");
				$config["string:token-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/token");
				$config["string:login-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/login");
				$config["string:marketToken-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/marketToken");
				$config["string:marketCampaignId-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/marketCampaignId");
				$config["boolean:cashOnDelivery-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/cashOnDelivery");
				$config["boolean:cardOnDelivery-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/cardOnDelivery");
				$config["boolean:shopPrepaid-{$domain_id}"] = $umiRegistry->getVal("//modules/emarket/yandex_market/{$domain_id}/shopPrepaid");
				$params[$domain_name] = $config;
			}

			$mode = (string) getRequest('param0');

			if ($mode == "do") {
				$params = $this->expectParams($params);

				foreach ($domains as $domain) {
					$domain_id = $domain->getId();
					$domain_name = $domain->getHost();

					$clientId = trim($params[$domain_name]["string:clientId-{$domain_id}"]);
					$password = trim($params[$domain_name]["string:password-{$domain_id}"]);
					$token = trim($params[$domain_name]["string:token-{$domain_id}"]);
					$login = trim($params[$domain_name]["string:login-{$domain_id}"]);
					$marketToken = trim($params[$domain_name]["string:marketToken-{$domain_id}"]);
					$marketCampaignId = trim($params[$domain_name]["string:marketCampaignId-{$domain_id}"]);
					$cashOnDelivery = trim($params[$domain_name]["boolean:cashOnDelivery-{$domain_id}"]);
					$cardOnDelivery = trim($params[$domain_name]["boolean:cardOnDelivery-{$domain_id}"]);
					$shopPrepaid = trim($params[$domain_name]["boolean:shopPrepaid-{$domain_id}"]);

					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/clientId", $clientId);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/password", $password);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/token", $token);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/login", $login);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/marketToken", $marketToken);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/marketCampaignId", $marketCampaignId);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/cashOnDelivery", $cashOnDelivery);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/cardOnDelivery", $cardOnDelivery);
					$umiRegistry->setVar("//modules/emarket/yandex_market/{$domain_id}/shopPrepaid", $shopPrepaid);

					if ($clientId && $password && $token && $login && $marketToken && $marketCampaignId && !$cashOnDelivery && !$cardOnDelivery && !$shopPrepaid) {
						$this->module->errorNewMessage(getLabel('error-yandex_market-no-payment-method', false, $domain_name));
					}
				}

				$this->chooseRedirect();
			}

			$this->setDataType('settings');
			$this->setActionType("modify");
			$data = $this->prepareData($params, "settings");
			$this->setData($data);
			$this->doData();
		}

		/**
		 * Возвращает настройки для формирования табличного контрола
		 * @param string $param контрольный параметр
		 * @return array
		 */
		public function getDatasetConfiguration($param = '') {
			switch ($param) {
				case 'discounts': {
					$loadMethod = 'discounts';
					$objectType = 'discount';
					break;
				}
				case 'orders': {
					$loadMethod = 'orders';
					$objectType = 'order';
					break;
				}
				case 'delivery': {
					$loadMethod = 'delivery';
					$objectType = 'delivery';
					break;
				}
				case 'payment': {
					$loadMethod = 'payment';
					$objectType = 'payment';
					break;
				}
				case 'stores': {
					$loadMethod = 'stores';
					$objectType = 'store';
					break;
				}
				case 'realpayments': {
					$loadMethod = 'realpayments';
					$objectType = 'order';
					break;
				}
				default: {
					$loadMethod = $objectType = $param;
				}
			}
			return [
				'methods' => [
					[
						'title' => getLabel('smc-load'),
						'forload' => true,
						'module' => 'emarket',
						'#__name' => $loadMethod
					],
					[
						'title' => getLabel('smc-delete'),
						'module' => 'emarket',
						'#__name' => 'del',
						'aliases' => 'tree_delete_element,delete,del'
					],
					[
						'title' => getLabel('smc-activity'),
						'module' => 'emarket',
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
					]
				],
				'types' => [
					[
						'common' => 'true',
						'id' => $objectType
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
					'rate_voters',
					'rate_sum',
					'total_count',
					'discount_rules_id',
					'discount_modificator_id',
					'delivery_address'
				],
				'default' => 'name[400px]|question[250px]'
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function getEntityFieldsConfig($entityClass, \iUmiConstantMap $entityFieldsConstantsMap) {
			/**
			 * @var emarket | UmiCms\Classes\Components\Emarket\Delivery\ApiShip\ModuleApi\Admin $module
			 */
			$module = $this->module;
			$tableControlConfig = $module->getApiShipOrderConfig();
			return $tableControlConfig['fields'];
		}

		/**
		 * Выводит в буффер настройки склада по умолчанию
		 */
		public function flushDefaultStoreAttributes() {
			/**
			 * @var EmarketSettings $settings
			 */
			$settings =  $this->module->getSettings();
			$settingKeyList = [
				'country-code',
				'region',
				'city',
				'street',
				'house-number',
				'apartment' ,
				'contact-full-name',
				'contact-phone',
				'contact-email'
			];

			$result = [];

			foreach ($settingKeyList as $settingKey) {
				$result[$settingKey] = $settings->get(EmarketSettings::DEFAULT_STORE_SECTION, $settingKey);
			}

			$this->module->printJson($result);
		}
	}
