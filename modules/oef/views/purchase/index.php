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

    $this->title = Yii::t('oef.purchase', 'Purchases List');

    $htmlFooterRequest = NumberUtils::format($sumPurchaseValue['purchase']);
    $htmlFooterPurchaseFee = NumberUtils::format($sumPurchaseValue['purchase_fee']);
    $htmlFooterFoundStockSold = NumberUtils::format($sumPurchaseValue['found_stock_sold'], 2);
    $htmlFooterFoundStock = NumberUtils::format($sumPurchaseValue['found_stock'], 2);
    $htmlFooterTransferFee = NumberUtils::format($sumPurchaseValue['transfer_fee']);
    $htmlFooterRealPurchase = NumberUtils::format($sumPurchaseValue['purchase'] - $sumPurchaseValue['purchase_fee']);
    $otherFee = $sumPurchaseValue['transfer_fee'] + $sumPurchaseValue['other_fee'];
    $htmlFooterOtherFee = NumberUtils::format($otherFee);
    $htmlFooterInvestment = NumberUtils::format($sumPurchaseValue['purchase'] + $otherFee);
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('oef.purchase', 'Transaction'); ?></h3>
    </div>
    <div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($searchModel, 'purchase_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'purchase_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6"><div class="form-group">
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
                    'footerOptions'=>['style'=>'text-align: right', 'colspan'=>3],
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
                    'label'=>Yii::t('fin.grid', 'Reference'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['colspan'=>0],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return str_pad($model->id, 6, '0', STR_PAD_LEFT);
                    }
                ],
                [
                    'class'=>DataColumn::className(),
                    'label'=>Yii::t('fin.grid', 'Transaction'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['colspan'=>0],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->purchase_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Type'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) use ($arrPurchaseType) {
                        $html = $arrPurchaseType[$model->purchase_type];
                        $html .= is_null($model->sip_date) ? '' : ' ' . DateTimeUtils::htmlDateFormatFromDB($model->sip_date, DateTimeUtils::FM_VIEW_DATE, true);
                        $html .= '<br/>' . NumberUtils::format($model->purchase);
                        return $html;
                    },
                    'footer'=>$htmlFooterRequest
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Fee Rate'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        $totalFeeRate = $model->purchase_fee_rate * (100 - $model->discount_rate) / 100;
                        $html = NumberUtils::format($totalFeeRate, 2);
                        $html .= ' %<br/>' . NumberUtils::format($model->purchase_fee);
                        return $html;
                    },
                    'footer'=>$htmlFooterPurchaseFee
                ],
                [
                    'label'=>Yii::t('fin.grid', 'NAV / Real'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        $html = NumberUtils::format($model->nav, 2);
                        $html .= '<br/>' . NumberUtils::format($model->purchase - $model->purchase_fee);
                        return $html;
                    },
                    'footer'=>$htmlFooterRealPurchase
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Stock'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->found_stock, 2);
                    },
                    'footer'=>$htmlFooterFoundStock
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Sold'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->found_stock_sold, 2);
                    },
                    'footer'=>$htmlFooterFoundStockSold
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Other Fee'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->transfer_fee + $model->other_fee);
                    },
                    'footer'=>$htmlFooterOtherFee
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Investment'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return NumberUtils::format($model->purchase + $model->transfer_fee + $model->other_fee);
                    },
                    'footer'=>$htmlFooterInvestment
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

                        $entryId = $model->id;
                        $urlEdit = BaseUrl::toRoute(['purchase/update', 'id'=>$model->id]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);
                        $urlView = BaseUrl::toRoute(['purchase/view', 'id'=>$entryId]);
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