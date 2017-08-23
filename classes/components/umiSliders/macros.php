<?php
/**
 * Класс макросов, то есть методов, доступных в шаблоне
 */
class UmiSlidersMacros implements iModulePart{

	use tModulePart;

	/**
	 * Возвращает список слайдов слайдера по его имени.
	 * Список готов для трансляции шаблонизатора.
	 * @param string $template имя шаблона (только для tpl шаблонизатора)
	 * @param string $sliderName имя слайдера
	 * @return mixed
	 * @throws RequiredPropertyHasNoValueException
	 * @throws publicException
	 */
	public function getSlidesBySliderName($template = 'default', $sliderName) {
		/**
		 * @var umiSliders $module
		 */
		$module = $this->getModule();
		$slidersCollection = $module->getSlidersCollection();

		if (!is_string($sliderName) || strlen($sliderName) == 0) {
			throw new publicException(getLabel('label-error-slider-name-expected', $this->getModuleName()));
		}

		$slider = $slidersCollection->getByName($sliderName);

		if (!$slider instanceof \UmiCms\Classes\Components\UmiSliders\iSlider) {
			throw new publicException(getLabel('label-error-slider-with-name-not-exists', $this->getModuleName()));
		}

		$slidesCollection = $module->getSlidesCollection();

		$slides = $slidesCollection->get(
			$this->getQueryParamsForGettingSlides($slider, $slidesCollection->getMap())
		);

		if (count($slides) === 0) {
			throw new publicException(getLabel('label-error-there-are-no-slides', $this->getModuleName()));
		}

		if ($slider->isSlidesRandomOrderEnable()) {
			shuffle($slides);
		}

		list($sliderTemplate, $slidesTemplate, $slideTemplate) = umiSliders::loadTemplates(
			'umiSliders/' . $template,
			'slider',
			'slides',
			'slide'
		);

		$slidesItems = [];

		foreach ($slides as $slide) {
			$slidesItems[] = umiSliders::parseTemplate($slideTemplate,
				$this->getSlideDataForTemplate($slide)
			);
		}

		$result = [
			'subnodes:slides' => umiSliders::parseTemplate(
				$slidesTemplate, $slidesItems
			)
		];

		$result += $this->getSliderDataForTemplate($slider);

		return umiSliders::parseTemplate(
			$sliderTemplate, $result
		);
	}

	/**
	 * Возвращает структуру запроса на получения списка слайдов
	 * @param \UmiCms\Classes\Components\UmiSliders\iSlider $slider слайдер, к которому принадлежат слайды
	 * @param iUmiConstantMap $slideConstants константы слайдов
	 * @return array
	 */
	public function getQueryParamsForGettingSlides(
		\UmiCms\Classes\Components\UmiSliders\iSlider $slider, iUmiConstantMap $slideConstants
	) {
		return [
			$slideConstants->get('SLIDER_ID_FIELD_NAME') => $slider->getId(),
			$slideConstants->get('IS_ACTIVE_FIELD_NAME') => true,
			$slideConstants->get('LIMIT_KEY') => $slider->getSlidesCount(),
			$slideConstants->get('ORDER_KEY') => [
				$slideConstants->get('ORDER_FIELD_NAME') => $slideConstants->get('ORDER_DIRECTION_ASC')
			]
		];
	}

	/**
	 * Возвращает структуру данных слайдера для трансляции шаблонизатора
	 * @param \UmiCms\Classes\Components\UmiSliders\iSlider $slider слайдер
	 * @return array
	 */
	protected function getSliderDataForTemplate(\UmiCms\Classes\Components\UmiSliders\iSlider $slider) {
		/**
		 * @var \UmiCms\Classes\Components\UmiSliders\iSlider|iUmiConstantMapInjector $slider
		 */
		$constants = $slider->getMap();
		return [
			$constants->get('ID_FIELD_NAME') => $slider->getId(),
			$constants->get('NAME_FIELD_NAME') => $slider->getName(),
			$constants->get('SLIDING_SPEED_FIELD_NAME') => $slider->getSlidingSpeed(),
			$constants->get('SLIDING_DELAY_FIELD_NAME') => $slider->getSlidingDelay(),
			$constants->get('SLIDING_LOOP_ENABLE_FIELD_NAME') => $slider->isSlidingLoopEnable(),
			$constants->get('SLIDING_AUTO_PLAY_ENABLE_FIELD_NAME') => $slider->isSlidingAutoPlayEnable()
		];
	}

	/**
	 * Возвращает структуру данных слайда для трансляции шаблонизатора
	 * @param \UmiCms\Classes\Components\UmiSliders\iSlide $slide слайд
	 * @return array
	 */
	protected function getSlideDataForTemplate(\UmiCms\Classes\Components\UmiSliders\iSlide $slide) {
		$tplTemplate = umiTemplater::create('TPL');
		return [
			'@name' => $slide->getName(),
			'@title' => $slide->getTitle(),
			'@image' => $slide->getImagePath(),
			'@text' => $tplTemplate->parse([], $slide->getText()),
			'@link' => $tplTemplate->parse([], $slide->getLink()),
			'@open_in_new_tab' => (int) $slide->isNeedToOpenLinkInNewTab()
		];
	}
}
