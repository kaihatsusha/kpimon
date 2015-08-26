<?php
use app\modules\api\ga\models\Media;
/* @var $this yii\web\View */
$this->title = 'Page View with AngularJS';
$listMedia = Media::find()->all();
foreach ($listMedia as &$media) {
	$media = $media->attributes;
}
?>
<div ng-app="pageViewApp" ng-controller="pageViewController">
	<div class="col-md-4" ng-repeat="media in media_list | orderBy: 'order_num'">
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">{{ media.media_name  }}</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body" style="display: block;">
				<div class="row">
					<h2 id="eventList-{{ media.media_id }}" style="text-align: center; font-size: 8em; font-weight: bold;">{{ media_count[media.media_id] }}</h2>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="progress-legend-{{ media.media_id }}">
							<div class="legend" role="" style="">
								<div ng-repeat="media_device in media_device[media.media_id]" style="display: inline-block">
								<span class="label label-{{ media_device.color }}">&nbsp;</span> {{ media_device.name }}&nbsp;&nbsp;
								</div>
							</div>
							<br>
							<div class="progress">
								<div ng-repeat="media_device in media_device[media.media_id]" class="progress-bar progress-bar-{{ media_device.color }}" role="progressbar" style="width:{{ media_device.count }}%">
									{{ media_device.count }}%
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.box-body -->
			<div class="box-footer no-padding" style="display: block;">

			</div>
			<!-- /.footer -->
		</div>
	</div>
</div>

<?php
	// Register AngularJS process for Page View script
	$this->registerJs("var media_list = ".json_encode($listMedia).";", \yii\web\View::POS_END, 'my-options');
	$this->registerJsFile('js/pageViewAngular.js'); ?>
<script>

</script>
