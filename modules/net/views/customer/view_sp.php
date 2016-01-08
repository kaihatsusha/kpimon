<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.customer', 'Details of Customer');
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <?php
        $htmlBalance = NumberUtils::getIncDecNumber($model->balance, ['template'=>'<span class="pull-right badge {color}">{number}</span>', 'incColor'=>'bg-aqua', 'decColor'=>'bg-red']);
        $htmlStatus = isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : '';

        $htmlFooterCredit = NumberUtils::format($sumPaymentValue['credit']);
        $htmlFooterDebit = NumberUtils::format($sumPaymentValue['debit']);
        $htmlFooterBalance = NumberUtils::format($sumPaymentValue['credit'] - $sumPaymentValue['debit']);
    ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('name'); ?>
                <span class="pull-right"><?= $model->name; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                    <?= $model->getAttributeLabel('balance'); ?>
                    <?= $htmlBalance; ?>
                </a></li>
            <li><a href="javascript:void(0);">
                    <?= $model->getAttributeLabel('status'); ?>
                    <span class="pull-right"><?= $htmlStatus; ?></span>
                </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('description'); ?>
                <span class="pull-right"><?= $model->description; ?></span>
            </a></li>
        </ul>
    </div>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Payment History'); ?></h3></div>
    <div class="box-footer"><div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
        'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
        'options'=>['class'=>'grid-view col-xs-12'],
        'tableOptions'=>['class'=>'table table-bordered'],
        'showFooter'=>true,
        'headerRowOptions'=>['class'=>'warning'],
        'footerRowOptions'=>['class'=>'warning', 'style'=>'font-weight:bold'],
        'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
        'dataProvider'=>new ActiveDataProvider([
            'query'=>$dataPaymentQuery,
            'pagination'=>['pagesize'=>20]
        ]),
        'columns'=>[
            [
                'label'=>Yii::t('fin.grid', 'Date'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>function($model, $key, $index) {
                    return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                },
                'format'=>'raw',
                'value'=>function($model) {
                    return DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);
                },
                'footer'=>$htmlFooterBalance
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
                'headerOptions'=>['style'=>'text-align: center'],
                'contentOptions'=>function($model, $key, $index) {
                    return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                },
                'format'=>'raw',
                'value'=>function($model) {
                    if ($model->order_id > 0 && !is_null($model->bill_date)) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->bill_date, DateTimeUtils::FM_VIEW_DATE, true);
                    }
                    return '';
                }
            ]
        ]
    ]); ?><?php Pjax::end(); ?></div></div>
</div></div></div><?php endif; ?>