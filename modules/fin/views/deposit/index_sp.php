<?php
    use yii\bootstrap\ActiveForm;
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\jui\DatePicker;
    use yii\widgets\Pjax;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;

    $this->title = Yii::t('fin.deposit', 'Time Deposit Accounts List');
    $phpFmShortDateGui = 'php:' . $phpFmShortDate;

    $htmlFooterInterest = '<span class="label label-info">' . NumberUtils::format($sumTimeDepositValue['interest_add']) . '</span>';
    $htmlFooterInterest .= '<br/><span class="label label-success">' . NumberUtils::format($sumTimeDepositValue['adding_value'] - $sumTimeDepositValue['withdrawal_value']) . '</span>';
    $htmlFooterAdding = '<span class="label label-danger">' . NumberUtils::format($sumTimeDepositValue['withdrawal_value']) . '</span>';
    $htmlFooterAdding .= '<br/><span class="label label-info">' . NumberUtils::format($sumTimeDepositValue['adding_value']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('fin.deposit', 'Transaction'); ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($searchModel, 'opening_date_from')->widget(DatePicker::className(), [
                'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
                    'class'=>'form-control'
                ]
            ]); ?>
            <?= $form->field($searchModel, 'opening_date_to')->widget(DatePicker::className(), [
                'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
                    'class'=>'form-control'
                ]
            ]); ?>
            <?= $form->field($searchModel, 'saving_account')->dropDownList($arrSavingAccount, ['prompt'=>'']); ?>
            <?= $form->field($searchModel, 'current_assets')->dropDownList($arrCurrentAssets, ['prompt'=>'']); ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
    <div class="box-body-notool"><div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
        'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
        'options'=>['class'=>'grid-view col-xs-12'],
        'tableOptions'=>['class'=>'table table-bordered'],
        'showFooter'=>true,
        'footerRowOptions'=>['style'=>'font-weight:bold'],
        'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
        'dataProvider'=>new ActiveDataProvider([
            'query'=>$dataQuery,
            'pagination'=>['pagesize'=>10]
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
                    $html .= '<br/>' . str_pad($model->transactions_id, 6, '0', STR_PAD_LEFT);

                    return $html;
                },
                'footer'=>Yii::t('fin.grid', 'Total')
            ],
            [
                'attribute'=>'entry_date',
                'label'=>Yii::t('fin.grid', 'Opening'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model) use ($phpFmShortDate) {
                    $html = DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE, true);

                    $lblView = Yii::t('button', 'View');
                    $lblEdit = Yii::t('button', 'Edit');
                    $lblCopy = Yii::t('button', 'Copy');
                    $arrBtns = [];

                    $entryId = $model->transactions_id;
                    $urlEdit = BaseUrl::toRoute(['deposit/update', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                    $urlView = BaseUrl::toRoute(['deposit/view', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

                    $urlCopy = BaseUrl::toRoute(['deposit/copy', 'id'=>$entryId]);
                    $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlCopy, $lblCopy]);

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
                'footer'=>$htmlFooterInterest
            ],
            [
                'label'=>Yii::t('fin.grid', 'Principal'),
                'headerOptions'=>['style'=>'text-align: center'],
                'footerOptions'=>['style'=>'text-align: right'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: left; min-width:162px'],
                'format'=>'raw',
                'value'=>function($model) use ($arrSavingAccount, $arrCurrentAssets) {
                    $htmls = [];

                    $htmlSavingAccount = isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : '';
                    if (!empty($htmlSavingAccount)) {
                        $amount = NumberUtils::format($model->interest_add);
                        $htmlSavingAccount .= '<span class="label label-info pull-right">' . $amount . '</span>';
                        $htmls[] = $htmlSavingAccount;
                    }

                    $htmlCurrentAssets = isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : '';
                    if (!empty($htmlCurrentAssets)) {
                        $amount = NumberUtils::format($model->entry_value);
                        if ($model->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING) {
                            $htmlCurrentAssets .= '<span class="label label-info pull-right">' . $amount . '</span>';
                        } else {
                            $htmlCurrentAssets .= '<span class="label label-danger pull-right">' . $amount . '</span>';
                        }
                        $htmls[] = $htmlCurrentAssets;
                    }

                    return implode('<br/>', $htmls);
                },
                'footer'=>$htmlFooterAdding
            ],
            [
                'label'=>Yii::t('fin.grid', 'Unit'),
                'headerOptions'=>['style'=>'text-align: center'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: right'],
                'format'=>'raw',
                'value'=>function($model) {
                    $interestRate = NumberUtils::format($model->interest_rate, 4) . ' %';
                    $interestUnit = NumberUtils::format($model->interest_unit, 4) . ' d';
                    return $interestRate . '<br/>' . $interestUnit;
                }
            ],
            [
                'label'=>Yii::t('fin.grid', 'Closing'),
                'headerOptions'=>['style'=>'text-align: center'],
                'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                'format'=>'raw',
                'value'=>function($model) {
                    return DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE, true);
                }
            ]
        ]
    ]); ?><?php Pjax::end(); ?></div></div>
</div></div></div>