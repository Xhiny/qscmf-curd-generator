<?php

namespace CurdGen\Mode\Type\Float\Operate;


class EditRightButton extends \CurdGen\Operate\EditRightButton implements IOperate
{

    public function rightButtonParse(){
        return <<<sample
->addRightButton('modal',['title' => '编辑'], '', '', 'list_edit_form')
sample;
    }

    public function modalFunParse()
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

    public function tableBtnPlaceholderParse(){
        return <<<sample
    \$data['list_edit_form'] = \$this->buildEditModal(\$data['id']);
sample;
    }

    public function operateParse()
    {
        return <<<sample
{DummyEditModal}

    public function edit(\$id=null){
        if (IS_POST) {
            parent::autoCheckToken();
            \$m_id = I('post.id');
            \$data = I('post.');
            \$model = D('{DummyModel}');
            if(!\$m_id){
                E('缺少{DummyModelTitle}ID');
            }

            \$ent = \$model->getOne(\$m_id);
            if(!\$ent){
                E('不存在{DummyModelTitle}');
            }

            {DummyEditColumns}
            if(\$model->createSave(\$ent) === false){
                \$this->error(\$model->getError());
            }
            else{
                sysLogs('修改{DummyModelTitle}, id:' . \$m_id);
                \$this->success('修改成功', 'javascript:location.reload();');
            }
        } else {
            \$info = D('{DummyModel}')->getOne(\$id);

            {DummyFormExtra}
            \$builder = new \Qscmf\Builder\FormBuilder();
            \$builder
                ->setPostUrl(U('edit'))
                ->addFormItem('id', 'hidden', 'ID')
                {DummyFormColumns}
                ->setFormData(\$info)
                ->setShowBtn(false);
                
            return \$builder->display(true);

        }
    }
sample;

    }

}