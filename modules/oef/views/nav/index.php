<?php
    use yii\bootstrap\ActiveForm;
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;
    use app\modules\oef\views\MoreAsset;
    use kartik\datetime\DateTimePicker;

    // css & js
    MoreAsset::$CONTEXT = ['js'=>['js/oef/navIndex.js', 'js/fin/report.js'], 'depends'=>['app\assets\ChartSparklineAsset']];
    MoreAsset::register($this);

    $ajaxNavChartUrl = BaseUrl::toRoute(['nav/ajaxchart']);

    $this->title = Yii::t('oef.nav', 'Navs List');
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('oef.nav', 'Transaction'); ?></h3>
        <div class="box-tools">
        <div style="width: 150px;" class="input-group input-group-sm">
            <select id="max-items-chart" class="pull-right form-control">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30" selected="selected">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </select>
            <div class="input-group-btn">
                <button id="btnGetChart" class='btn btn-default' type='button' onclick='getNavChart("<?= $ajaxNavChartUrl; ?>")'><i class="fa fa-bar-chart-o"></i></button>
            </div>
        </div>
    </div>
    <div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($searchModel, 'trade_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'trade_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6"><div class="form-group">
                    <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div></div>
        </div>
        <div class="row">
            <div id="navChart" class="col-md-12"></div>
        </div>
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'headerRowOptions'=>['class'=>'warning'],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>20]
            ]),
            'columns'=>[
                [
                    'label'=>Yii::t('fin.grid', 'No.'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        return $pagination->page * $pagination->pageSize + $index + 1;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Reference'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return str_pad($model->nav_id, 6, '0', STR_PAD_LEFT);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Trade Date'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->trade_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Decide Date'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->decide_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Nav'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->nav_value, 2);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Prev Nav'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->nav_value_prev, 2);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Delta'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        $config = ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red'];
                        return NumberUtils::getIncDecNumber($model->nav_value - $model->nav_value_prev, $config, 2);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Delta'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        $config = ['template'=>'<span class="{color}">{number} %</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red'];
                        $percent = 100*($model->nav_value - $model->nav_value_prev) / $model->nav_value_prev;
                        return NumberUtils::getIncDecNumber($percent, $config, 2);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Action'),
                    'headerOptions'=>['style'=>'text-align: center; width: 100px;'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model, $key, $index) {
                        $btnClass = MasterValueUtils::getColorRow($index);
                        $lblView = Yii::t('button', 'View');
                        $lblEdit = Yii::t('button', 'Edit');
                        $urlEdit = null;
                        $arrBtns = [];

                        $entryId = $model->nav_id;
                        $urlEdit = BaseUrl::toRoute(['nav/update', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);
                        $urlView = BaseUrl::toRoute(['nav/view', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

                        $html = '<div class="btn-group">';
                        $html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-' . $btnClass]);
                        $html .= '<button type="button" class="btn btn-' . $btnClass . ' dropdown-toggle" data-toggle="dropdown">';
                        $html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
                        $html .= '</button>';
                        $html .= '<ul class="dropdown-menu" role="menu">';
                        $html .= implode('', $arrBtns);
                        $html .= '</ul></div>';

                        return $html;
                    }
                ]
            ]
        ]); ?><?php Pjax::end(); ?></div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>