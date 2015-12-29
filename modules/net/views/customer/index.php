<?php
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\BaseUrl;
    use yii\helpers\Html;
    use yii\widgets\Pjax;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;

    $this->title = Yii::t('net.customer', 'Customers List');
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('net.customer', 'Customers'); ?></h3>
    </div>
    <div class="box-body">
        <div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
            'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
            'tableOptions'=>['class'=>'table table-bordered'],
            'headerRowOptions'=>['class'=>'warning'],
            'dataProvider'=>new ActiveDataProvider([
                'query'=>$dataQuery,
                'pagination'=>['pagesize'=>10]
            ]),
            'columns'=>[
                [
                    'label'=>Yii::t('fin.grid', 'No.'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->pagination;
                        return $pagination->page * $pagination->pageSize + $index + 1;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'ID'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return str_pad($model->id, 6, '0', STR_PAD_LEFT);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Name'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return $model->name;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Balance'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model) {
                        $htmlConfig = ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red'];
                        return NumberUtils::getIncDecNumber($model->balance, $htmlConfig);
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Status'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) use ($arrCustomerStatus) {
                        return isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : '';
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Description'),
                    'headerOptions'=>['style'=>'text-align: center'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'value'=>function($model) {
                        return $model->description;
                    }
                ],
                [
                    'label'=>Yii::t('fin.grid', 'Action'),
                    'headerOptions'=>['style'=>'text-align: center; width: 100px;'],
                    'contentOptions'=>function($model, $key, $index) {
                        return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
                    },
                    'format'=>'raw',
                    'value'=>function($model, $key, $index) {
                        $btnClass = MasterValueUtils::getColorRow($index);
                        $lblView = Yii::t('button', 'View');
                        $lblEdit = Yii::t('button', 'Edit');
                        $arrBtns = [];

                        $entryId = $model->id;
                        $urlEdit = BaseUrl::toRoute(['customer/update', 'id'=>$entryId]);
                        $arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

                        $urlView = BaseUrl::toRoute(['customer/view', 'id'=>$entryId]);
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
    </div>
</div></div></div>