<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;
    use app\modules\fin\views\ReportAsset;

    // css & js
    ReportAsset::$CONTEXT = ['js'=>['js/fin/interesIndex.js'], 'depends'=>['app\assets\ChartJsAsset']];
    ReportAsset::register($this);

    $this->title = Yii::t('fin.interest', 'Interest Unit List');
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('fin.interest', 'Transaction'); ?></h3>
    </div>
    <div class="box-body">
        <script type="text/javascript">
            CHART_DATA = <?= $chartData; ?>;
        </script>
        <div class="row"><div class="chart">
            <canvas id="interestUnitAreaChart" style="height:400px"></canvas>
        </div></div>
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
                        return str_pad($model->id, 6, '0', STR_PAD_LEFT);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Start Date'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'End Date'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return is_null($model->end_date) ? '<span class="text-fuchsia">' . DateTimeUtils::getNow()->format(DateTimeUtils::FM_DB_DATE) . '</span>' : DateTimeUtils::htmlDateFormatFromDB($model->end_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Unit'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->interest_unit, 2);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Days'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);
                        $endDate = is_null($model->end_date) ? DateTimeUtils::getNow() : DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
                        $interval = $endDate->diff($startDate);
                        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
                        return $days;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Interest'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);
                        $endDate = is_null($model->end_date) ? DateTimeUtils::getNow() : DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
                        $interval = $endDate->diff($startDate);
                        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;

                        return NumberUtils::format($model->interest_unit * $days, 2);
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
                        $lblCopy = Yii::t('button', 'Copy');
                        $arrBtns = [];

                        $entryId = $model->id;
                        $urlEdit = BaseUrl::toRoute(['interest/update', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                        $urlView = BaseUrl::toRoute(['interest/view', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

                        $urlCopy = BaseUrl::toRoute(['interest/copy', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlCopy, $lblCopy]);

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
    </div>
</div></div></div>
