<?php
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Alert;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('net.bill', 'Create Bill');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('net.bill', 'Edit Bill');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('net.bill', 'Copy Bill');
    }
    $rowindex = 0;

    $memberCount = count($model->arr_member_list);
    $pricePerMember = NumberUtils::rounds($model->total / $memberCount, NumberUtils::NUM_CEIL);
?>

<div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div id="netBillConfirmForm" class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('bill_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->bill_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('total'); ?></th>
                <td class="info"><?= NumberUtils::format($model->total); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('arr_member_list'); ?></th>
                <td class="info"><?= StringUtils::showArrValueAsString($model->arr_member_list, $arrNetCustomer); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('common', 'Number Of Member'); ?></th>
                <td class="info"><?= $memberCount; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('common', 'Price Per Member'); ?></th>
                <td class="info"><?= NumberUtils::format($pricePerMember); ?></td>
            </tr>
        </table>
        <div style="display: none">
            <?= $form->field($model, 'bill_date')->hiddenInput(); ?>
            <?= $form->field($model, 'arr_member_list')->checkboxList($arrNetCustomer); ?>
        </div>
    </div></div></div>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-body">
        <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2">
            <?php if(isset($isValid) && is_string($isValid)): ?>
                <?= Alert::widget(['body'=>$isValid, 'options'=>['class'=>'alert alert-error']])?>
            <?php endif; ?>
            <table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center; width: 70px"><?= Yii::t('fin.grid', 'No.'); ?></th>
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
                    <th style="text-align: center; width: 120px"><?= Yii::t('fin.grid', 'Price'); ?></th>
                    <th style="text-align: center; width: 160px"><?= Yii::t('fin.grid', 'Pay Date'); ?></th>
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Description'); ?></th>
                </tr></thead>
                <tbody><?php foreach($arrBillDetail as $i=>$item): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $btnClass = MasterValueUtils::getColorRow($rowindex);
                        if ($item->delete_flag) {
                            $style = 'style="display:none;"';
                        } else {
                            $style = '';
                            $rowindex++;
                        }

                        // show error columns
                        $colItemNameStyle = '';
                        $colPriceStyle = '';
                        $colPayDateStyle = '';
                        $colDescriptionStyle = '';
                        $htmlItemName = $item->item_name;
                        $htmlPrice = is_numeric($item->price) ? NumberUtils::format($item->price) : $item->price;
                        $htmlPayDate = empty($item->pay_date) ? '' : DateTimeUtils::htmlDateFormat($item->pay_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true);
                        $htmlDescription = $item->description;
                        if (!$item->is_valid) {
                            $errorItemName = $item->getErrors('item_name');
                            if (count($errorItemName) > 0) {
                                $colItemNameStyle = ' class="danger"';
                                $htmlItemName = '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="' . $errorItemName[0] . '"></i> ' . $htmlItemName;
                            }
                            $errorPrice = $item->getErrors('price');
                            if (count($errorPrice) > 0) {
                                $colPriceStyle = ' class="danger"';
                                $htmlPrice = '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="' . $errorPrice[0] . '"></i> ' . $htmlPrice;
                            }
                            $errorPayDate = $item->getErrors('pay_date');
                            if (count($errorPayDate) > 0) {
                                $colPayDateStyle = ' class="danger"';
                                $htmlPayDate = '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="' . $errorPayDate[0] . '"></i> ' . $htmlPayDate;
                            }
                            $errorDescription = $item->getErrors('description');
                            if (count($errorDescription) > 0) {
                                $colDescriptionStyle = ' class="danger"';
                                $htmlDescription = '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="' . $errorDescription[0] . '"></i> ' . $htmlDescription;
                            }
                        }
                    ?>
                    <tr class="<?= $class; ?>" <?= $style; ?>>
                        <td style="vertical-align: middle; text-align: center">
                            <?= $item->item_no; ?>
                            <span style="display: none">
                                <?= $form->field($item, "[$i]item_no")->hiddenInput(); ?>
                                <?= $form->field($item, "[$i]delete_flag")->hiddenInput(); ?>
                            </span>
                        </td>
                        <td style="vertical-align: middle; text-align: left" <?= $colItemNameStyle; ?>>
                            <?= $htmlItemName; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]item_name")->hiddenInput(); ?></span>
                        </td>
                        <td style="vertical-align: middle; text-align: right" <?= $colPriceStyle; ?>>
                            <?= $htmlPrice; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]price")->hiddenInput(); ?></span>
                        </td>
                        <td style="vertical-align: middle; text-align: center" <?= $colPayDateStyle; ?>>
                            <?= $htmlPayDate; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]pay_date")->hiddenInput(); ?></span>
                        </td>
                        <td style="vertical-align: middle; text-align: left" <?= $colDescriptionStyle; ?>>
                            <?= $htmlDescription; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]description")->hiddenInput(); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div></div>
    </div>
<?php ActiveForm::end(); ?></div>