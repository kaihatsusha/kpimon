<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\gaRealTime;
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * Description of GaRealtime
 *
 * @author slen
 */
class GaRealtime extends \yii\base\Widget {
	
	public $listMedia;
	
	//put your code here
	public function run() {
		if(!$this->listMedia)
			return;
		
		$templateContent = <<<EOD
			<div class="legend" role="" style="">
			{{#rows}}
				<span class="label label-{{color}}">&nbsp;</span> {{device}}&nbsp;&nbsp;
			{{/rows}}
			{{^rows}}
				&nbsp;
			{{/rows}}
			</div>
			<br>
			<div class="progress">
			{{#rows}}
				<div title="{{percent}}%" class="progress-bar progress-bar-{{color}}" role="progressbar" style="width:{{percent}}%">
					{{percent}}%
				</div>
			{{/rows}}
			{{^rows}}
				<div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%">&nbsp;</div>
			{{/rows}}
			</div>	
EOD;
		$result = Html::tag('script', $templateContent, ['id'=>'template','type'=>'x-tmpl-mustache']);
		
		$listMediaId = [];
		$listKey = [];
		$listIdAndKey = [];
		foreach ($this->listMedia as $media) {
			$listMediaId [] = $media->media_id;
			$listKey [] = $media->rt_keyds;
			$listIdAndKey[$media->media_id] = $media->rt_keyds;
			$result .= $this->render('gaRealtime',['media'=>$media]);
			
			
		}
		
		$view = $this->getView();
		GaRealtimeAsset::register($view);
		
		$url = Url::to(['/api/ga/ga/get-page-view','mediaId'=>  implode('|', $listMediaId),'key'=>implode('|', $listKey)]);
		
		$calable = <<<EOD
			function(data,id){
				GaRealtime.renderDevicePartition('#template',data,'.progress-legend-'+id);
				$('#eventList-'+id).text(data.metricTotals[0]?data.metricTotals[0]:0);
				GaRealtime.renderHisRealtimes(id, $('#history-realtime-'+id));
			}
EOD;
				
		$js = "GaRealtime.getRealtime('$url',".json_encode($listIdAndKey).",".$calable.");";
		$view->registerJs($js);
		return $result;
		
	}
}
