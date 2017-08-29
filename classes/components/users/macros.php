<?php

	/**
	 * Класс макросов, то есть методов, доступных в шаблоне
	 */
	class UsersMacros {
		/**
		 * @var users $module
		 */
		public $module;

		/** Адрес API ulogin */
		const ULOGIN_URL = 'http://ulogin.ru/token.php?';

		/**
		 * Выводит форму авторизации
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function login($template = "default") {
			if (!$template) {
				$template = "default";
			}

			$from_page = getRequest('from_page');

			if (!$from_page) {
				$from_page = getServer('REQUEST_URI');
			}

			if (isDemoMode()) {
				list($template_login) = users::loadTemplates("users/" . $template, "login_demo");
			} else {
				list($template_login) = users::loadTemplates("users/" . $template, "login");
			}

			$block_arr = [];
			$block_arr['from_page'] = users::protectStringVariable($from_page);

			return users::parseTemplate($template_login, $block_arr);
		}

		/**
		 * Выводит форму авторизации для пользователя либо информацию об авторизованном пользователе
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed|void
		 */
		public function auth($template = "default") {
			if (!$template) {
				$template = "default";
			}

			if ($this->module->is_auth()) {
				if (cmsController::getInstance()->getCurrentMode() == "admin") {
					$this->module->redirect($this->module->pre_lang . "/admin/");
				} else {
					list($template_logged) = users::loadTemplates("users/" . $template, "logged");

					$block_arr = [];
					$block_arr['xlink:href'] = "uobject://" . $this->module->user_id;
					$block_arr['user_id'] = $this->module->user_id;
					$block_arr['user_name'] = $this->module->user_fullname;
					$block_arr['user_login'] = $this->module->user_login;

					return users::parseTemplate($template_logged, $block_arr, false, $this->module->user_id);
				}
			}

			return $this->login($template);
		}

		/**
		 * Авторизует пользователя
		 * @return mixed|string
		 * @throws publicAdminException если авторизация не удалась через административную панель
		 */
		public function login_do() {
			$res = "";
			$login = getRequest('login');
			$rawPassword = getRequest('password');
			$from_page = getRequest('from_page');

			if (strlen($login) == 0) {
				return $this->auth();
			}

			$cmsController = cmsController::getInstance();
			$auth = UmiCms\Service::Auth();
			$userId = $auth->checkLogin($login, $rawPassword);
			$user = umiObjectsCollection::getInstance()->getObject($userId);

			/* @var iUmiObject|iUmiEntinty $user */
			if ($user instanceof iUmiObject) {
				if (\UmiCms\Service::Session()->get('fake-user') == 1) {
					return ($this->module->restoreUser(true)) ? $this->auth() : $res;
				}

				$hashedPassword = $user->getValue('password');
				$hashAlgorithm = UmiCms\Service::PasswordHashAlgorithm();

				if ($hashAlgorithm->isHashedWithMd5($hashedPassword, $rawPassword)) {
					$hashedPassword = $hashAlgorithm->hash($rawPassword, $hashAlgorithm::SHA256);
					$user->setValue('password', $hashedPassword);
					$user->commit();
				}

				$auth->loginUsingId($user->getId());

				$oEventPoint = new umiEventPoint("users_login_successfull");
				$oEventPoint->setParam("user_id", $user->id);
				users::setEventPoint($oEventPoint);
				$module = $this->module;

				if ($cmsController->getCurrentMode() == "admin") {
					ulangStream::getLangPrefix();
					system_get_skinName();
					/* @var UsersAdmin|users $module */
					$module->chooseRedirect($from_page);
				} else {
					/* @var UsersMacros|users $module */
					if (!$from_page) {
						$from_page = getServer('HTTP_REFERER');
					}

					$module->redirect($from_page ? $from_page : ($module->pre_lang . '/users/auth/'));
				}
			} else {
				$oEventPoint = new umiEventPoint("users_login_failed");
				$oEventPoint->setParam("login", $login);
				$oEventPoint->setParam("password", $rawPassword);
				users::setEventPoint($oEventPoint);

				if ($cmsController->getCurrentMode() == "admin") {
					throw new publicAdminException(getLabel('label-text-error'));
				}

				/**
				 * @var users|UsersMacros $this
				 */
				return $this->auth();
			}

			return $res;
		}

		/**
		 * Регистрирует пользователя с помощью Loginza
		 * @return mixed
		 * @throws coreException
		 * @throws selectorException
		 */
		public function loginza() {
			/* @var users|UsersMacros $this */
			if (empty($_POST['token'])) {
				return $this->auth();
			}

			$loginzaAPI = new loginzaAPI();
			$profile = $loginzaAPI->getAuthInfo($_POST['token']);

			if (empty($profile)) {
				return $this->auth();
			}

			$profile = new loginzaUserProfile($profile);

			$nickname = $profile->genNickname();
			$provider = $profile->genProvider();
			$provider_url = parse_url($provider);
			$provider_name = str_ireplace('www.', '', $provider_url['host']);
			$login = $nickname . "@" . $provider_name;
			$password = $profile->genRandomPassword();
			$hashAlgorithm =  UmiCms\Service::PasswordHashAlgorithm();
			$encodedPassword = $hashAlgorithm->hash($password);
			$email = $profile->genUserEmail();
			$lname = $profile->getLname();
			$fname = $profile->getFname();

			if (!$fname) {
				$fname = $nickname;
			}

			$this->tryToLoginAsExistingSocialUser($login, $provider_name);

			if (!preg_match("/.+@.+\..+/", $email)) {
				while (true) {
					$email = $nickname . rand(1, 100) . "@" . getServer('HTTP_HOST');
					if ($this->module->checkIsUniqueEmail($email)) {
						break;
					}
				}
			}

			$this->createNewSocialUserAndLogin([
				'login' => $login,
				'password' => $encodedPassword,
				'email' => $email,
				'firstName' => $fname,
				'lastName' => $lname,
				'network' => $provider_name
			]);
		}

		/**
		 * Регистрирует пользователя с помощью сервиса ulogin.ru
		 *
		 * В ответе от uLogin обязательно должен прийти параметры:
		 * 'network'
		 * 'first_name'
		 * 'nickname'
		 * 'email'
		 *
		 * @link http://ulogin.ru/help.php#fields описание параметров
		 * @return mixed
		 * @throws coreException
		 * @throws selectorException
		 */
		public function ulogin() {
			if (empty($_POST['token'])) {
				return $this->auth();
			}

			$params = [
				'token' => $_POST['token'],
				'host' => $_SERVER['HTTP_HOST']
			];

			$response = umiRemoteFileGetter::get(self::ULOGIN_URL . http_build_query($params));
			$data = json_decode($response);

			if (empty($data->network) || empty($data->first_name) ||
				empty($data->nickname) || empty($data->email)
			) {
				return $this->auth();
			}

			$network = $data->network;
			$login = $data->nickname . '@' . $network;

			try {
				$this->tryToLoginAsExistingSocialUser($login, $network);
			} catch (AuthenticationException $e) {
				// Пользователь не найден, нужно создать нового
			}

			$password = $this->module->getRandomPassword();
			$encodedPassword = UmiCms\Service::PasswordHashAlgorithm()->hash($password);
			$firstName = $data->first_name;
			$lastName = (isset($data->last_name) ? $data->last_name : '');
			$candidateEmail = $data->email;

			$email = $candidateEmail;
			$prefix = 1;

			while (!$this->module->checkIsUniqueEmail($email)) {
				$email = $prefix . $candidateEmail;
				$prefix += 1;
			}

			$this->createNewSocialUserAndLogin([
				'login' => $login,
				'password' => $encodedPassword,
				'email' => $email,
				'firstName' => $firstName,
				'lastName' => $lastName,
				'network' => $network
			]);
		}

		protected function tryToLoginAsExistingSocialUser($login, $network) {
			$result = UmiCms\Service::Auth()->loginBySocials($login, $network);

			if ($result) {
				$fromPage = getRequest('from_page');
				$redirectTarget = $fromPage ?: ($this->module->pre_lang . '/users/settings/');
				$this->module->redirect($redirectTarget);
			}
		}

		protected function createNewSocialUserAndLogin(array $data) {
			$umiObjects = umiObjectsCollection::getInstance();

			$userType = selector::get('object-type')->name('users', 'user');
			$userId = $umiObjects->addObject($data['login'], $userType->getId());
			$user = $umiObjects->getObject($userId);

			$user->setValue("login", $data['login']);
			$user->setValue("password", $data['password']);
			$user->setValue("e-mail", $data['email']);
			$user->setValue("fname", $data['firstName']);
			$user->setValue("lname", $data['lastName']);
			$user->setValue('loginza', $data['network']);
			$user->setValue("register_date", time());
			$user->setValue("is_activated", '1');
			$user->setValue("activate_code", md5(uniqid(rand(), true)));

			UmiCms\Service::Auth()->loginUsingId($userId);

			$groupId = regedit::getInstance()->getVal("//modules/users/def_group");
			$user->setValue("groups", [$groupId]);

			/** @var data|DataForms $dataModule */
			$dataModule = cmsController::getInstance()->getModule('data');
			$dataModule->saveEditedObject($userId, true);
			$user->commit();

			$fromPage = getRequest("from_page");
			$this->module->redirect($fromPage ?: ($this->module->pre_lang . '/users/settings/'));
		}

		/**
		 * Выводит информацию об авторизованном пользователе
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return string
		 */
		public function welcome($template = "default") {
			if (!$template) {
				$template = "default";
			}

			if ($this->module->is_auth()) {
				/** @noinspection PhpUndefinedMethodInspection */
				return $this->module->auth($template);
			} else {
				return "";
			}
		}

		/**
		 * Выводит форму изменения настроек пользователя.
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function settings($template = "default") {
			if (!$template) {
				$template = "default";
			}

			list($template_block) = users::loadTemplates("users/register/" . $template, "settings_block");

			$block_arr = [];
			$block_arr['xlink:href'] = "udata://data/getEditForm/" . $this->module->user_id;
			$block_arr['user_id'] = $this->module->user_id;

			return users::parseTemplate($template_block, $block_arr, false, $this->module->user_id);
		}

		/**
		 * Выводит форму регистрации пользователя на сайте.
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function registrate($template = "default") {
			if (!$template) {
				$template = "default";
			}

			if ($this->module->is_auth()) {
				$this->module->redirect($this->module->pre_lang . "/users/settings/");
			}

			$objectTypeId = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName("users", "user");

			list($template_block) = users::loadTemplates("users/register/" . $template, "registrate_block");

			$block_arr = [];
			$block_arr['xlink:href'] = "udata://data/getCreateForm/" . $objectTypeId;
			$block_arr['type_id'] = $objectTypeId;

			return users::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Выводит результат регистрации
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function registrate_done($template = "default") {
			if (!$template) {
				$template = "default";
			}

			$suffix = '';
			switch (getRequest('result')) {
				case 'without_activation':
					$suffix = '_without_activation';
					break;
				case 'error'             :
					$suffix = '_error';
					break;
				case 'error_user_exists' :
					$suffix = '_user_exists';
					break;
			}

			list($template_block) = users::loadTemplates("users/register/" . $template, "registrate_done_block" . $suffix);
			$block_arr = [
				'result' => getRequest('result')
			];

			return users::parseTemplate($template_block, $block_arr);
		}

		/**
		 * Возвращает результат активации
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param bool $isSuccessful удачно ли прошла активация
		 * @return mixed
		 */
		public function getActivateResult($template = "default", $isSuccessful = true) {
			if (!$template) {
				$template = "default";
			}

			list($template_block, $template_block_failed) = users::loadTemplates(
				"users/register/" . $template,
				"activate_block",
				"activate_block_failed"
			);

			$resultTemplate = ($isSuccessful) ? $template_block : $template_block_failed;

			$result = [
				'attribute:status' => ($isSuccessful) ? "success" : "fail"
			];

			return users::parseTemplate($resultTemplate, $result);
		}

		/**
		 * Выводит профиль пользователя.
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param bool $user_id идентификатор пользователя
		 * @return mixed
		 * @throws coreException
		 * @throws publicException
		 */
		public function profile($template = "default", $user_id = false) {
			if (!$template) {
				$template = "default";
			}

			list($template_block, $template_bad_user_block) = users::loadTemplates(
				"users/profile/" . $template,
				"profile_block",
				"bad_user_block"
			);
			$block_arr = [];

			if (!$user_id) {
				$user_id = (int) getRequest('param0');
			}

			if (!$user_id) {
				$permissions = permissionsCollection::getInstance();
				if ($permissions->isAuth()) {
					$auth = UmiCms\Service::Auth();
					$user_id = $auth->getUserId();
				}
			}

			/**
			 * @var iUmiObject $user
			 */
			if ($user = selector::get('object')->id($user_id)) {
				$this->module->validateEntityByTypes($user, ['module' => 'users', 'method' => 'user']);
				$block_arr['xlink:href'] = "uobject://" . $user_id;
				$userTypeId = $user->getTypeId();

				if ($userType = umiObjectTypesCollection::getInstance()->getType($userTypeId)) {
					$userHierarchyTypeId = $userType->getHierarchyTypeId();
					if ($userHierarchyType = umiHierarchyTypesCollection::getInstance()->getType($userHierarchyTypeId)) {
						if ($userHierarchyType->getName() == "users" && $userHierarchyType->getExt() == "user") {
							$block_arr['id'] = $user_id;
							return users::parseTemplate($template_block, $block_arr, false, $user_id);
						}
					}
				}
			} else {
				throw new publicException(getLabel('error-object-does-not-exist', null, $user_id));
			}

			return users::parseTemplate($template_bad_user_block, $block_arr);
		}

		/**
		 * Выводит список зарегистрированных и активированных пользователей
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param int $perPage количество выводимых пользователей на одну страницу
		 * @return mixed
		 * @throws coreException
		 */
		public function list_users($template = "default", $perPage = 10) {
			list($templateBlock, $templateBlockItem) = users::loadTemplates(
				"users/list_users/" . $template,
				"block",
				"block_item"
			);

			$blockArr = [];
			$currPage = (int) getRequest('p');

			$sel = new selector('objects');
			$sel->types('object-type')->name('users', 'user');
			$sel->where('is_activated')->equals(true);
			$sel->option('return')->value('id');
			$sel->limit($currPage, $perPage);
			selectorHelper::detectFilters($sel);

			$result = $sel->result();
			$total = $sel->length();

			$items = [];

			foreach ($result as $info) {
				$userId = $info['id'];
				$itemArr = [];
				$itemArr['void:user_id'] = $userId;
				$itemArr['attribute:id'] = $userId;
				$itemArr['xlink:href'] = "uobject://" . $userId;
				$items[] = users::parseTemplate($templateBlockItem, $itemArr, false, $userId);
			}

			$blockArr['subnodes:items'] = $items;
			$blockArr['per_page'] = $perPage;
			$blockArr['total'] = $total;

			return users::parseTemplate($templateBlock, $blockArr);
		}

		/**
		 * Выводит общее количество зарегистрированных и активированных пользователей.
		 * @return Int
		 * @throws coreException
		 */
		public function count_users() {
			$sel = new selector('objects');
			$sel->types('hierarchy-type')->name('users', 'user');
			$sel->where('is_activated')->equals(true);
			$sel->option('return')->value('count');
			return $sel->result();
		}

		/**
		 * Выводит форму восстановления пароля
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function forget($template = "default") {
			list($template_block) = def_module::loadTemplates("users/forget/" . $template, "forget_block");
			return users::parseTemplate($template_block, []);
		}

		/**
		 * Возвращает результат отправки письма для восстановления пароля
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param bool $isCorrect была отправка успешной
		 * @param string|bool $login некорректный логин, который был введен
		 * @param string|bool $email некорректный почтовый ящик, который был введен
		 * @return mixed
		 */
		public function getForgetResult($template = "default", $isCorrect = true, $login = false, $email = false) {
			list($template_wrong_login_block, $template_forget_sended) = users::loadTemplates(
				"users/forget/" . $template,
				"wrong_login_block",
				"forget_sended"
			);
			$block_arr = [];
			$template = null;

			if ($isCorrect) {
				$template = $template_forget_sended;
				$block_arr['attribute:status'] = "success";
			} else {
				$template = $template_wrong_login_block;
				$block_arr['attribute:status'] = "fail";
				$block_arr['forget_login'] = $login;
				$block_arr['forget_email'] = $email;
			}

			return users::parseTemplate($template, $block_arr);
		}

		/**
		 * Возвращает результат восстановления пароля
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @param bool $isCorrect было ли восстановление успешным
		 * @param bool $login корректный логин, восстановленного пользователя
		 * @param bool $password корректный пароль, восстановленного пользователя
		 * @param bool $userId корректный идентификатор, восстановленного пользователя
		 * @return mixed
		 */
		public function getRestoreResult($template = "default", $isCorrect = true, $login = false, $password = false, $userId = false) {
			list(
				$template_restore_failed_block, $template_restore_ok_block
				) = def_module::loadTemplatesForMail(
				"users/forget/" . $template,
				"restore_failed_block",
				"restore_ok_block"
			);

			$block_arr = [];
			$template = null;
			$pageId = false;
			$objectId = false;

			if ($isCorrect) {
				$template = $template_restore_ok_block;
				$block_arr['attribute:status'] = "success";
				$block_arr['login'] = $login;
				$block_arr['password'] = $password;
				$objectId = $userId;
			} else {
				$block_arr['attribute:status'] = "fail";
				$template = $template_restore_failed_block;
			}

			return def_module::parseTemplate($template, $block_arr, $pageId, $objectId);
		}

		/**
		 * Возвращает информацию об авторе
		 * @param bool|int $author_id идентификатор автора
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 * @throws publicException
		 */
		public function viewAuthor($author_id = false, $template = "default") {
			if ($author_id === false) {
				throw new publicException(getLabel('error-object-does-not-exist', null, $author_id));
			}

			if (!($author = umiObjectsCollection::getInstance()->getObject($author_id))) {
				throw new publicException(getLabel('error-object-does-not-exist', null, $author_id));
			}

			if (!$template) {
				$template = "default";
			}

			list($template_user, $template_guest, $template_sv) = users::loadTemplates(
				"users/author/{$template}",
				"user_block",
				"guest_block",
				"sv_block"
			);

			$block_arr = [];

			if ($author->getTypeId() == umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName('users', 'user')) {
				$template = $template_user;
				$block_arr['user_id'] = $author_id;

				$user = $author;

				$block_arr['nickname'] = $user->getValue("login");
				$block_arr['email'] = $user->getValue("e-mail");
				$block_arr['fname'] = $user->getValue("fname");
				$block_arr['lname'] = $user->getValue("lname");

				$block_arr['subnodes:groups'] = $groups = $user->getValue("groups");
				$systemUsersPermissions = UmiCms\Service::SystemUsersPermissions();

				if (in_array($systemUsersPermissions->getSvGroupId(), $groups)) {
					if ($template_sv) {
						$template = $template_sv;
					}
				}
			} else {
				if ($author->getValue("is_registrated")) {
					$template = $template_user;
					$block_arr['user_id'] = $user_id = $author->getValue("user_id");

					$user = umiObjectsCollection::getInstance()->getObject($user_id);

					if (!$user instanceof umiObject) {
						$systemUsersPermissions = UmiCms\Service::SystemUsersPermissions();
						$block_arr['user_id'] = $user_id = intval($systemUsersPermissions->getGuestUserId());
						$user = umiObjectsCollection::getInstance()->getObject($user_id);
					}

					if (!$user instanceof umiObject) {
						return false;
					}

					$block_arr['nickname'] = $user->getValue("login");
					$block_arr['login'] = $user->getValue("login");
					$block_arr['email'] = $user->getValue("e-mail");
					$block_arr['fname'] = $user->getValue("fname");
					$block_arr['lname'] = $user->getValue("lname");

					$block_arr['subnodes:groups'] = $groups = $user->getValue("groups");
					$systemUsersPermissions = UmiCms\Service::SystemUsersPermissions();

					if (in_array($systemUsersPermissions->getSvGroupId(), $groups)) {
						if ($template_sv) {
							$template = $template_sv;
						}
					}
				} else {
					$template = $template_guest;
					$block_arr['user_id'] = $author_id;
					$block_arr['nickname'] = $author->getValue("nickname");
					$block_arr['email'] = $author->getValue("email");
				}
			}
			return users::parseTemplate($template, $block_arr, false, $author_id);
		}

		/**
		 * Сохраняет настройки пользователя
		 * @throws coreException
		 * @throws errorPanicException
		 * @throws privateException
		 */
		public function settings_do() {
			$module = $this->module;

			if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
				$module->errorNewMessage("%errors_bad_request_method%");
				$module->errorPanic();
			}

			$this->checkCsrf();
			$redirectUrl = getRequest("from_page");

			if (!$redirectUrl) {
				$redirectUrl = getServer('HTTP_REFERER') ?: $module->pre_lang . "/users/settings/";
			}

			$module->errorSetErrorPage($redirectUrl);

			$userId = $module->user_id;
			$user = umiObjectsCollection::getInstance()->getObject($userId);
			$this->checkCurrentPassword($user);

			$oEventPoint = new umiEventPoint("users_settings_do");
			$oEventPoint->setMode("before");
			$oEventPoint->setParam("user_id", $userId);
			users::setEventPoint($oEventPoint);

			$password = trim((string) getRequest('password'));

			if ($password) {
				$passwordConfirmation = getRequest('password_confirm');
				$login = $user->getValue('login');
				$public = true;
				$password = $module->validatePassword($password, $passwordConfirmation, $login, $public);
				$module->errorThrow('public');
				$hashedPassword = UmiCms\Service::PasswordHashAlgorithm()->hash($password);
				$user->setValue("password", $hashedPassword);
			}

			$email = trim((string) getRequest('email'));

			if ($email) {
				$email = $module->validateEmail($email, $userId);
				$module->errorThrow('public');
				$user->setValue("e-mail", $email);
			}

			/** @var data|DataForms $data */
			$data = cmsController::getInstance()->getModule('data');
			$data->saveEditedObject($userId);

			$user->commit();

			$oEventPoint = new umiEventPoint("users_settings_do");
			$oEventPoint->setMode("after");
			$oEventPoint->setParam("user_id", $userId);
			users::setEventPoint($oEventPoint);

			$module->redirect($redirectUrl);
		}

		/**
		 * Проверяет значение поля "текущий пароль" из формы изменения настроек пользователя.
		 * Если значение не совпадает с текущим паролем пользователя, будет выдано сообщение об ошибке.
		 * @param iUmiObject $user текущий пользователь
		 */
		private function checkCurrentPassword(iUmiObject $user) {
			if (!$this->module->requireCurrentPassword()) {
				return;
			}

			$fields = $this->getPostedFieldsForUser($user->getId(), $_POST);


			if (!$this->containsSensitiveData($fields)) {
				return;
			}

			$login = $user->getValue('login');
			$currentPassword = trim((string) getRequest('current-password'));

			$foundUser = UmiCms\Service::Auth()->checkLogin($login, $currentPassword);

			if (!$foundUser) {
				$this->module->errorAddErrors('error-wrong-current-password');
				$this->module->errorThrow('public');
			}
		}

		/**
		 * Возвращает поля пользователя, переданные в POST-запросе
		 * @param int $userId ID пользователя
		 * @param array $data информация, переданная в POST-запросе
		 * @return array
		 */
		private function getPostedFieldsForUser($userId, $data) {
			$fields = array_key_exists('data', $data) ? $data['data'] : [];
			$fieldNames = array_key_exists($userId, $fields) ? array_keys($fields[$userId]) : [];

			if (array_key_exists('email', $data) && $data['email']) {
				$fieldNames[]= 'e-mail';
			}

			if (array_key_exists('password', $data) && $data['password']) {
				$fieldNames[]= 'password';
			}

			return $fieldNames;
		}

		/**
		 * Проверяет, есть ли в переданном списке поля из групп "idetntify_data" и "short_info"
		 * @param array $fieldNames названия полей пользователя
		 * @return bool
		 */
		private function containsSensitiveData(array $fieldNames) {
			$type = umiObjectTypesCollection::getInstance()->getTypeByGUID('users-user');
			$idFieldGroup = $type->getFieldsGroupByName('idetntify_data');
			$idFieldList = ($idFieldGroup instanceof iUmiFieldsGroup) ? $idFieldGroup->getFields() : [];

			$personalFieldGroup = $type->getFieldsGroupByName('short_info');
			$personalFieldList = ($personalFieldGroup instanceof iUmiFieldsGroup) ? $personalFieldGroup->getFields() : [];

			/**
			 * @var iUmiField[] $fieldList
			 */
			$fieldList = array_merge(
				$idFieldList,
				$personalFieldList
			);

			foreach ($fieldList as $field) {
				if (in_array($field->getName(), $fieldNames)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Регистрирует пользователя
		 * @throws coreException
		 * @throws errorPanicException
		 * @throws wrongValueException
		 */
		public function registrate_do() {
			$module = $this->module;

			if ($module->is_auth()) {
				$module->redirect($module->pre_lang . "/");
			}

			$template = getRequest('template');

			if (!$template) {
				$template = 'default';
			}

			$umiObjectTypes = umiObjectTypesCollection::getInstance();
			$umiRegistry = regedit::getInstance();

			$withoutActivation = (bool) $umiRegistry->getVal("//modules/users/without_act");
			$userTypeId = $umiObjectTypes->getTypeIdByHierarchyTypeName("users", "user");
			$customObjectTypeId = getRequest('type-id');

			if ($customObjectTypeId) {
				$childClasses = $umiObjectTypes->getChildTypeIds($userTypeId);

				if (in_array($customObjectTypeId, $childClasses)) {
					$userTypeId = $customObjectTypeId;
				}
			}

			$module->errorSetErrorPage(getServer('HTTP_REFERER'));

			$login = $module->validateLogin(getRequest('login'), false, true);
			$password = $module->validatePassword(
				getRequest('password'), getRequest('password_confirm'), getRequest('login'), true
			);
			$email = $module->validateEmail(getRequest('email'), false, !$withoutActivation);

			if (!umiCaptcha::checkCaptcha()) {
				$module->errorAddErrors('errors_wrong_captcha');
			}

			$module->errorThrow('public');

			$eventPoint = new umiEventPoint("users_registrate");
			$eventPoint->setMode("before");
			$eventPoint->setParam("login", $login);
			$eventPoint->addRef("password", $password);
			$eventPoint->addRef("email", $email);
			users::setEventPoint($eventPoint);

			$umiObjects = umiObjectsCollection::getInstance();

			$userId = $umiObjects->addObject($login, $userTypeId);
			$user = $umiObjects->getObject($userId);
			$activationCode = md5($login . time());

			$user->setValue("login", $login);
			$encodedPassword = UmiCms\Service::PasswordHashAlgorithm()->hash($password);
			$user->setValue("password", $encodedPassword);

			$user->setValue("e-mail", $email);
			$user->setValue("is_activated", $withoutActivation);
			$user->setValue("activate_code", $activationCode);

			$session = \UmiCms\Service::Session();
			$user->setValue("referer", urldecode($session->get("http_referer")));
			$user->setValue("target", urldecode($session->get("http_target")));

			$date = new umiDate();
			$user->setValue("register_date", $date->getCurrentTimeStamp());
			$user->setOwnerId($userId);

			$groupId = $umiRegistry->getVal("//modules/users/def_group");
			$user->setValue("groups", [$groupId]);

			$cmsController = cmsController::getInstance();

			/** @var data|DataForms $data_module */
			$data_module = $cmsController->getModule('data');
			$data_module->saveEditedObjectWithIgnorePermissions($userId, true, true);
			$user->commit();

			if ($withoutActivation) {
				UmiCms\Service::Auth()->loginUsingId($userId);
			}

			$variables = [
				'user_id' => $userId,
				'domain' => $domain = $cmsController->getCurrentDomain()->getCurrentHostName(),
				'activate_link' => getSelectedServerProtocol() . "://" . $domain . $module->pre_lang . "/users/activate/" . $activationCode . "/",
				'login' => $login,
				'password' => $password,
				'lname' => $user->getValue("lname"),
				'fname' => $user->getValue("fname"),
				'father_name' => $user->getValue("father_name"),
			];

			$subject = null;
			$content = null;

			if ($this->module->isUsingUmiNotifications()) {
				$mailNotifications = UmiCms\Service::MailNotifications();

				$notificationName = 'notification-users-registered';
				$subjectTemplateName = 'users-registered-subject';
				$contentTemplateName = 'users-registered-content';

				if ($withoutActivation) {
					$notificationName = 'notification-users-registered-no-activation';
					$subjectTemplateName = 'users-registered-no-activation-subject';
					$contentTemplateName = 'users-registered-no-activation-content';
				}

				$notification = $mailNotifications->getCurrentByName($notificationName);

				if ($notification instanceof MailNotification) {
					$subjectTemplate = $notification->getTemplateByName($subjectTemplateName);
					$contentTemplate = $notification->getTemplateByName($contentTemplateName);

					if ($subjectTemplate instanceof MailTemplate) {
						$subject = $subjectTemplate->getProcessedContent($variables);
					}

					if ($contentTemplate instanceof MailTemplate) {
						$content = $contentTemplate->getProcessedContent($variables);
					}
				}
			} else {
				try {
					list(
						$contentTemplate,
						$subjectTemplate,
						$contentTemplateNoActivation,
						$subjectTemplateNoActivation
						) = def_module::loadTemplatesForMail(
						"users/register/" . $template,
						"mail_registrated",
						"mail_registrated_subject",
						"mail_registrated_noactivation",
						"mail_registrated_subject_noactivation"
					);

					if ($withoutActivation && $contentTemplateNoActivation && $subjectTemplateNoActivation) {
						$subjectTemplate = $subjectTemplateNoActivation;
						$contentTemplate = $contentTemplateNoActivation;
					}

					$subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
					$content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
				} catch (Exception $e) {
					// nothing
				}
			}

			if ($subject && $content) {
				$fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");
				$emailFrom = $umiRegistry->getVal("//settings/email_from");
				$fioFrom = $umiRegistry->getVal("//settings/fio_from");

				$mail = new umiMail();
				$mail->addRecipient($email, $fio);
				$mail->setFrom($emailFrom, $fioFrom);
				$mail->setSubject($subject);
				$mail->setContent($content);
				$mail->commit();
				$mail->send();
			}

			$eventPoint = new umiEventPoint("users_registrate");
			$eventPoint->setMode("after");
			$eventPoint->setParam("user_id", $userId);
			$eventPoint->setParam("login", $login);
			users::setEventPoint($eventPoint);

			$query = ($withoutActivation) ? '?result=without_activation' : '';
			$module->redirect($module->pre_lang . "/users/registrate_done/{$query}");
		}

		/**
		 * Запускает активацию пользователя и возвращает ее результат
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 */
		public function activate($template = "default") {
			$module = $this->module;

			if ($module->is_auth()) {
				$module->redirect("/");
			}

			$activationCode = (string) getRequest('param0');
			$isSuccessful = false;

			if (!$activationCode || strlen($activationCode) != 32) {
				return $this->getActivateResult($template, $isSuccessful);
			}

			$userId = UmiCms\Service::Auth()->checkCode($activationCode);

			if ($userId !== false) {
				$module->activateUser($userId);
				$isSuccessful = true;
			}

			return $this->getActivateResult($template, $isSuccessful);
		}

		/**
		 * Отправляет письмо с кодом активации для восстановления пароля
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 * @throws coreException
		 * @throws errorPanicException
		 * @throws privateException
		 * @throws publicException
		 * @throws selectorException
		 */
		public function forget_do($template = "default") {
			$module = $this->module;
			static $macrosResult;

			if ($macrosResult) {
				return $macrosResult;
			}

			$forgetLogin = (string) getRequest('forget_login');
			$forgetEmail = (string) getRequest('forget_email');
			$hasLogin = strlen($forgetLogin) != 0;
			$hasEmail = strlen($forgetEmail) != 0;
			$userId = false;

			if ($hasLogin || $hasEmail) {
				$sel = new selector('objects');
				$sel->types('object-type')->name('users', 'user');

				if ($hasLogin) {
					$sel->where('login')->equals($forgetLogin);
				}

				if ($hasEmail) {
					$sel->where('e-mail')->equals($forgetEmail);
				}

				$sel->limit(0, 1);

				if ($sel->first) {
					$userId = $sel->first->id;
				}
			}

			if (!$userId) {
				$refererUrl = getServer('HTTP_REFERER');

				if (!strlen($refererUrl)) {
					$refererUrl = $module->pre_lang . "/users/forget/";
				}

				$module->errorRegisterFailPage($refererUrl);

				if ($hasLogin && !$hasEmail) {
					$module->errorNewMessage("%errors_forget_wrong_login%");
				}

				if ($hasEmail && !$hasLogin) {
					$module->errorNewMessage("%errors_forget_wrong_email%");
				}

				if (($hasEmail && $hasLogin) || (!$hasEmail && !$hasLogin)) {
					$module->errorNewMessage("%errors_forget_wrong_person%");
				}

				$module->errorPanic();
				return $macrosResult = $this->getForgetResult($template, false, $forgetLogin, $forgetEmail);
			}

			$activationCode = md5(users::getRandomPassword());
			$user = umiObjectsCollection::getInstance()->getObject($userId);

			$regedit = regedit::getInstance();
			$withoutActivation = (bool) $regedit->getVal("//modules/users/without_act");

			if ($withoutActivation || intval($user->getValue('is_activated'))) {
				$user->setValue("activate_code", $activationCode);
				$user->commit();

				$email = $user->getValue("e-mail");
				$fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");
				$domain = $_SERVER['HTTP_HOST'];
				$restoreLink = getSelectedServerProtocol() . "://" .
					$domain . $module->pre_lang . "/users/restore/" . $activationCode . "/";

				$variables = [
					'domain' => $domain,
					'restore_link' => $restoreLink,
					'login' => $user->getValue('login'),
					'email' => $user->getValue('e-mail'),
				];

				$subject = null;
				$content = null;

				if ($this->module->isUsingUmiNotifications()) {
					$mailNotifications = UmiCms\Service::MailNotifications();
					$notification = $mailNotifications->getCurrentByName('notification-users-restore-password');

					if ($notification instanceof MailNotification) {
						$subjectTemplate = $notification->getTemplateByName('users-restore-password-subject');
						$contentTemplate = $notification->getTemplateByName('users-restore-password-content');

						if ($subjectTemplate instanceof MailTemplate) {
							$subject = $subjectTemplate->getProcessedContent($variables);
						}

						if ($contentTemplate instanceof MailTemplate) {
							$content = $contentTemplate->getProcessedContent($variables);
						}
					}
				} else {
					try {
						list($subjectTemplate, $contentTemplate) = users::loadTemplatesForMail(
							"users/forget/" . $template,
							"mail_verification_subject",
							"mail_verification"
						);
						$subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
						$content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
					} catch (Exception $e) {
						// nothing
					}
				}

				if ($subject && $content) {
					$emailFrom = $regedit->getVal("//settings/email_from");
					$fioFrom = $regedit->getVal("//settings/fio_from");

					$mail = new umiMail();
					$mail->addRecipient($email, $fio);
					$mail->setFrom($emailFrom, $fioFrom);
					$mail->setPriorityLevel("highest");
					$mail->setSubject($subject);
					$mail->setContent($content);
					$mail->commit();
					$mail->send();
				}

				$eventPoint = new umiEventPoint("users_restore_password");
				$eventPoint->setParam("user_id", $userId);
				users::setEventPoint($eventPoint);
				return $this->getForgetResult($template, true);

			} else {
				$refererUrl = getServer('HTTP_REFERER');

				if (!strlen($refererUrl)) {
					$refererUrl = $module->pre_lang . "/users/forget/";
				}

				$module->errorRegisterFailPage($refererUrl);
				$module->errorNewMessage("%errors_forget_nonactivated_login%");
				$module->errorPanic();

				return $this->getForgetResult($template, false, $forgetLogin, $forgetEmail);
			}
		}

		/**
		 * Восстанавливает доступ пользователя и отправляет ему
		 * письмо с данными для доступа
		 * @param bool|string $activateCode код активации
		 * @param string $template имя шаблона для tpl шаблонизатора
		 * @return mixed
		 * @throws coreException
		 * @throws selectorException
		 */
		public function restore($activateCode = false, $template = "default") {
			static $result = [];

			if (isset($result[$template])) {
				return $result[$template];
			}

			if (!$activateCode) {
				$activateCode = (string) getRequest('param0');
				$activateCode = trim($activateCode);
			}

			$userId = UmiCms\Service::Auth()->checkCode($activateCode);
			$user = selector::get('object')->id($userId);

			if ($user instanceof iUmiObject) {
				/** @var iUmiObject|iUmiEntinty $user */
				$userId = $user->getId();
			} else {
				$user = false;
				$userId = false;
			}

			if (!($userId && $activateCode)) {
				return $result[$template] = $this->getRestoreResult($template, false);
			}

			$password = users::getRandomPassword();
			$encodedPassword = UmiCms\Service::PasswordHashAlgorithm()->hash($password);

			$login = $user->getValue("login");
			$email = $user->getValue("e-mail");
			$fio = $user->getValue("lname") . " " . $user->getValue("fname") . " " . $user->getValue("father_name");
			$user->setValue("password", $encodedPassword);
			$user->setValue("activate_code", "");
			$user->commit();

			$variables = [
				'domain' => getServer('HTTP_HOST'),
				'password' => $password,
				'login' => $login
			];

			$subject = null;
			$content = null;

			if ($this->module->isUsingUmiNotifications()) {
				$mailNotifications = UmiCms\Service::MailNotifications();
				$notification = $mailNotifications->getCurrentByName('notification-users-new-password');

				if ($notification instanceof MailNotification) {
					$subjectTemplate = $notification->getTemplateByName('users-new-password-subject');
					$contentTemplate = $notification->getTemplateByName('users-new-password-content');

					if ($subjectTemplate instanceof MailTemplate) {
						$subject = $subjectTemplate->getProcessedContent($variables);
					}

					if ($contentTemplate instanceof MailTemplate) {
						$content = $contentTemplate->getProcessedContent($variables);
					}
				}
			} else {
				try {
					list($subjectTemplate, $contentTemplate) = users::loadTemplatesForMail(
						"users/forget/" . $template,
						"mail_password_subject",
						"mail_password"
					);
					$subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
					$content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
				} catch (Exception $e) {
					// nothing
				}
			}

			if ($subject && $content) {
				$umiRegistry = regedit::getInstance();
				$emailFrom = $umiRegistry->getVal("//settings/email_from");
				$fioFrom = $umiRegistry->getVal("//settings/fio_from");

				$mail = new umiMail();
				$mail->setFrom($emailFrom, $fioFrom);
				$mail->addRecipient($email, $fio);
				$mail->setSubject($subject);
				$mail->setContent($content);
				$mail->commit();
				$mail->send();
			}

			$eventPoint = new umiEventPoint('successfulPasswordRestoring');
			$eventPoint->setMode('after');
			$eventPoint->setParam('userId', $userId);
			$eventPoint->call();

			return $result[$template] = $this->getRestoreResult($template, true, $login, $password, $userId);
		}

		/**
		 * Выполняет проверку безопасности на наличие CSRF-атаки
		 * @throws coreException
		 */
		private function checkCsrf() {
			\UmiCms\System\Protection\Security::getInstance()->checkReferrer();
			$umiRegistry = regedit::getInstance();

			if (!$umiRegistry->getVal('//modules/users/check_csrf_on_user_update')) {
				return;
			}

			try {
				UmiCms\System\Protection\Security::getInstance()->checkCsrf();
			} catch (UmiCms\System\Protection\CsrfException $e) {
				throw new coreException('CSRF Protection');
			} catch (Exception $e) {
				throw new coreException($e->getMessage());
			}
		}
	}
