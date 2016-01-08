<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.customer', 'Details of Customer');
?>

<?php if ($model): ?><div class="box box-default">
    <?php
        $htmlBalance = NumberUtils::getIncDecNumber($model->balance, ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red']);
        $htmlStatus = isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : '';

        $htmlFooterDebit = '<span class="text-danger">' . NumberUtils::format($sumPaymentValue['debit']) . '</span>';
        $htmlFooterCredit = '<span class="text-info">' . NumberUtils::format($sumPaymentValue['credit']) . '</span>';
        $htmlFooterCreditBalance = '<span class="text-success">' . NumberUtils::format($sumPaymentValue['credit'] - $sumPaymentValue['debit']) . '</span>';
    ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('name'); ?></th>
                <td class="info"><?= $model->name; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('balance'); ?></th>
                <td class="info"><?= $htmlBalance; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('status'); ?></th>
                <td class="info"><?= $htmlStatus; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
                <td class="info"><?= $model->description; ?></td>
            </tr>
        </table>
    </div></div></div>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Payment History'); ?></h3></div>
    <div class="box-body"><div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
        'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
        'tableOptions'=>['class'=>'table table-bordered'],
        'showFooter'=>true,
        'headerRowOptions'=>['class'=>'warning'],
        'footerRowOptions'=>['class'=>'warning', 'style'=>'font-weight:bold'],
        'dataProvider'=>new ActiveDataProvider([
            'query'=>$dataPaymentQuery,
            'pagination'=>['pagesize'=>20]
        ]),
        'columns'=>[
            [
                'label'=>Yii::t('fin.grid', 'No.'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
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
                'label'=>Yii::t('fin.grid', 'Transaction Date'),
                'headerOptions'=>['style'=>'text-align: center; width: 120px;'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>function($model, $key, $index) {
                    return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                },
                'format'=>'raw',
                'value'=>function($model) {
                    return DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);
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
</div><?php endif; ?>