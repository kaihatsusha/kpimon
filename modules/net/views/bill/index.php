<?php
    use yii\bootstrap\ActiveForm;
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    //use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    //use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    //use app\components\NumberUtils;
    //use app\components\StringUtils;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('net.bill', 'Bills List');
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('net.bill', 'Transaction'); ?></h3>
    </div>
    <div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($searchModel, 'bill_date_from')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($searchModel, 'bill_date_to')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
            </div>
            <div class="col-md-12"><div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div></div>
        </div>
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'headerRowOptions'=>['class'=>'warning'],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>30]
            ]),
        ]); ?><?php Pjax::end(); ?></div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>