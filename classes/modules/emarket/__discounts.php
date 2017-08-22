<?php
	abstract class __emarket_discounts {

		public function getAllDiscounts($codeName = false, $resetCache = false) {
			static $discounts = array();

			if ($resetCache || cmsController::$IGNORE_MICROCACHE) {
				$discounts = array();
			}

			if ($codeName && isset($discounts[$codeName])) {
				return $discounts[$codeName];
			} elseif (isset($discounts['all'])) {
				return $discounts['all'];
			}

			$cacheFrontend = cacheFrontend::getInstance();
			$key = ($codeName) ? (string) $codeName . '_discounts' : 'discounts_list';
			$cachedDiscounts = $cacheFrontend->loadData($key);
			if (is_array($cachedDiscounts)) {
				return $cachedDiscounts;
			}

			$sel = new selector('objects');
			$sel->types('hierarchy-type')->name('emarket', 'discount');
			$sel->where('is_active')->equals(true);
			if ($codeName) {
				$sel->where('discount_type_id')->equals($this->getDiscountTypeId($codeName));
				$discounts[$codeName] = $sel->result();
				$cacheFrontend->saveData($key, $discounts[$codeName], 3600);
				return $discounts[$codeName];
			}
			$sel->option('load-all-props')->value(true);
			$sel->option('no-length')->value(true);
			$discounts['all'] = $sel->result();
			$cacheFrontend->saveData($key, $discounts['all'], 3600);
			return $discounts['all'];
		}

		public function getDiscountTypeId($codeName) {
			return discount::getTypeId($codeName);
		}

		public function discountInfo($discountId = false, $template = 'default') {
			if(!$template) $template = 'default';
			list($tpl_block, $tpl_block_empty) = def_module::loadTemplates("emarket/discounts/{$template}",
				'discount_block', 'discount_block_empty');

			try {
				$discount = itemDiscount::get($discountId);
			} catch (privateException $e) {
				$discount = null;
			}

			if($discount instanceof discount) {
				$info = array(
					'attribute:id'		=> $discount->id,
					'attribute:name'	=> $discount->getName(),
					'description'		=> $discount->getValue('description')
				);
				return def_module::parseTemplate($tpl_block, $info, false, $discount->id);
			} else {
				return def_module::parseTemplate($tpl_block_empty, array());
			}
		}
	};
?>
