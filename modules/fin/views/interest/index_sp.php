<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;
    use app\modules\fin\views\ReportAsset;

    // css & js
    ReportAsset::$CONTEXT = ['js'=>['js/fin/interesIndexSp.js'], 'depends'=>['app\assets\ChartSparklineAsset']];
    ReportAsset::register($this);

    $this->title = Yii::t('fin.interest', 'Interest Unit List');
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('fin.interest', 'Transaction'); ?></h3>
    </div>
    <div class="box-body-notool">
        <script type="text/javascript">
            CHART_DATA = <?= $chartData; ?>;
        </script>
        <div class="row"><div id="interestUnitAreaChart" style="margin-left: 14px; padding-bottom: 10px;"></div></div>
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
            'options'=>['class'=>'grid-view col-xs-12'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>20]
            ]),
            'columns'=>[
                [
                    'label'=>Yii::t('fin.grid', 'Ref'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        $html = $pagination->page * $pagination->pageSize + $index + 1;
                        $html .= '<br/>' . str_pad($model->id, 6, '0', STR_PAD_LEFT);

                        return $html;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Days'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model) {
                        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);
                        $endDate = is_null($model->end_date) ? DateTimeUtils::getNow() : DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
                        $interval = $endDate->diff($startDate);
                        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;

                        $html = '<span>' . $days . '</span>';

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

                        $html .= '<br/><div class="btn-group">';
                        $html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-xs btn-info']);
                        $html .= '<button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">';
                        $html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
                        $html .= '</button>';
                        $html .= '<ul class="dropdown-menu" role="menu">';
                        $html .= implode('', $arrBtns);
                        $html .= '</ul></div>';

                        return $html;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Term'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model) {
                        $html = DateTimeUtils::htmlDateFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE, true);
                        $html = $html . '<br/>';
                        $endDate = is_null($model->end_date) ? DateTimeUtils::getNow()->format(DateTimeUtils::FM_DB_DATE) : $model->end_date;
                        $html = $html . DateTimeUtils::htmlDateFormatFromDB($endDate, DateTimeUtils::FM_VIEW_DATE, true);

                        return $html;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Interest'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: right'],
                    'format'=>'raw',
                    'value'=>function($model) {
                        $html = NumberUtils::format($model->interest_unit, 2);
                        $html = $html . '<br/>';

                        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);
                        $endDate = is_null($model->end_date) ? DateTimeUtils::getNow() : DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
                        $interval = $endDate->diff($startDate);
                        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
                        $html = $html . NumberUtils::format($model->interest_unit * $days, 2);

                        return $html;
                    }
                ]
            ]
        ]); ?><?php Pjax::end(); ?></div>
    </div>
</div></div></div>
