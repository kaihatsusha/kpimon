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
    use app\components\yii2grid\DataColumn;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('net.payment', 'Payments List');

    $htmlFooterDebit = '<span class="text-danger">' . NumberUtils::format($sumEntryValue['debit']) . '</span>';
    $htmlFooterCredit = '<span class="text-info">' . NumberUtils::format($sumEntryValue['credit']) . '</span>';
    $htmlFooterCreditBalance = '<span class="text-success">' . NumberUtils::format($sumEntryValue['credit'] - $sumEntryValue['debit']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('net.payment', 'Transaction'); ?></h3>
    </div>
    <div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($searchModel, 'customer_id')->dropDownList($arrNetCustomer, ['prompt'=>'']); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'entry_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'entry_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-12"><div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div></div>
        </div>
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'showFooter'=>true,
            'headerRowOptions'=>['class'=>'warning'],
            'footerRowOptions'=>['class'=>'warning', 'style'=>'font-weight:bold'],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>20]
            ]),
            'columns'=>[
                [
                    'label'=>Yii::t('fin.grid', 'No.'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        return $pagination->page * $pagination->pageSize + $index + 1;
                    },
                    'footer'=>Yii::t('fin.grid', 'Total')
                ],
                [
                    'class'=>DataColumn::className(),
                    'label'=>Yii::t('fin.grid', 'Transaction Date'),
                    'headerOptions'=>['style'=>'text-align: center; width: 120px;'],
                    'footerOptions'=>['colspan'=>0],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Customer'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) use ($arrNetCustomer) {
                        return isset($arrNetCustomer[$model->customer_id]) ? $arrNetCustomer[$model->customer_id] : '';
                    },
                    'footer'=>$htmlFooterCreditBalance
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Credit'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return ($model->credit > 0) ? NumberUtils::format($model->credit) : '';
                    },
                    'footer'=>$htmlFooterCredit
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Debit'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return ($model->debit > 0) ? NumberUtils::format($model->debit) : '';
                    },
                    'footer'=>$htmlFooterDebit
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Bill Date'),
                    'headerOptions'=>['style'=>'text-align: center; width: 120px;'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        if ($model->order_id > 0 && !is_null($model->bill_date)) {
                            $urlEdit = BaseUrl::toRoute(['bill/update', 'id'=>$model->order_id]);
                            return StringUtils::format('<a href="{0}">{1}</a>', [$urlEdit, DateTimeUtils::htmlDateFormatFromDB($model->bill_date, DateTimeUtils::FM_VIEW_DATE, true)]);
                        }
                        return '';
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
                        $arrBtns = [];

                        $entryId = $model->secret_key;
                        $urlEdit = BaseUrl::toRoute(['payment/update', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                        $urlView = BaseUrl::toRoute(['payment/view', 'id'=>$entryId]);
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
