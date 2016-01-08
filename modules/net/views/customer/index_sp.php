<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    use app\components\NumberUtils;
    use app\components\StringUtils;

    $this->title = Yii::t('net.customer', 'Customers List');
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('net.customer', 'Customers'); ?></h3>
    </div>
    <div class="box-body-notool">
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
            'options'=>['class'=>'grid-view col-xs-12'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'showFooter'=>true,
            'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>10]
            ]),
            'columns'=>[
                [
                    'label'=>Yii::t('fin.grid', 'Ref'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        $html = $pagination->page * $pagination->pageSize + $index + 1;
                        $html .= '<br/>' . str_pad($model->id, 6, '0', STR_PAD_LEFT);

                        return $html;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Status'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
                    'format'=>'raw',
                    'value'=>function($model) use ($arrCustomerStatus) {
                        $status = isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : '';
                        $html = '<span>' . $status . '</span>';

                        $lblView = Yii::t('button', 'View');
                        $lblEdit = Yii::t('button', 'Edit');
                        $arrBtns = [];

                        $entryId = $model->id;
                        $urlEdit = BaseUrl::toRoute(['customer/update', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                        $urlView = BaseUrl::toRoute(['customer/view', 'id'=>$entryId]);
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
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Balance'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'footerOptions'=>['style'=>'vertical-align: middle; text-align: right'],
                    'contentOptions'=>['style'=>'vertical-align: middle; text-align: right'],
                    'format'=>'raw',
                    'value'=>function($model) {
                        $labelClass = $model->balance < 0 ? 'label-danger' : 'label-info';
                        $labelValue = NumberUtils::format(abs($model->balance));
                        return StringUtils::format('{2}<br/><span class="label {0} pull-right">{1}</span>', [$labelClass, $labelValue, $model->name]);
                    },
                    'footer'=>NumberUtils::getIncDecNumber($sumCustomerValue['balance'], ['template'=>'<span class="label pull-right {color}">{number}</span>', 'incColor'=>'label-info', 'decColor'=>'label-danger'])
                ]
            ]
        ]); ?><?php Pjax::end(); ?></div>
    </div>
</div></div></div>