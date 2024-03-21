<?php

namespace CurdGen\Mode;

class FloatMode extends AMode
{
    protected $mode = 'float';

    public function injectAddTopButton(){
        return <<<sample
->addTopButton('modal', ['title' => '新增'],'','',\$this->buildAddModal())
sample;

    }

    public function injectEditRightButton(){
        return <<<sample
->addRightButton('modal',['title' => '编辑'], '', '', 'list_edit_form')
sample;

    }

    public function funSuccessJumpUrl(){
        return <<<sample
javascript:location.reload();
sample;

    }

    public function funFormDisplay(){
        return <<<sample
                ->setShowBtn(false);
                
            return \$builder;
sample;

    }

    public function editMetaTitle(){
        return '';
    }

    public function addMetaTitle(){
        return '';
    }

    public function editExtraFun(){
        return <<<fun
    protected function buildEditModal(\$id=null){
        \$modal = (new \Qs\ModalButton\ModalButtonBuilder());
        return
            \$modal
                ->setTitle('编辑{DummyModelTitle}')
                ->setBackdrop(false)
                ->setKeyboard(false)
                ->bindFormBuilder(\$this->edit(\$id));
    }
fun;
    }

    public function addExtraFun(){
        return <<<fun
    protected function buildAddModal(){
        \$modal = (new \Qs\ModalButton\ModalButtonBuilder());
        return
            \$modal
                ->setTitle('新增{DummyModelTitle}')
                ->setBackdrop(false)
                ->setKeyboard(false)
                ->bindFormBuilder(\$this->add());
    }
fun;
    }

    public function tableBtnPlaceholder(){
        return <<<sample
    \$data['list_edit_form'] = \$this->buildEditModal(\$data['id']);
sample;
    }
}