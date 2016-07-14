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

    $this->title = Yii::t('jar.payment', 'Payments List');

    $htmlFooterCreditDebit = '';
    $htmlFooterCreditDebit .= '<span class="label label-danger pull-left">' . NumberUtils::format($sumEntryValue['debit']) . '</span>';
    $htmlFooterCreditDebit .= '<span class="label label-success pull-right">' . NumberUtils::format($sumEntryValue['credit'] - $sumEntryValue['debit']) . '</span>';

    $htmlFooterDate = '<span class="label label-info pull-left">' . NumberUtils::format($sumEntryValue['credit']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('jar.payment', 'Transaction'); ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($searchModel, 'entry_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($searchModel, 'entry_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($searchModel, 'account_source')->dropDownList($arrAccount, ['prompt'=>'']); ?>
            <?= $form->field($searchModel, 'account_target')->dropDownList($arrAccount, ['prompt'=>'']); ?>
            <?= $form->field($searchModel, 'entry_status')->inline(true)->checkboxList($arrEntryLog); ?>
            <?= $form->field($searchModel, 'description')->textInput(); ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
    <div class="box-body-notool">
        <div class="row"><div class="col-md-12"><table class="table"><thead><tr>
            <th style="text-align: right;"><span class="badge bg-aqua"><?= NumberUtils::format($sumCurrentMonthData['credit']); ?></span></th>
            <th style="text-align: right;"><span class="badge bg-red"><?= NumberUtils::format($sumCurrentMonthData['debit']); ?></span></th>
            <th style="text-align: right;"><span class="badge bg-green"><?= NumberUtils::format($sumCurrentMonthData['credit'] - $sumCurrentMonthData['debit']); ?></span></th>
        </tr></thead></table></div></div>
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
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
                    'label'=>Yii::t('fin.grid', 'Ref'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        $html = $pagination->page * $pagination->pageSize + $index + 1;
                        $html .= '<br/>' . str_pad($model->id, 6, '0', STR_PAD_LEFT);
                        $html = '<span class="' . ($model->entry_status == MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP ? 'text-red' : '') . '">' . $html . '<span>';

                        return $html;
                    },
                    'footer'=>Yii::t('fin.grid', 'Total')
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
                    'footer'=>$htmlFooterDate
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Credit / Debit'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'text-align: right'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: left; min-width:162px'],
                    'format'=>'raw',
                    'value'=>function($model) use ($arrAccount) {
                        $htmls = [];

                        $htmlCredit = isset($arrAccount[$model->account_target]) ? $arrAccount[$model->account_target] : '';
                        if (!empty($htmlCredit)) {
                            $amount = $model->account_target == 0 ? '' : NumberUtils::format($model->entry_value);
                            $htmlCredit .= '<span class="label label-info pull-right">' . $amount . '</span>';
                            $htmls[] = $htmlCredit;
                        }

                        $htmlDebit = isset($arrAccount[$model->account_source]) ? $arrAccount[$model->account_source] : '';
                        if (!empty($htmlDebit)) {
                            $amount = $model->account_source == 0 ? '' : NumberUtils::format($model->entry_value);
                            $htmlDebit .= '<span class="label label-danger pull-right">' . $amount . '</span>';
                            $htmls[] = $htmlDebit;
                        }

                        return implode('<br/>', $htmls);
                    },
                    'footer'=>$htmlFooterCreditDebit
                ],
                [
                    'attribute'=>'description',
                    'label'=>Yii::t('fin.grid', 'Description'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
                    'enableSorting'=>false
                ]
            ]
        ]); ?><?php Pjax::end(); ?></div>
    </div>
</div></div></div>