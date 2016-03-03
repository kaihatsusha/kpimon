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
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('jar.distribute', 'Distributed Items List');
    $htmlFooterShareValue = ($sumShareValue) ? '<span class="label label-info pull-right">' . NumberUtils::format($sumShareValue['share_value']) . '</span>' : '';
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div c<div class="box-header">
        <h3 class="box-title"><?= Yii::t('jar.distribute', 'Transaction'); ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($searchModel, 'share_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($searchModel, 'share_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
    <div class="box-body-notool"><div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
        'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
        'options'=>['class'=>'grid-view col-xs-12'],
        'tableOptions'=>['class'=>'table table-bordered'],
        'showFooter'=>true,
        'footerRowOptions'=>['style'=>'font-weight:bold'],
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
                    $html .= '<br/>' . str_pad($model->share_id, 6, '0', STR_PAD_LEFT);

                    return $html;
                }
            ],
            [
                'label'=>Yii::t('fin.grid', 'Date'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model) {
                    $html = DateTimeUtils::htmlDateFormatFromDB($model->share_date, DateTimeUtils::FM_VIEW_DATE, true);

                    $lblView = Yii::t('button', 'View');
                    $lblEdit = Yii::t('button', 'Edit');
                    $arrBtns = [];

                    $entryId = $model->share_id;
                    $urlEdit = BaseUrl::toRoute(['distribute/update', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                    $urlView = BaseUrl::toRoute(['distribute/view', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

                    $html .= '<br/><div class="btn-group">';
                    $html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-xs btn-info']);
                    $html .= '<button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">';
                    $html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
                    $html .= '</button>';
                    $html .= '<ul class="dropdown-menu" role="menu">';
                    $html .= implode('', $arrBtns);
                    $html .= '</ul></div>';

                    return $html;
                },
                'footer'=>Yii::t('fin.grid', 'Total')
            ],
            [
                'label'=>Yii::t('fin.grid', 'Share'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
                'format'=>'raw',
                'value'=>function($model) {
                    return '<span class="label label-info pull-right">' . NumberUtils::format($model->share_value) . '</span>';
                },
                'footer'=>$htmlFooterShareValue
            ],
            [
                'label'=>Yii::t('fin.grid', 'Description'),
                'headerOptions'=>['style'=>'text-align: center'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
                'value'=>function($model) {
                    return $model->description;
                }
            ]
        ]
    ]); ?><?php Pjax::end(); ?></div></div>
</div></div></div>