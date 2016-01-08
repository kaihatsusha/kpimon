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

    $this->title = Yii::t('net.payment', 'Payments List');

    $htmlFooterCredit = '<span class="label label-info">' . NumberUtils::format($sumEntryValue['credit']) . '</span>';
    $htmlFooterDebit = '<span class="label label-danger">' . NumberUtils::format($sumEntryValue['debit']) . '</span>';
    $htmlFooterBalance = '<span class="label label-success">' . NumberUtils::format($sumEntryValue['credit'] - $sumEntryValue['debit']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('net.payment', 'Transaction'); ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($searchModel, 'customer_id')->dropDownList($arrNetCustomer, ['prompt'=>'']); ?>
            <?= $form->field($searchModel, 'entry_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($searchModel, 'entry_date_to')->widget(DateTimePicker::className(), ['type'=>1,
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
                'label'=>Yii::t('fin.grid', 'Customer'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model, $key, $index, $column) use ($arrNetCustomer) {
                    $pagination = $column->grid->dataProvider->pagination;
                    $html = [$pagination->page * $pagination->pageSize + $index + 1];
                    if (isset($arrNetCustomer[$model->customer_id])) {
                        $html[] = $arrNetCustomer[$model->customer_id];
                    }

                    return implode('<br/>', $html);
                },
                'footer'=>$htmlFooterBalance
            ],
            [
                'label'=>Yii::t('fin.grid', 'Date'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model) {
                    $html = DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);

                    $lblView = Yii::t('button', 'View');
                    $lblEdit = Yii::t('button', 'Edit');
                    $arrBtns = [];

                    $entryId = $model->secret_key;
                    $urlEdit = BaseUrl::toRoute(['payment/update', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                    $urlView = BaseUrl::toRoute(['payment/view', 'id'=>$entryId]);
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
                'footer'=>$htmlFooterCredit
            ],
            [
                'label'=>Yii::t('fin.grid', 'Credit / Debit'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
                'format'=>'raw',
                'value'=>function($model) {
                    $htmls = [];

                    if ($model->credit > 0) {
                        $htmls[] = '<span class="label label-info pull-right">' . NumberUtils::format($model->credit) . '</span>';
                    }
                    if ($model->debit > 0) {
                        $htmls[] = '<span class="label label-danger pull-right">' . NumberUtils::format($model->debit) . '</span>';
                    }

                    return implode('<br/>', $htmls);
                },
                'footer'=>$htmlFooterDebit
            ],
            [
                'label'=>Yii::t('fin.grid', 'Bill Date'),
                'headerOptions'=>['style'=>'text-align: center'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model) {
                    if ($model->order_id > 0 && !is_null($model->bill_date)) {
                        $urlEdit = BaseUrl::toRoute(['bill/update', 'id'=>$model->order_id]);
                        return StringUtils::format('<a href="{0}">{1}</a>', [$urlEdit, DateTimeUtils::htmlDateFormatFromDB($model->bill_date, DateTimeUtils::FM_VIEW_DATE, true)]);
                    }
                    return '';
                }
            ]
        ]
    ]); ?><?php Pjax::end(); ?></div></div>
</div></div></div>
