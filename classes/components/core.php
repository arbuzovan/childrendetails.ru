<?php
 class core {public function cms_callMethod($v2fa70149e2a7e75da2b0303d0a36a944, $args) {return call_user_func_array(array($this, $v2fa70149e2a7e75da2b0303d0a36a944), $args);}public function isMethodExists($v2fa70149e2a7e75da2b0303d0a36a944) {return method_exists($this, $v2fa70149e2a7e75da2b0303d0a36a944);}public function __call($vea9f6aca279138c58f705c8d4cb4b8ce, $args) {throw new publicException("Method " . get_class($this) . "::" . $vea9f6aca279138c58f705c8d4cb4b8ce . " doesn't exist");}public function navibar($v66f6181bcb4cff4cd38fbc804a036db6 = 'default', $v67278b1893f28aa5a341740e3d75ff1c = true, $vb7b50d03c3f733165f5701ac7fcf81bf = 0, $v94b05a0fe0ef80a92c054adf7e668bf1 = 0) {if(!$v66f6181bcb4cff4cd38fbc804a036db6) $v66f6181bcb4cff4cd38fbc804a036db6 = 'default';$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$vb81ca7c0ccaa77e7aa91936ab0070695 = umiHierarchy::getInstance();$v50d644c42a9f32486af6d339527e1020 = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentElementId();list(    $v31912934b8f34be4364cc043cd8a0176, $vd268fd226c122b3da2fabee66e798225, $v5ad10ccde9b1728f3d06c1eb0b05ab0f, $v5499ddd845fc4355a16a16559fa94a0c, $v6bf63b3047051a28085cdb91a050acf3   ) = def_module::loadTemplates("content/navibar/".$v66f6181bcb4cff4cd38fbc804a036db6,    'navibar', 'navibar_empty', 'element', 'element_active', 'quantificator'   );$vc68ad910ed49ac65f21f1bf2c5dbf912 = $vb81ca7c0ccaa77e7aa91936ab0070695->getAllParents($v50d644c42a9f32486af6d339527e1020);$vc68ad910ed49ac65f21f1bf2c5dbf912[] = $v50d644c42a9f32486af6d339527e1020;$v691d502cfd0e0626cd3b058e5682ad1c = array();foreach($vc68ad910ed49ac65f21f1bf2c5dbf912 as $v7552cd149af7495ee7d8225974e50f80) {if(!$v7552cd149af7495ee7d8225974e50f80) continue;$v8e2dcfd7e7e24b1ca76c1193f645902b = $vb81ca7c0ccaa77e7aa91936ab0070695->getElement($v7552cd149af7495ee7d8225974e50f80);if($v8e2dcfd7e7e24b1ca76c1193f645902b instanceof iUmiHierarchyElement) {$v691d502cfd0e0626cd3b058e5682ad1c[] = $v8e2dcfd7e7e24b1ca76c1193f645902b;}}$v7dabf5c198b0bab2eaa42bb03a113e55 = sizeof($v691d502cfd0e0626cd3b058e5682ad1c) - $v94b05a0fe0ef80a92c054adf7e668bf1;$vf1386a17eed513dff70798b0551dc170 = array();for($v865c0c0b4ab0e063e5caa3387c1a8741 = (int) $vb7b50d03c3f733165f5701ac7fcf81bf;$v865c0c0b4ab0e063e5caa3387c1a8741 < $v7dabf5c198b0bab2eaa42bb03a113e55;$v865c0c0b4ab0e063e5caa3387c1a8741++) {$v8e2dcfd7e7e24b1ca76c1193f645902b = $v691d502cfd0e0626cd3b058e5682ad1c[$v865c0c0b4ab0e063e5caa3387c1a8741];$vb7983678c84fd38bc2a4db875f31215d = (!$v67278b1893f28aa5a341740e3d75ff1c && $v865c0c0b4ab0e063e5caa3387c1a8741 == ($v7dabf5c198b0bab2eaa42bb03a113e55 - 1)) ? $v5499ddd845fc4355a16a16559fa94a0c : $v5ad10ccde9b1728f3d06c1eb0b05ab0f;$ve253bed1357afcefc5fadfbc92f9eb97 = def_module::parseTemplate($vb7983678c84fd38bc2a4db875f31215d, array(     'attribute:id'  => $v8e2dcfd7e7e24b1ca76c1193f645902b->id,     'attribute:link' => $v8e2dcfd7e7e24b1ca76c1193f645902b->link,     'xlink:href'  => 'upage://' . $v8e2dcfd7e7e24b1ca76c1193f645902b->id,     'node:text'   => $v8e2dcfd7e7e24b1ca76c1193f645902b->name    ), $v8e2dcfd7e7e24b1ca76c1193f645902b->id);if (is_string($ve253bed1357afcefc5fadfbc92f9eb97) && ($v865c0c0b4ab0e063e5caa3387c1a8741 != ($v7dabf5c198b0bab2eaa42bb03a113e55 - 1))) {$ve253bed1357afcefc5fadfbc92f9eb97 .= $v6bf63b3047051a28085cdb91a050acf3;}$vf1386a17eed513dff70798b0551dc170[] = $ve253bed1357afcefc5fadfbc92f9eb97;}if($v7dabf5c198b0bab2eaa42bb03a113e55 == 0) $v31912934b8f34be4364cc043cd8a0176 = $vd268fd226c122b3da2fabee66e798225;return def_module::parseTemplate($v31912934b8f34be4364cc043cd8a0176, array(    'items'   => array('nodes:item' => $vf1386a17eed513dff70798b0551dc170),    'void:elements' => $vf1386a17eed513dff70798b0551dc170   ));}public function insertCut($v66f6181bcb4cff4cd38fbc804a036db6 = "default") {if(!$v66f6181bcb4cff4cd38fbc804a036db6) $v66f6181bcb4cff4cd38fbc804a036db6 = "default";$vb3b32a2d422265cd25c3323ed0157f81 = getRequest('cut_pages');$ve1ba980ce14a8c0d7e2779f895ab8695 = ((int) getRequest('cut_curr_page')) + 1;if($vb3b32a2d422265cd25c3323ed0157f81 > 1) {return "%system numpages('{$vb3b32a2d422265cd25c3323ed0157f81}', '1', '{$v66f6181bcb4cff4cd38fbc804a036db6}', 'cut')%";}}public function curr_module() {$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$v22884db148f0ffb0d830ba431102b0b5 = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentModule();$vea9f6aca279138c58f705c8d4cb4b8ce = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentMethod();if($v22884db148f0ffb0d830ba431102b0b5 == "config" && $vea9f6aca279138c58f705c8d4cb4b8ce == "mainpage") return "";if($v22884db148f0ffb0d830ba431102b0b5 == "data" && $vea9f6aca279138c58f705c8d4cb4b8ce == "trash") return "trash";return $v22884db148f0ffb0d830ba431102b0b5;}public function getTypeEditLinkXml($v5f694956811487225d15e973ca38fbab) {if(system_is_allowed("data", "type_edit")) {$v0e8133eb006c0f85ed9444ae07a60842 = umiObjectTypesCollection::getInstance();if($v599dcce2998a6b40b1e38e8c6006cb0a = $v0e8133eb006c0f85ed9444ae07a60842->getType($v5f694956811487225d15e973ca38fbab)) {return array(      'type' => array(       'node:name'   => $v599dcce2998a6b40b1e38e8c6006cb0a->getName(),       'attribute:link' => $this->pre_lang . "/admin/data/type_edit/{$v5f694956811487225d15e973ca38fbab}/"      )     );}}}public function importSkinXsl($v435ed7e9f07f740abf511a62c00eef6e = false) {static $v5de329acc7ea89a6c074d66f839ae8c3 = false;$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$v6c71bfd1173fe0a53dbcf5839d2d11f7 = CURRENT_WORKING_DIR . '/styles/skins/' . system_get_skinName();$v3d9f9cf73085146758d31dfaeba8dfaf = $v8b1dc169bf460ee884fceef66c6607d6->getResourcesDirectory() . 'admin';$v52a43e48ec4649dee819dadabcab1bde = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentModule();if (!$v435ed7e9f07f740abf511a62c00eef6e) {$v22884db148f0ffb0d830ba431102b0b5 = $v8b1dc169bf460ee884fceef66c6607d6->getModule($v52a43e48ec4649dee819dadabcab1bde);$v870b60148237c1452dfb337fdd19c314 = ($v22884db148f0ffb0d830ba431102b0b5 instanceof def_module) ? $v22884db148f0ffb0d830ba431102b0b5->dataType : null;$vc3e705290a2a0a940dc4a4859b9a2fbb = ($v22884db148f0ffb0d830ba431102b0b5 instanceof def_module) ? $v22884db148f0ffb0d830ba431102b0b5->actionType : null;if ($vc3e705290a2a0a940dc4a4859b9a2fbb == 'create') {$vc3e705290a2a0a940dc4a4859b9a2fbb = 'modify';}$ve25c4c1e191c110a85b2a16c1889e111 = '/data/' . $v870b60148237c1452dfb337fdd19c314 . '.' . $vc3e705290a2a0a940dc4a4859b9a2fbb . '.xsl';}else {$ve25c4c1e191c110a85b2a16c1889e111 = '/data/modules/' . $v52a43e48ec4649dee819dadabcab1bde . '/' . $v435ed7e9f07f740abf511a62c00eef6e;}$v5a11716ee7b836c45034333b1dd25c7c = $v3d9f9cf73085146758d31dfaeba8dfaf . str_replace("custom", "", $ve25c4c1e191c110a85b2a16c1889e111);$vf9daed1e99fc942d1e8edcffb040b183 = $v6c71bfd1173fe0a53dbcf5839d2d11f7 . $ve25c4c1e191c110a85b2a16c1889e111;$vd6fe1d0be6347b8ef2427fa629c04485 = !file_exists($v5a11716ee7b836c45034333b1dd25c7c) ? $vf9daed1e99fc942d1e8edcffb040b183 : $v5a11716ee7b836c45034333b1dd25c7c;$v75be6044f74d1a111afdcff1b6cd5991 = $v6c71bfd1173fe0a53dbcf5839d2d11f7 . '/empty.xsl';if (!$v5de329acc7ea89a6c074d66f839ae8c3) {if (file_exists($v75be6044f74d1a111afdcff1b6cd5991) == false) {throw new coreException("Empty template is required. Not found in '{$v75be6044f74d1a111afdcff1b6cd5991}'");}$v5de329acc7ea89a6c074d66f839ae8c3 = array('plain:result' => file_get_contents($v75be6044f74d1a111afdcff1b6cd5991));}if (checkFileForReading($vd6fe1d0be6347b8ef2427fa629c04485, array('xsl'))) {$result = file_get_contents($vd6fe1d0be6347b8ef2427fa629c04485);return array('plain:result' => $result);}else {return $v5de329acc7ea89a6c074d66f839ae8c3;}}public function importExtSkinXsl($v15d61712450a686a7f365adf4fef581f = null) {$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$v37c24a3158dc6f1aab7f6398e8cf5f70 = system_get_skinName();$v52a43e48ec4649dee819dadabcab1bde = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentModule();if (!is_null($v15d61712450a686a7f365adf4fef581f)) {$vd6fe1d0be6347b8ef2427fa629c04485 = CURRENT_WORKING_DIR . "/styles/skins/{$v37c24a3158dc6f1aab7f6398e8cf5f70}/data/modules/{$v52a43e48ec4649dee819dadabcab1bde}/ext/";if (file_exists($vd6fe1d0be6347b8ef2427fa629c04485)) {$result = '';$v45b963397aa40d4a0063e0d85e4fe7a1 = glob($vd6fe1d0be6347b8ef2427fa629c04485 . "{$v15d61712450a686a7f365adf4fef581f}.*.xsl");if (is_array($v45b963397aa40d4a0063e0d85e4fe7a1)) {foreach ($v45b963397aa40d4a0063e0d85e4fe7a1 as $v435ed7e9f07f740abf511a62c00eef6e) {$v66f6181bcb4cff4cd38fbc804a036db6 = str_replace($vd6fe1d0be6347b8ef2427fa629c04485, '', $v435ed7e9f07f740abf511a62c00eef6e);$result .= "<xsl:include href='udata://core/importExtFileXsl/{$v66f6181bcb4cff4cd38fbc804a036db6}'/>";}}return array(      'plain:result' => $this->getIncludesTemplate($result)     );}}$v239319d78b3f5eec311e4f7afa7f7780 = CURRENT_WORKING_DIR . "/styles/skins/{$v37c24a3158dc6f1aab7f6398e8cf5f70}/empty.xsl";if(file_exists($v239319d78b3f5eec311e4f7afa7f7780) == false) {throw new coreException("Empty template is required. Not found in '{$v239319d78b3f5eec311e4f7afa7f7780}'");}return array(    'plain:result' => file_get_contents($v239319d78b3f5eec311e4f7afa7f7780)   );}public function importExtFileXsl($v435ed7e9f07f740abf511a62c00eef6e = false) {$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$v37c24a3158dc6f1aab7f6398e8cf5f70 = system_get_skinName();$v52a43e48ec4649dee819dadabcab1bde = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentModule();$vd6fe1d0be6347b8ef2427fa629c04485 = CURRENT_WORKING_DIR . '/styles/skins/' . $v37c24a3158dc6f1aab7f6398e8cf5f70 . '/data/modules/' . $v52a43e48ec4649dee819dadabcab1bde . '/ext/' . $v435ed7e9f07f740abf511a62c00eef6e;return array(    'plain:result' => file_get_contents($vd6fe1d0be6347b8ef2427fa629c04485)   );}public function getIncludesTemplate($result) {$v37c24a3158dc6f1aab7f6398e8cf5f70 = system_get_skinName();$v510f48dc21b4534f08a7391575952635 = CURRENT_WORKING_DIR . "/styles/skins/{$v37c24a3158dc6f1aab7f6398e8cf5f70}/ext.xsl";if(file_exists($v510f48dc21b4534f08a7391575952635) == false) {throw new coreException("Ext template is required. Not found in '{$v510f48dc21b4534f08a7391575952635}'");}return str_replace('<!--includes-->', $result, file_get_contents($v510f48dc21b4534f08a7391575952635));}public function header() {$v594c103f2c6e04c3d8ab059f031e0c1a = cmsController::getInstance();if($v594c103f2c6e04c3d8ab059f031e0c1a->headerLabel) {$vd304ba20e96d87411588eeabac850e34 = $v594c103f2c6e04c3d8ab059f031e0c1a->headerLabel;}else {$v22884db148f0ffb0d830ba431102b0b5 = $v594c103f2c6e04c3d8ab059f031e0c1a->getCurrentModule();$vea9f6aca279138c58f705c8d4cb4b8ce = $v594c103f2c6e04c3d8ab059f031e0c1a->getCurrentMethod();$vd304ba20e96d87411588eeabac850e34 = "header-{$v22884db148f0ffb0d830ba431102b0b5}-{$vea9f6aca279138c58f705c8d4cb4b8ce}";}return getLabel($vd304ba20e96d87411588eeabac850e34);}public function cacheIsEnabled() {$v3f48301f2668ec4eec370518ddcffe63 = mainConfiguration::getInstance();$vce09120550b0038f0f5fac99be54cbb5 = (int) (bool) $v3f48301f2668ec4eec370518ddcffe63->get('cache', 'static.enabled');$vb99eb979e6f6efabc396f777b503f7e7 = cacheFrontend::getInstance();$vb6d9b5062199565f24ca0f91578c8e8b = $vb99eb979e6f6efabc396f777b503f7e7->getIsConnected();if ($vb6d9b5062199565f24ca0f91578c8e8b) {$vb99eb979e6f6efabc396f777b503f7e7->doPeriodicOperations();}return (int) ($vce09120550b0038f0f5fac99be54cbb5 || $vb6d9b5062199565f24ca0f91578c8e8b);}public function contextManualUrl() {$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();$v52a43e48ec4649dee819dadabcab1bde = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentModule();$vddaa6e8c8c412299272e183087b8f7b6 = $v8b1dc169bf460ee884fceef66c6607d6->getCurrentMethod();$v535d02e594853266d2db02ff178dd7f7 = uLangStream::getLangPrefix();$vf25b6b531fd89909bed207bd4feacd28 = false;$v22884db148f0ffb0d830ba431102b0b5 = $v8b1dc169bf460ee884fceef66c6607d6->getModule($v52a43e48ec4649dee819dadabcab1bde);if($v22884db148f0ffb0d830ba431102b0b5 instanceof def_module) {if(isset($v22884db148f0ffb0d830ba431102b0b5->data['object']['attribute:id'])) {$v16b2b26000987faccb260b9d39df1269 = $v22884db148f0ffb0d830ba431102b0b5->data['object']['attribute:id'];$va8cfde6331bd59eb2ac96f8911c4b666 = umiObjectsCollection::getInstance()->getObject($v16b2b26000987faccb260b9d39df1269);if($va8cfde6331bd59eb2ac96f8911c4b666 instanceof umiObject) {$v6301cee35ea764a1e241978f93f01069 = $va8cfde6331bd59eb2ac96f8911c4b666->getTypeId();$v726e8e4809d4c1b28a6549d86436a124 = umiObjectTypesCollection::getInstance()->getType($v6301cee35ea764a1e241978f93f01069);$vacf567c9c3d6cf7c6e2cc0ce108e0631 = $v726e8e4809d4c1b28a6549d86436a124->getHierarchyTypeId();if($vacf567c9c3d6cf7c6e2cc0ce108e0631) {$v89b0b9deff65f8b9cd1f71bc74ce36ba = umiHierarchyTypesCollection::getInstance()->getType($vacf567c9c3d6cf7c6e2cc0ce108e0631);$vf25b6b531fd89909bed207bd4feacd28 = $v89b0b9deff65f8b9cd1f71bc74ce36ba->getExt();}}}if(!$vf25b6b531fd89909bed207bd4feacd28 && isset($v22884db148f0ffb0d830ba431102b0b5->data['page']['attribute:id'])) {$v7552cd149af7495ee7d8225974e50f80 = $v22884db148f0ffb0d830ba431102b0b5->data['page']['attribute:id'];$v8e2dcfd7e7e24b1ca76c1193f645902b = umiHierarchy::getInstance()->getElement($v7552cd149af7495ee7d8225974e50f80);if($v8e2dcfd7e7e24b1ca76c1193f645902b instanceof umiHierarchyElement) {$vf25b6b531fd89909bed207bd4feacd28 = $v8e2dcfd7e7e24b1ca76c1193f645902b->getMethod();}}if(!$vf25b6b531fd89909bed207bd4feacd28 && isset($v22884db148f0ffb0d830ba431102b0b5->data['page']['attribute:type-id'])) {$v6301cee35ea764a1e241978f93f01069 = $v22884db148f0ffb0d830ba431102b0b5->data['page']['attribute:type-id'];$v726e8e4809d4c1b28a6549d86436a124 = umiObjectTypesCollection::getInstance()->getType($v6301cee35ea764a1e241978f93f01069);if($v726e8e4809d4c1b28a6549d86436a124 instanceof umiObjectType) {$vacf567c9c3d6cf7c6e2cc0ce108e0631 = $v726e8e4809d4c1b28a6549d86436a124->getHierarchyTypeId();$v89b0b9deff65f8b9cd1f71bc74ce36ba = umiHierarchyTypesCollection::getInstance()->getType($vacf567c9c3d6cf7c6e2cc0ce108e0631);if($v89b0b9deff65f8b9cd1f71bc74ce36ba instanceof umiHierarchyType) {$vf25b6b531fd89909bed207bd4feacd28 = $v89b0b9deff65f8b9cd1f71bc74ce36ba->getExt();}}}if(!$vf25b6b531fd89909bed207bd4feacd28 && isset($v22884db148f0ffb0d830ba431102b0b5->data['object']['attribute:type-id'])) {$v6301cee35ea764a1e241978f93f01069 = $v22884db148f0ffb0d830ba431102b0b5->data['object']['attribute:type-id'];$v726e8e4809d4c1b28a6549d86436a124 = umiObjectTypesCollection::getInstance()->getType($v6301cee35ea764a1e241978f93f01069);if($v726e8e4809d4c1b28a6549d86436a124 instanceof umiObjectType) {$vacf567c9c3d6cf7c6e2cc0ce108e0631 = $v726e8e4809d4c1b28a6549d86436a124->getHierarchyTypeId();$v89b0b9deff65f8b9cd1f71bc74ce36ba = umiHierarchyTypesCollection::getInstance()->getType($vacf567c9c3d6cf7c6e2cc0ce108e0631);if($v89b0b9deff65f8b9cd1f71bc74ce36ba instanceof umiHierarchyType) {$vf25b6b531fd89909bed207bd4feacd28 = $v89b0b9deff65f8b9cd1f71bc74ce36ba->getExt();}}}}$vd6fe1d0be6347b8ef2427fa629c04485 = false;if($vf25b6b531fd89909bed207bd4feacd28) {$v606d70ea1d6f03c2fc9b74fffa0648fd = "./man/" . $v535d02e594853266d2db02ff178dd7f7 . "/" . $v52a43e48ec4649dee819dadabcab1bde . "/" . $vddaa6e8c8c412299272e183087b8f7b6 . "." . $vf25b6b531fd89909bed207bd4feacd28 . ".html";if(is_file($v606d70ea1d6f03c2fc9b74fffa0648fd)) {$vd6fe1d0be6347b8ef2427fa629c04485 = $v606d70ea1d6f03c2fc9b74fffa0648fd;}}if(!$vd6fe1d0be6347b8ef2427fa629c04485) {$v606d70ea1d6f03c2fc9b74fffa0648fd = "./man/" . $v535d02e594853266d2db02ff178dd7f7 . "/" . $v52a43e48ec4649dee819dadabcab1bde . "/" . $vddaa6e8c8c412299272e183087b8f7b6 . ".html";if(is_file($v606d70ea1d6f03c2fc9b74fffa0648fd)) {$vd6fe1d0be6347b8ef2427fa629c04485 = $v606d70ea1d6f03c2fc9b74fffa0648fd;}else {$vd6fe1d0be6347b8ef2427fa629c04485 = "./man/" . $v535d02e594853266d2db02ff178dd7f7 . "/dummy.html";}}if(!$vd6fe1d0be6347b8ef2427fa629c04485) $vd6fe1d0be6347b8ef2427fa629c04485 = "";return $vd6fe1d0be6347b8ef2427fa629c04485;}public function getDomainsList() {$ve4e46deb7f9cc58c7abfb32e5570b6f3 = domainsCollection::getInstance();$v41275a535677f79ff347e01bc530c176 = permissionsCollection::getInstance();$vfa53b91ccc1b78668d5af58e1ed3a485 = UmiCms\Service::Auth();$v8e44f0089b076e18a718eb9ca3d94674 = $vfa53b91ccc1b78668d5af58e1ed3a485->getUserId();$result = $ve4e46deb7f9cc58c7abfb32e5570b6f3->getList();foreach ($result as $vb80bb7740288fda1f201890375a60c8f => $vad5f82e879a9c5d6b5b442eb37e50551) {if ($v41275a535677f79ff347e01bc530c176->isAllowedDomain($v8e44f0089b076e18a718eb9ca3d94674, $vad5f82e879a9c5d6b5b442eb37e50551->getId()) == 0) {unset($result[$vb80bb7740288fda1f201890375a60c8f]);}}return array('domains' => array('nodes:domain' => $result));}public function contextManual() {$vd6fe1d0be6347b8ef2427fa629c04485 = $this->contextManualUrl();return ($vd6fe1d0be6347b8ef2427fa629c04485) ? array('plain:result' => file_get_contents($vd6fe1d0be6347b8ef2427fa629c04485)) : false;}public function getEditLinkWrapper ($v22884db148f0ffb0d830ba431102b0b5,$vb80bb7740288fda1f201890375a60c8f=0,$v599dcce2998a6b40b1e38e8c6006cb0a=false){if ($vb80bb7740288fda1f201890375a60c8f==0 && $v22884db148f0ffb0d830ba431102b0b5=='content'){return array(     'nodes:item' => array(      array(       'attribute:add' =>'/admin/content/add/0/page'      )     )    );}$v8b1dc169bf460ee884fceef66c6607d6 = cmsController::getInstance();if ($v8b1dc169bf460ee884fceef66c6607d6->getCurrentMode() !== 'admin'){return false;}$ve4e675c0eb28cf87adb064baf98a316e = $v8b1dc169bf460ee884fceef66c6607d6->getModule($v22884db148f0ffb0d830ba431102b0b5);if ($ve4e675c0eb28cf87adb064baf98a316e instanceof def_module){if (is_callable(array($ve4e675c0eb28cf87adb064baf98a316e,'getEditLink'))) {$v9b207167e5381c47682c6b4f58a623fb = $ve4e675c0eb28cf87adb064baf98a316e->getEditLink($vb80bb7740288fda1f201890375a60c8f, $v599dcce2998a6b40b1e38e8c6006cb0a);if (count($v9b207167e5381c47682c6b4f58a623fb) == 2) {return array(       'nodes:item' => array(        array(         'attribute:add' =>$v9b207167e5381c47682c6b4f58a623fb[0],                                    'attribute:edit'=>$v9b207167e5381c47682c6b4f58a623fb[1]                                )                            )                        );}else if (count($v9b207167e5381c47682c6b4f58a623fb)==1) {if (strpos($v9b207167e5381c47682c6b4f58a623fb[0],'edit') !== false){return array(        'nodes:item' => array(         array(          'attribute:add' =>$v9b207167e5381c47682c6b4f58a623fb[0]         )        )       );}else {return false;}}else {return false;}}else {return false;}}else {return false;}}public function insertPopup($v1cb251ec0d568de6a929b520c4aed8d1 = "", $v25d902c24283ab8cfbac54dfa101ad31 = "") {$v9b207167e5381c47682c6b4f58a623fb = $v1cb251ec0d568de6a929b520c4aed8d1;$vd6fe1d0be6347b8ef2427fa629c04485 = (substr($v25d902c24283ab8cfbac54dfa101ad31, 0, 1) == "/") ? "." . $v25d902c24283ab8cfbac54dfa101ad31 : $v25d902c24283ab8cfbac54dfa101ad31;if(file_exists($vd6fe1d0be6347b8ef2427fa629c04485)) {$v49960fd911110a40c4cfec43e2e7603b = getimagesize($vd6fe1d0be6347b8ef2427fa629c04485);if(is_array($v49960fd911110a40c4cfec43e2e7603b)) {list($veaae26a6fb20ed3ef54fb23bfa0b1fcc, $vb435e227d5dd201e1768b2bcb2e0aa81) = $v49960fd911110a40c4cfec43e2e7603b;$v9b207167e5381c47682c6b4f58a623fb = "<a href=\"$v25d902c24283ab8cfbac54dfa101ad31\" onclick=\"javascript: return gen_popup('$v25d902c24283ab8cfbac54dfa101ad31', '$veaae26a6fb20ed3ef54fb23bfa0b1fcc', '$vb435e227d5dd201e1768b2bcb2e0aa81');\" class=\"umi_popup\">" . $v1cb251ec0d568de6a929b520c4aed8d1 . "</a>";}}return $v9b207167e5381c47682c6b4f58a623fb;}public function insertThumb($v8330c47ad2c1594f58ed3c1493e14a4a = "", $v99297352c909559e81d6e48218c07f8a = "", $v34823136d0dd91d0f5d22db740f7679c = "") {$vc832fc52362cfaac7cf87a517e289c3a = (substr($v99297352c909559e81d6e48218c07f8a, 0, 1) == "/") ? "." . $v99297352c909559e81d6e48218c07f8a : $v99297352c909559e81d6e48218c07f8a;$vf12105146129696d6a7e0ed521db2e48 = "<img src=\"$v8330c47ad2c1594f58ed3c1493e14a4a\" border=\"0\" class=\"umi_thumb\" alt=\"{$v34823136d0dd91d0f5d22db740f7679c}\" title=\"{$v34823136d0dd91d0f5d22db740f7679c}\" />";if(file_exists($vc832fc52362cfaac7cf87a517e289c3a)) {$v49960fd911110a40c4cfec43e2e7603b = getimagesize($vc832fc52362cfaac7cf87a517e289c3a);if(is_array($v49960fd911110a40c4cfec43e2e7603b)) {list($veaae26a6fb20ed3ef54fb23bfa0b1fcc, $vb435e227d5dd201e1768b2bcb2e0aa81) = $v49960fd911110a40c4cfec43e2e7603b;$v9b207167e5381c47682c6b4f58a623fb = "<a href=\"$v99297352c909559e81d6e48218c07f8a\" onclick=\"javascript: return gen_popup('$v99297352c909559e81d6e48218c07f8a', '$veaae26a6fb20ed3ef54fb23bfa0b1fcc', '$vb435e227d5dd201e1768b2bcb2e0aa81');\">" . $vf12105146129696d6a7e0ed521db2e48 . "</a>";}}return $v9b207167e5381c47682c6b4f58a623fb;}}