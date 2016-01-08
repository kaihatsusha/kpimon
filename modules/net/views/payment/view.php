<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.payment', 'Details of Payment');
?>

<?php if($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('customer_id'); ?></th>
                <td class="info"><?= isset($arrNetCustomer[$model->customer_id]) ? $arrNetCustomer[$model->customer_id] : ''; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('entry_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <?php if($model->credit > 0): ?><tr>
                <th class="warning"><?= $model->getAttributeLabel('credit'); ?></th>
                <td class="info"><?= NumberUtils::format($model->credit); ?></td>
            </tr><?php endif; ?>
            <?php if($model->debit > 0): ?><tr>
                <th class="warning"><?= $model->getAttributeLabel('debit'); ?></th>
                <td class="info"><?= NumberUtils::format($model->debit); ?></td>
            </tr></tr><?php endif; ?>
        </table>
    </div></div></div>
</div><?php endif; ?>