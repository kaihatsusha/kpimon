<?php
namespace app\widgets\net;

class BillItemModal extends \yii\base\Widget {
    public $form;
    public $model;
    public $title;
    public $fmShortDateJui;

    public function run() {
        $view = $this->getView();
        BillItemModalAsset::register($view);
        return $this->render('billItemModal', ['form'=>$this->form, 'model'=>$this->model, 'title'=>$this->title, 'fmShortDateJui'=>$this->fmShortDateJui]);
    }
}