<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('oef.nav', 'Details of Nav');
?>

<?php if($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('nav_id'); ?></th>
                <td class="info"><?= $model->nav_id; ?></td>
            </tr>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('trade_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->trade_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('decide_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->decide_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav_value, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav_value_prev'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav_value_prev, 2); ?></td>
            </tr>
        </table>
    </div></div></div>
</div><?php endif; ?>