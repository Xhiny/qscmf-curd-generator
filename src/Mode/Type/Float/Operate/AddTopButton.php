<?php

namespace CurdGen\Mode\Type\Float\Operate;


class AddTopButton extends \CurdGen\Operate\AddTopButton implements IOperate
{

    public function topButtonParse(){
        return <<<sample
->addTopButton('modal', ['title' => '新增'],'','',\$this->buildAddModal())
sample;
    }

    public function modalFunParse()
    {
        return <<<fun
    protected function buildAddModal(){
        \$modal = (new \Qs\ModalButton\ModalButtonBuilder());
        return
            \$modal
                ->setTitle('新增{DummyModelTitle}')
                ->setBackdrop(false)
                ->setKeyboard(false)
                ->setBody(\$this->add());
    }
fun;

    }

    public function tableBtnPlaceholderParse(){
        return '';
    }

    public function operateParse()
    {
        return <<<sample
{DummyAddModal}

    public function add(){
        if (IS_POST) {
            parent::autoCheckToken();
            \$data = I('post.');

            \$model = D('{DummyModel}');
            \$r = \$model->createAdd(\$data);
            if(\$r === false){
                \$this->error(\$model->getError());
            }
            else{
                sysLogs('新增{DummyModelTitle}, id:' . \$r);


                \$this->success(l('add') . l('success'), 'javascript:location.reload();');
            }
        }
        else {
            \$builder = new \Qscmf\Builder\FormBuilder();

            \$data_list = array(
                "status"=>1
            );

            if(\$data_list){
                \$builder->setFormData(\$data_list);
            }

            {DummyFormExtra}
            \$builder
                ->setPostUrl(U('add'))
                {DummyFormColumns}
                ->setShowBtn(false);
                
            return \$builder->display(true);
        }
    }
sample;

    }



}