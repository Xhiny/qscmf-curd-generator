<?php

namespace CurdGen\Operate;


class AddTopButton implements ICurrentPage, INewPage
{

    public function currentTableButtonParse(){
        return <<<sample
->addTopButton('modal', ['title' => '新增'],'','',\$this->buildAddModal())
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

    public function newTableButtonParse(){
        return <<<sample
->addTopButton('addnew')
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
        return '';
    }

    public function newMetaTitleParse(){
        return '新增{DummyModelTitle}';
    }


}