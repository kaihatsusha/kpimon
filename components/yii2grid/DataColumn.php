<?php
namespace app\components\yii2grid;

use yii\helpers\Html;

class DataColumn extends \yii\grid\DataColumn {
	/**
     * Renders the footer cell.
     */
    public function renderFooterCell() {
    	$colspan = isset($this->footerOptions['colspan']) ? $this->footerOptions['colspan'] : false;
    	if ($colspan === 0) {
    		return '';
    	}
    	
        return Html::tag('td', $this->renderFooterCellContent(), $this->footerOptions);
    }
}
?>