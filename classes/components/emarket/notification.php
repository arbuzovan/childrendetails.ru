<?php

	/** Класс уведомлений об изменении статуса заказа, доставки или оплаты */
	class EmarketNotification {

		/** @var emarket $module */
		public $module;

		/**
		 * Запускает отправку уведомления об изменении статуса заказа, доставки или оплаты.
		 * Устанавливает флаг необходимости экспорта в 1С и дату изменения статуса заказа.
		 * @param order $order заказ
		 * @param string $changedProperty строковой идентификатор поля заказа, значение которого изменилось
		 * @throws selectorException
		 */
		public function notifyOrderStatusChange(order $order, $changedProperty) {
			$order->need_export = true;

			if ($changedProperty == "status_id") {
				$order->status_change_date = new umiDate();
			}

			if (order::getCodeByStatus($order->getPaymentStatus()) == "accepted" && !$order->delivery_allow_date) {
				$sel = new selector('objects');
				$sel->types('hierarchy-type')->name('emarket', 'delivery');
				$sel->option('no-length')->value(true);
				if ($sel->first) {
					$order->delivery_allow_date = new umiDate();
				}
			}

			$statusId = $order->getValue($changedProperty);
			$codeName = order::getCodeByStatus($statusId);

			if ($changedProperty == 'status_id' && (!$statusId || $codeName == 'payment')) {
				return;
			}

			$this->sendCustomerNotification($order, $changedProperty, $codeName);

			if ($changedProperty == 'status_id' && $codeName == 'waiting') {
				$this->sendManagerNotification($order);
				$this->sendManagerPushNotification($order);
			}

			$order->commit();
		}

		/**
		 * Отправляет информацию о заказе на сервер PUSH уведомлений
		 * @param order $order заказ
		 * @throws Exception
		 * @throws selectorException
		 * @throws coreException
		 * @throws umiRemoteFileGetterException
		 */
		public function sendManagerPushNotification(order $order) {
			$currentDomain = cmsController::getInstance()->getCurrentDomain();

			if (!$currentDomain instanceof domain) {
				throw new coreException('Cannot detect current domain');
			}

			$currentLanguage = cmsController::getInstance()->getCurrentLang();

			if (!$currentLanguage instanceof lang) {
				throw new coreException('Cannot detect current language');
			}

			$selector = new selector("objects");
			$selector->types('object-type')->id('emarket-mobile-devices');
			$selector->where('domain_id')->equals($currentDomain->getId());
			$selector->where('active')->equals(1);
			$selector->option('no-length')->value(true);
			$selector->option('load-all-props')->value(true);

			if (!$selector->first) {
				return;
			}

			$tokens = [];
			/**
			 * @var iUmiObject $deviceObject
			 */
			foreach ($selector->result() as $deviceObject) {
				$token = $deviceObject->getValue('token');
				if (!$token) {
					continue;
				}
				$tokens[] = $token;
			}

			if (count($tokens) == 0) {
				return;
			}

			$request = $this->getPushSendingParams($currentDomain, $currentLanguage, $tokens, $order);
			umiRemoteFileGetter::get(PUSH_SERVER, false, false, $request, false, 'POST', 10);
		}

		/**
		 * Отправляет почтовое уведомление об изменении статуса заказа, доставки
		 * или оплаты покупателю, оформившему заказ.
		 * @param order $order заказ
		 * @param string $changedStatus строковой идентификатор поля заказа, которое было изменено
		 * @param string $codeName код нового статуса
		 * @return bool было ли письмо отправлено
		 */
		public function sendCustomerNotification(order $order, $changedStatus, $codeName) {
			$status = $this->getStatus($order, $changedStatus, $codeName);

			if (!$status) {
				return false;
			}

			$umiObjects = umiObjectsCollection::getInstance();

			$customer = $umiObjects->getObject($order->getCustomerId());
			$currency = $this->module->getCurrentCurrency();
			$suffix = $currency->getValue('suffix');
			$items = $this->getOrderItems($order, $suffix);

			$variables = [
				'order_id' => $order->id,
				'order_name' => $order->name,
				'order_number' => $order->number,
				'status' => $status,
				'personal_params' => $this->module->getPersonalLinkParams($customer->getId()),
				'total_amount' => $order->getTotalAmount(),
				'total_price' => $order->getActualPrice(),
				'suffix' => $suffix,
				'+items' => $items,
			];

			$domain = cmsController::getInstance()->getCurrentDomain();
			$variables["domain"] = ($domain instanceof domain) ? $domain->getCurrentHostName() : getServer('HTTP_HOST');
			$paymentObject = $umiObjects->getObject($order->payment_id);
			$paymentClassName = null;

			if ($paymentObject instanceof iUmiObject) {
				$paymentType = $umiObjects->getObject($paymentObject->getValue('payment_type_id'));
				$paymentClassName = ($paymentType instanceof iUmiObject) ? $paymentType->getValue('class_name') : null;
			}

			if ($paymentClassName == "receipt") {
				$variables["receipt_signature"] = sha1(
					"{$customer->getId()}:{$customer->getValue('email')}:{$order->getValue('order_date')}"
				);
			}

			$name =
				$customer->getValue('lname') . ' ' .
				$customer->getValue('fname') . ' ' .
				$customer->getValue('father_name');
			$email = $this->getCustomerEmail($order, $customer);

			if (!umiMail::checkEmail($email)) {
				return false;
			}

			$sender = $this->getSenderInfo($domain);
			$isReceiptPayment = ($paymentClassName == "receipt");
			$header = $this->getSubject($changedStatus, $codeName);

			$subject = null;
			$content = null;

			if ($this->module->isUsingUmiNotifications()) {
				$mailNotifications = UmiCms\Service::MailNotifications();
				$notification = $mailNotifications->getCurrentByName('notification-emarket-status-change');

				if ($notification instanceof MailNotification) {
					$subjectTemplate = $notification->getTemplateByName('emarket-status-notification-subject');

					if ($isReceiptPayment) {
						$contentTemplate = $notification->getTemplateByName('emarket-status-notification-receipt');
					} else {
						$contentTemplate = $notification->getTemplateByName('emarket-status-notification-content');
					}

					if ($subjectTemplate instanceof MailTemplate) {
						$subject = $subjectTemplate->getProcessedContent(['header' => $header]);
					}

					if ($contentTemplate instanceof MailTemplate) {
						$content = $contentTemplate->getProcessedContent($variables);
					}
				}
			} else {
				try {
					$templateName = ($isReceiptPayment) ? "status_notification_receipt" : "status_notification";
					list($contentTemplate) = emarket::loadTemplatesForMail(
						'emarket/mail/default', $templateName
					);
					$subject = $header;
					$content = emarket::parseTemplateForMail($contentTemplate, $variables);
				} catch (Exception $e) {
					// nothing
				}
			}

			if ($subject === null || $content === null) {
				return false;
			}

			$mail = new umiMail();
			$mail->addRecipient($email, $name);
			$mail->setFrom($sender['email'], $sender['name']);
			$mail->setSubject($subject);
			$mail->setContent($content);
			$mail->commit();
			$mail->send();

			return true;
		}

		/**
		 * Определяет данные товарных наименований
		 * для уведомления об изменении статуса заказа
		 * @see $this->sendCustomerNotification()
		 * @param order $order
		 * @param $suffix
		 * @return array
		 */
		protected function getOrderItems(order $order, $suffix) {
			$umiHierarchy = umiHierarchy::getInstance();
			$items = [];

			foreach ($order->getItems() as $item) {
				$productPage = $item->getItemElement();
				$link = '';

				if ($productPage instanceof iUmiHierarchyElement) {
					$link = $umiHierarchy->getPathById($productPage->getId());
				}

				$items[] = [
					'link' => $link,
					'name' => $item->getName(),
					'price' => $item->getTotalActualPrice(),
					'amount' => $item->getAmount(),
					'suffix' => $suffix,
				];
			}

			return $items;
		}

		/**
		 * Определяет заголовок уведомления об изменении статуса заказа
		 * @see $this->sendCustomerNotification()
		 * @param $changedStatus
		 * @param $codeName
		 * @return mixed
		 */
		protected function getSubject($changedStatus, $codeName) {
			$labels = cmsController::getInstance()->langs;
			$subject = $labels['notification-status-subject'];

			if ($changedStatus == 'status_id' && $codeName == 'waiting') {
				$subject = $labels['notification-client-neworder-subject'];
			}

			return $subject;
		}

		/**
		 * Определяет статус заказа для уведомления об изменении статуса заказа
		 * @see $this->sendCustomerNotification()
		 * @param order $order
		 * @param $changedStatus
		 * @param $codeName
		 * @return mixed
		 */
		protected function getStatus(order $order, $changedStatus, $codeName) {
			$umiRegistry = regedit::getInstance();
			$labels = cmsController::getInstance()->langs;
			$status = null;

			switch ($changedStatus) {
				case 'status_id': {
					if ($umiRegistry->getVal('//modules/emarket/no-order-status-notification')) {
						return;
					}

					if ($codeName == 'waiting') {
						$paymentStatusCodeName = order::getCodeByStatus($order->getPaymentStatus());

						$paymentStatusKey = "notification-status-payment-{$paymentStatusCodeName}";
						$paymentStatus = isset($labels[$paymentStatusKey]) ? ($labels[$paymentStatusKey]) : '';

						$orderStatusKey = "notification-status-{$codeName}";
						$orderStatus = isset($labels[$orderStatusKey]) ? ($labels[$orderStatusKey]) : '';

						if ($paymentStatusCodeName == 'initialized') {
							$left = $orderStatus;
							$right = $paymentStatus;
						} else {
							$left = $paymentStatus;
							$right = $orderStatus;
						}

						$middle = ($left && $right) ? " {$labels['notification-and']} " : '';
						$status = $left . $middle . $right;
					} else {
						$key = "notification-status-{$codeName}";
						$status = isset($labels[$key]) ? $labels[$key] : "_";
					}

					break;
				}

				case 'payment_status_id': {
					if ($umiRegistry->getVal('//modules/emarket/no-payment-status-notification')) {
						return;
					}

					$key = "notification-status-payment-{$codeName}";
					$status = isset($labels[$key]) ? $labels[$key] : "_";
					break;
				}

				case 'delivery_status_id': {
					if ($umiRegistry->getVal('//modules/emarket/no-delivery-status-notification')) {
						return;
					}

					$key = "notification-status-delivery-{$codeName}";
					$status = isset($labels[$key]) ? $labels[$key] : "_";
					break;
				}
			}

			return $status;
		}

		/**
		 * Определяет email покупателя для уведомления об изменении статуса заказа
		 * @see $this->sendCustomerNotification()
		 * @param order $order
		 * @param iUmiObject $customer
		 * @return bool|mixed
		 */
		protected function getCustomerEmail(order $order, iUmiObject $customer) {
			$umiObjects = umiObjectsCollection::getInstance();

			$buyerOneClick = $umiObjects->getObject($order->getValue('purchaser_one_click'));
			$emailOneClick = false;

			if ($buyerOneClick instanceof umiObject) {
				$emailOneClick = $buyerOneClick->getValue('email') ? $buyerOneClick->getValue('email') : $buyerOneClick->getValue("e-mail");
			}

			if ($emailOneClick) {
				return $emailOneClick;
			} else {
				return $customer->getValue('email') ? $customer->getValue('email') : $customer->getValue("e-mail");
			}
		}

		/**
		 * Определяет email/имя отправителя для уведомления об изменении статуса заказа
		 * @see $this->sendCustomerNotification()
		 * @param $domain
		 * @return array
		 */
		protected function getSenderInfo($domain) {
			$umiRegistry = regedit::getInstance();
			$domains = domainsCollection::getInstance();
			$domainId = ($domain instanceof domain) ? $domain->getId() : null;
			$defaultDomain = $domains->getDefaultDomain();
			$defaultDomainId = ($defaultDomain instanceof domain) ? $defaultDomain->getId() : null;

			if ($umiRegistry->getVal("//modules/emarket/from-email/{$domainId}")) {
				$email = $umiRegistry->getVal("//modules/emarket/from-email/{$domainId}");
				$name = $umiRegistry->getVal("//modules/emarket/from-name/{$domainId}");
			} elseif ($umiRegistry->getVal("//modules/emarket/from-email/{$defaultDomainId}")) {
				$email = $umiRegistry->getVal("//modules/emarket/from-email/{$defaultDomainId}");
				$name = $umiRegistry->getVal("//modules/emarket/from-name/{$defaultDomainId}");
			} else {
				$email = $umiRegistry->getVal("//modules/emarket/from-email");
				$name = $umiRegistry->getVal("//modules/emarket/from-name");
			}

			return [
				'email' => $email,
				'name' => $name
			];
		}

		/**
		 * Отправляет почтовое уведомление об изменении статуса заказа, доставки
		 * или оплаты менеджеру магазина
		 * @param order $order изменившийся заказа
		 * @return bool
		 */
		public function sendManagerNotification(order $order) {
			$umiRegistry = regedit::getInstance();
			$cmsController = cmsController::getInstance();
			$umiDomains = domainsCollection::getInstance();
			$umiObjects = umiObjectsCollection::getInstance();

			$currentDomain = $cmsController->getCurrentDomain();
			$defaultDomain = $umiDomains->getDefaultDomain();
			$currentDomainId = ($currentDomain instanceof domain) ? $currentDomain->getId() : null;
			$defaultDomainId = ($defaultDomain instanceof domain) ? $defaultDomain->getId() : null;

			if ($umiRegistry->getVal("//modules/emarket/manager-email/{$currentDomainId}")) {
				$emails = $umiRegistry->getVal("//modules/emarket/manager-email/{$currentDomainId}");
				$fromMail = $umiRegistry->getVal("//modules/emarket/from-email/{$currentDomainId}");
				$fromName = $umiRegistry->getVal("//modules/emarket/from-name/{$currentDomainId}");
			} elseif ($umiRegistry->getVal("//modules/emarket/manager-email/{$defaultDomainId}")) {
				$emails = $umiRegistry->getVal("//modules/emarket/manager-email/{$defaultDomainId}");
				$fromMail = $umiRegistry->getVal("//modules/emarket/from-email/{$defaultDomainId}");
				$fromName = $umiRegistry->getVal("//modules/emarket/from-name/{$defaultDomainId}");
			} else {
				$emails = $umiRegistry->getVal('//modules/emarket/manager-email');
				$fromMail = $umiRegistry->getVal("//modules/emarket/from-email");
				$fromName = $umiRegistry->getVal("//modules/emarket/from-name");
			}

			$mail = new umiMail();
			$recipientsCount = 0;

			foreach (explode(',', $emails) as $recipient) {
				$recipient = trim($recipient);

				if (strlen($recipient) && umiMail::checkEmail($recipient)) {
					$mail->addRecipient($recipient);
					$recipientsCount++;
				}
			}

			if ($recipientsCount == 0) {
				return false;
			}

			try {
				/** @var payment $payment */
				$payment = payment::get($order->payment_id, $order);
				$paymentName = $payment ? $payment->name : '';
				$paymentStatus = order::getCodeByStatus($order->getPaymentStatus());
			} catch (coreException $e) {
				$paymentName = "";
				$paymentStatus = "";
			}

			$customer = $umiObjects->getObject($order->getCustomerId());
			$currency = $this->module->getCurrentCurrency();
			$suffix = $currency->getValue('suffix');
			$items = $this->getOrderItems($order, $suffix);
			$delivery = $this->getDeliveryName($order);
			$address = $this->getDeliveryAddress($order);

			$variables = [
				'order_id' => $order->id,
				'order_name' => $order->name,
				'order_number' => $order->number,
				'payment_type' => $paymentName,
				'payment_status' => $paymentStatus,
				'price' => $order->getActualPrice(),
				'total_price' => $order->getActualPrice(),
				'total_amount' => $order->getTotalAmount(),
				'domain' => ($currentDomain instanceof domain) ? $currentDomain->getCurrentHostName() : getServer('HTTP_HOST'),
				'suffix' => $suffix,
				'+items' => $items,
				'first_name' => $customer->getValue('fname'),
				'last_name' => $customer->getValue('lname'),
				'email' => $this->getCustomerEmail($order, $customer),
				'phone' => $customer->getValue('phone'),
				'delivery' => $delivery,
				'address' => $address,
			];

			$labels = $cmsController->langs;
			$header = $labels['notification-neworder-subject'] . " (#{$order->number})";

			$subject = null;
			$content = null;

			if ($this->module->isUsingUmiNotifications()) {
				$mailNotifications = UmiCms\Service::MailNotifications();
				$notification = $mailNotifications->getCurrentByName('notification-emarket-new-order');

				if ($notification instanceof MailNotification) {
					$subjectTemplate = $notification->getTemplateByName('emarket-neworder-notification-subject');
					$contentTemplate = $notification->getTemplateByName('emarket-neworder-notification-content');

					if ($subjectTemplate instanceof MailTemplate) {
						$subject = $subjectTemplate->getProcessedContent(['header' => $header]);
					}

					if ($contentTemplate instanceof MailTemplate) {
						$content = $contentTemplate->getProcessedContent($variables);
					}
				}
			} else {
				try {
					list($contentTemplate) = emarket::loadTemplatesForMail(
						"emarket/mail/default",
						"neworder_notification"
					);
					$subject = $header;
					$content = emarket::parseTemplateForMail($contentTemplate, $variables);
				} catch (Exception $e) {
					// nothing
				}
			}

			if ($subject === null || $content === null) {
				return false;
			}

			$mail->setFrom($fromMail, $fromName);
			$mail->setSubject($subject);
			$mail->setContent($content);
			$mail->commit();
			$mail->send();

			return true;
		}

		/**
		 * Определяет название доставки для уведомления об изменении статуса заказа
		 * @see $this->sendManagerNotification()
		 * @param order $order
		 * @return string
		 */
		protected function getDeliveryName(order $order) {
			$umiObjects = umiObjectsCollection::getInstance();
			$delivery = $umiObjects->getObject($order->delivery_id);

			if ($delivery instanceof iUmiObject) {
				return $delivery->getName();
			}

			return '';
		}

		/**
		 * Определяет адрес доставки для уведомления об изменении статуса заказа
		 * @see $this->sendManagerNotification()
		 * @param order $order
		 * @return string
		 */
		protected function getDeliveryAddress(order $order) {
			$umiObjects = umiObjectsCollection::getInstance();
			$address = $umiObjects->getObject($order->getValue('delivery_address'));

			if ($address instanceof iUmiObject) {
				$params = [
					$address->getValue('index'),
					$address->getValue('region'),
					$address->getValue('city'),
					$address->getValue('street'),
					$address->getValue('house'),
					$address->getValue('flat'),
				];

				return implode(', ', $params);
			}

			return '';
		}

		/**
		 * Возвращает параметры запроса для отправки PUSH-уведомления
		 * @param domain $domain домен, на котором был оформлен заказ
		 * @param lang $language языковая версия, на которой был оформлен заказ
		 * @param array $tokens список токенов устройств
		 * @param order $order оформленный заказ
		 * @return array список параметров
		 */
		protected function getPushSendingParams($domain, $language, $tokens, $order) {
			$punyConverter = new idna_convert();

			return [
				'requestType' => 'sendPushMessage',
				'domain' => $punyConverter->decode($domain->getHost()),
				'lang_prefix' => $language->getPrefix(),
				'tokens' => $tokens,
				'orderId' => (string) $order->getId(),
				'orderNumber' => (string) $order->getValue('number'),
				'orderStatusId' => (string) $order->getValue('status_id')
			];
		}

	}
