<?php
namespace app\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Menu
 * @package app\widget
 */
class Menu extends \yii\widgets\Menu
{

    /**
     * @var string
     */
    public $linkTemplate = "<a href=\"{url}\">\n{icon}\n{label}\n{right-icon}\n{badge}</a>";
    /**
     * @var string
     */
    public $labelTemplate = '{icon}\n{label}\n{badge}';

    /**
     * @var string
     */
    public $badgeTag = 'span';
    /**
     * @var string
     */
    public $badgeClass = 'label pull-right';
    /**
     * @var string
     */
    public $badgeBgClass;

    /**
     * @var string
     */
    public $parentRightIcon = '<i class="fa fa-angle-left pull-right"></i>';
	
	private $objectId;
    private $pathPattern;
	
	/**
     * Renders the menu.
     */
    public function run() {
		$controller = \Yii::$app->controller;
        $moduleObj = $controller->module;

        $paths = [];
        $moduleId = isset($moduleObj->id) ? $moduleObj->id : false;
        if ($moduleId && (in_array($moduleId, ['fin', 'net', 'jar', 'oef']))) {
            $paths[] = $moduleId;
        }
        $paths[] = $controller->id;

		$this->objectId = isset($controller->objectId) ? $controller->objectId : false;
        $this->pathPattern = '/^\/' . implode('\/', $paths) . '\/.*$/';
		parent::run();
	}
	
    /**
     * @inheritdoc
     */
    protected function renderItem($item) {
        if (isset($item['type'])) {
            switch ($item['type']) {
                case 'split':
                    return $item['label'];
                    break;
            }
        }

        $item['badgeOptions'] = isset($item['badgeOptions']) ? $item['badgeOptions'] : [];
        if (!ArrayHelper::getValue($item, 'badgeOptions.class')) {
            $bg = isset($item['badgeBgClass']) ? $item['badgeBgClass'] : $this->badgeBgClass;
            $item['badgeOptions']['class'] = $this->badgeClass.' '.$bg;
        }

        if (isset($item['items']) && !isset($item['right-icon'])) {
            $item['right-icon'] = $this->parentRightIcon;
        }

        if (isset($item['url'])) {
			$requireId = isset($item['requireId']) && $item['requireId'] === true;
			if ($requireId) {
				if ($this->objectId === false) {
					return '';
				}

                $matches = null;
                preg_match($this->pathPattern, $item['url'][0], $matches);
                if (count($matches) < 1) {
                    return '';
                }

				$item['url']['id'] = $this->objectId;
			}
			
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
            return strtr($template, [
                '{badge}'=> isset($item['badge'])
                    ? Html::tag('small', $item['badge'], $item['badgeOptions'])
                    : '',
                '{icon}'=>isset($item['icon']) ? $item['icon'] : '',
                '{right-icon}'=>isset($item['right-icon']) ? $item['right-icon'] : '',
                '{url}' => Url::to($item['url']),
                '{label}' => $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            return strtr($template, [
                '{badge}'=> isset($item['badge'])
                    ? Html::tag('small', $item['badge'], $item['badgeOptions'])
                    : '',
                '{icon}'=>isset($item['icon']) ? $item['icon'] : '',
                '{right-icon}'=>isset($item['right-icon']) ? $item['right-icon'] : '',
                '{label}' => $item['label'],
            ]);
        }
    }
}
