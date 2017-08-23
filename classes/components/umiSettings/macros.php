<?php
	/**
	 * Класс макросов, то есть методов, доступных в шаблоне
	 */
	class UmiSettingsMacros implements iModulePart {

		use tModulePart;

		/**
		 * Возвращает идентификатор настроек
		 * @param string $name название настроек
		 * @param bool|int $domainId идентификатор домена, к которому относятся настройки
		 * @param bool|int $languageId идентификатор языка, к которому относятся настройки
		 * @return int
		 * @throws publicException
		 */
		public function getId($name, $domainId = false, $languageId = false) {
			if (!is_string($name) || empty($name)) {
				$message = getLabel('label-error-wrong-setting-arguments', $this->getModuleName());
				throw new publicException($message);
			}

			$cmsController = cmsController::getInstance();

			if (!is_numeric($domainId)) {
				$domainId = $cmsController->getCurrentDomain()
					->getId();
			}

			if (!is_numeric($languageId)) {
				$languageId = $cmsController->getCurrentLang()
					->getId();
			}

			$query = new selector('objects');
			$query->types('object-type')->guid(umiSettings::ROOT_TYPE_GUID);
			$query->where('name')->equals($name);
			$query->where('domain_id')->equals($domainId);
			$query->where('lang_id')->equals($languageId);
			$query->option('return')->value('id');
			$query->limit(0, 1);

			if ($query->length() == 0) {
				$messageFormat = getLabel('label-error-settings-not-found', $this->getModuleName());
				$message = sprintf($messageFormat, $name);
				throw new publicException($message);
			}

			$result = $query->result();
			$settingsData = array_shift($result);
			return (int) $settingsData['id'];
		}
	}