<?php
/* @var $this yii\web\View */
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="col-md-4">
	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title"><?= $media->media_name?></h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
			</div>
		</div>
		<div class="box-body" style="display: block;">
			<img src="<?= Yii::$app->homeUrl?>img/<?= $media->logo?>" height="40px" style="margin-top: -10px;" />
			<div><span id="history-realtime-<?= $media->media_id?>"></span></div>
			<div class="row">
				<h2 id="eventList-<?= $media->media_id?>" style="margin-top: -10px; margin-bottom: 0px; text-align: center; font-size: 8em; font-weight: bold;">0</h2>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="progress-legend-<?= $media->media_id?>">
						<div class="legend" role="" style="">&nbsp;</div>
						<br>
						<div class="progress">
							<div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%;">&nbsp;</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="box-footer no-padding" style="display: block;"></div>
	</div>
</div>