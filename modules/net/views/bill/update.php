<?php
    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Alert;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;
    use app\modules\net\views\MoreAsset;
    use app\widgets\net\BillItemModal;
    use kartik\datetime\DateTimePicker;

    // css & js
    if ($model) {
        MoreAsset::$CONTEXT = ['css'=>['css/net/bill.css'], 'js'=>['js/net/bill.js']];
        MoreAsset::register($this);
    }

    $this->title = Yii::t('net.bill', 'Edit Bill');
    $rowindex = 0;
?>

<?php if ($model): ?><div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div id="netBillUpdateForm" class="box-body"><div class="row"><div class="col-md-12">
        <?= $form->field($model, 'bill_date')->widget(DateTimePicker::className(), ['type'=>1,
            'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
        ]); ?>
        <?= $form->field($model, 'arr_member_list')->inline(true)->checkboxList($arrNetCustomer); ?>
    </div></div></div>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3>
        <div class="box-tools"><div class="input-group input-group-sm">
            <div class="input-group-btn"><button class='btn btn-info' type='button' onclick="addBillItem();">
                <i class="fa fa-plus"> <?= Yii::t('button', 'Add'); ?></i>
            </button></div>
        </div></div>
    </div>
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
                    <th style="text-align: center; width: 100px;"><?= Yii::t('fin.grid', 'Action'); ?></th>
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

                        $lblDetele = Yii::t('button', 'Delete');
                        $lblEdit = Yii::t('button', 'Edit');
                        $dataset = ['item_no'=>$item->item_no, 'item_name'=>$item->item_name, 'price'=>$item->price,
                            'pay_date'=>$item->pay_date, 'description'=>$item->description, 'delete_flag'=>$item->delete_flag, 'mode'=>'edit'];
                        $datasetUpdateStr = json_encode($dataset, JSON_NUMERIC_CHECK);
                        $dataset['msgDeleteConfirm'] = Yii::t('message', 'Do you want to delete this item ?');
                        $dataset['mode'] = 'delete';
                        $datasetDeleteStr = json_encode($dataset, JSON_NUMERIC_CHECK);

                        $arrBtns = [];
                        $arrBtns[] = StringUtils::format("<li><a href='javascript:void(0)' onclick='editBillItem(this);' data-maps='{0}'>{1}</a></li>", [$datasetUpdateStr, $lblEdit]);
                        $arrBtns[] = StringUtils::format("<li><a href='javascript:void(0)' onclick='editBillItem(this);' data-maps='{0}'>{1}</a></li>", [$datasetDeleteStr, $lblDetele]);

                        $htmlAction = '<div class="btn-group">';
                        $htmlAction .= StringUtils::format("<a href='javascript:void(0)' onclick='editBillItem(this);' data-maps='{0}' class='btn btn-{1}'>{2}</a>", [$datasetUpdateStr, $btnClass, $lblEdit]);
                        $htmlAction .= '<button type="button" class="btn btn-' . $btnClass . ' dropdown-toggle" data-toggle="dropdown">';
                        $htmlAction .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
                        $htmlAction .= '</button>';
                        $htmlAction .= '<ul class="dropdown-menu" role="menu">';
                        $htmlAction .= implode('', $arrBtns);
                        $htmlAction .= '</ul></div>';

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
                        <td style="vertical-align: middle; text-align: left" <?= $colPayDateStyle; ?>>
                            <?= $htmlPayDate; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]pay_date")->hiddenInput(); ?></span>
                        </td>
                        <td style="vertical-align: middle; text-align: left" <?= $colDescriptionStyle; ?>>
                            <?= $htmlDescription; ?>
                            <span style="display: none"><?= $form->field($item, "[$i]description")->hiddenInput(); ?></span>
                        </td>
                        <td style="vertical-align: middle; text-align: center">
                            <?= $htmlAction; ?>
                        </td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div></div>
    </div>
    <?= BillItemModal::widget(['form'=>$form, 'model'=>$billDetail, 'title'=>Yii::t('fin.form', 'Add An Item'), 'fmShortDateJui'=>$fmShortDateJui]); ?>
<?php ActiveForm::end(); ?></div><?php endif; ?>