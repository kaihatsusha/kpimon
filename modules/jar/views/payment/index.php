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

    $this->title = Yii::t('jar.payment', 'Payments List');
    $htmlFooterDebit = '<span class="text-danger">' . NumberUtils::format($sumEntryValue['debit']) . '</span>';
    $htmlFooterCredit = '<span class="text-info">' . NumberUtils::format($sumEntryValue['credit']) . '</span>';
    $htmlFooterCreditBalance = '<span class="text-success">' . NumberUtils::format($sumEntryValue['credit'] - $sumEntryValue['debit']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('jar.payment', 'Transaction'); ?></h3>
    </div>
    <div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($searchModel, 'entry_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
                <?= $form->field($searchModel, 'account_source')->dropDownList($arrAccount, ['prompt'=>'']); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'entry_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
                <?= $form->field($searchModel, 'account_target')->dropDownList($arrAccount, ['prompt'=>'']); ?>
            </div>
            <div class="col-md-6"><div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div></div>
            <div class="col-md-6"><table class="table table-bordered"><thead><tr>
                <th class="warning" style="text-align: left; width: 120px"><?= Yii::t('fin.grid', 'This Month'); ?></th>
                <th class="info" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['credit']); ?></th>
                <th class="danger" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['debit']); ?></th>
                <th class="success" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['credit'] - $sumCurrentMonthData['debit']); ?></th>
            </tr></thead></table></div>
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
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
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
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'value'=>function($model) {
                        return str_pad($model->id, 6, '0', STR_PAD_LEFT);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Transaction Date'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        return DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);
                    },
                    'footer'=>$htmlFooterCreditBalance
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Debit Account'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'value'=>function($model) use ($arrAccount) {
                        return isset($arrAccount[$model->account_source]) ? $arrAccount[$model->account_source] : '';
                    },
                    'footer'=>$htmlFooterDebit
                ],
                [
                    'class'=>DataColumn::className(),
                    'label'=>Yii::t('fin.grid', 'Debit Amount'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['colspan'=>0],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'value'=>function($model) {
                        $amount = $model->account_source == 0 ? '' : NumberUtils::format($model->entry_value);
                        return $amount;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Credit Account'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'value'=>function($model) use ($arrAccount) {
                        return isset($arrAccount[$model->account_target]) ? $arrAccount[$model->account_target] : '';
                    },
                    'footer'=>$htmlFooterCredit
                ],
                [
                    'class'=>DataColumn::className(),
                    'label'=>Yii::t('fin.grid', 'Credit Amount'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['colspan'=>0],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'value'=>function($model) {
                        $amount = $model->account_target == 0 ? '' : NumberUtils::format($model->entry_value);
                        return $amount;
                    }
                ],
                [
                    'attribute'=>'description',
                    'label'=>Yii::t('fin.grid', 'Description'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'enableSorting'=>false
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Action'),
                    'headerOptions'=>['style'=>'text-align: center; width: 100px;'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'danger' : MasterValueUtils::getColorRow($index))];
                    },
                    'format'=>'raw',
                    'value'=>function($model, $key, $index) {
                        $btnClass = MasterValueUtils::getColorRow($index);
                        $lblView = Yii::t('button', 'View');
                        $lblEdit = Yii::t('button', 'Edit');
                        $urlEdit = null;
                        $arrBtns = [];

                        $entryId = $model->id;
                        if ($model->share_id > 0) {
                            $urlEdit = BaseUrl::toRoute(['distribute/update', 'id'=>$model->share_id]);
                        } else {
                            $urlEdit = BaseUrl::toRoute(['payment/update', 'id'=>$entryId]);
                        }
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