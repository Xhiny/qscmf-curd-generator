<?php

namespace CurdGen\Operate;


class EditRightButton implements ICurrentPage, INewPage
{

    public function currentTableButtonParse(){
        return <<<sample
->addRightButton('modal',['title' => '编辑'], '', '', 'list_edit_form')
sample;
    }


    public function currentSuccessJumpUrlParse(){
        return 'javascript:location.reload();';
    }

    public function currentFormDisplayParse(){
        return <<<sample
->setShowBtn(false);
            return \$builder->display(true);
sample;

    }


    public function currentModalFunParse()
    {
        return <<<fun
    protected function buildEditModal(\$id=null){
        \$modal = (new \Qs\ModalButton\ModalButtonBuilder());
        return
            \$modal
                ->setTitle('编辑{DummyModelTitle}')
                ->setBackdrop(false)
                ->setKeyboard(false)
                ->setBody(\$this->edit(\$id));
    }
fun;

    }


    public function newTableButtonParse(){
        return <<<sample
->addRightButton('edit')
sample;
    }


    public function newSuccessJumpUrlParse(){
        return 'javascript:location.href=document.referrer;';
    }


    public function newFormDisplayParse(){
        return <<<sample
        ->display();
sample;

    }

    public function currentTableBtnPlaceholderParse(){
        return <<<sample
    \$data['list_edit_form'] = \$this->buildEditModal(\$data['id']);
sample;
    }


    public function newMetaTitleParse(){
        return '编辑{DummyModelTitle}';
    }
}