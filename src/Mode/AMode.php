<?php

namespace CurdGen\Mode;


use CurdGen\Helper;
use CurdGen\Type\Factory;
use CurdGen\Type\IAuto;
use CurdGen\Type\IForm;
use CurdGen\Type\IFormExtra;
use CurdGen\Type\ISave;
use CurdGen\Type\ITable;
use CurdGen\Type\IValidate;

abstract class AMode implements IMode
{

    protected $mode;
    protected $table_item = [
        'name' => '',
        'title' => '',
    ];

    protected $form_item = [
        'name' => '',
        'type' => '',
        'title' => ''
    ];

    public function exec($comment){
        $num = preg_match_all('/@(.+?)=(.+?);/', $comment, $match);
        $pair = [];
        for($i = 0; $i < $num; $i++){
            $pair[$match[1][$i]] = $match[2][$i];
        }

        if(!isset($pair['type'])){
            $pair['type'] = 'text';
        }

        return $pair;
    }

    public function modelValidate($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['length'])){
            $res .= self::validateLength($column_set, $pair);
        }

        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set);
            if($type instanceof IValidate){
                $validate_res = $type->validateParse();
                return $res . $validate_res;
            }
        }
        if($res){
            return $res;
        }
        return false;
    }


    public function modelAuto($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if($column_set->COLUMN_NAME == 'create_date'){
            $res .= <<<p
        ['create_date', 'microtime', parent::MODEL_INSERT, 'function', true],
p
                . PHP_EOL;
        }

        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set);
            if($type instanceof IAuto){
                $auto_res = $type->autoParse();
                return $res . $auto_res;
            }
        }
        if($res){
            return $res;
        }
        return false;
    }

    protected function validateLength($column_set, $pair){
        if(!isset($pair['title'])){
            throw new \Exception('length type not found title');
        }

        list($min, $max) = explode(',', $pair['length']);
        $msg = $pair['title'] . '长度必须在' . $min . '到' . $max . '范围内';
        return <<<p
        ['{$column_set->COLUMN_NAME}', '{$pair['length']}', '{$msg}', self::EXISTS_VALIDATE, 'length'],
p
            . PHP_EOL;

    }

    public function tableColumn($column_set){
        $table_item = $this->table_item;

        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        $pair = self::exec($column_set->COLUMN_COMMENT);
        if(isset($pair['title'])){
            $table_item['title'] = Helper::wrap($pair['title']);
        }
        else{
            $table_item['title'] = Helper::wrap($column_set->COLUMN_NAME);
        }

        $table_item['name'] = Helper::wrap($column_set->COLUMN_NAME);

        $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
        if($type instanceof ITable){
            $res = $type->tableParse();
            $table_item = array_merge($table_item, $res);
        }

        $param_str = join(', ', $table_item);

        return <<<sample
            ->addTableColumn({$param_str})
sample;

    }

    public function formColumn($column_set){
        $form_item = $this->form_item;

        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        $form_item['name'] = Helper::wrap($column_set->COLUMN_NAME);

        $pair = self::exec($column_set->COLUMN_COMMENT);
        if(isset($pair['title'])){
            $form_item['title'] = Helper::wrap($pair['title']);
        }
        else{
            $form_item['title'] = Helper::wrap($column_set->COLUMN_NAME);
        }

        $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
        if($type instanceof IForm){
            $res = $type->formParse();
            $form_item = array_merge($form_item, $res);
        }

        $param_str = join(', ', $form_item);

        return <<<sample
                ->addFormItem({$param_str})
sample;
    }

    public function editColumn($column_set){
        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        return <<<sample
            \$ent['{$column_set->COLUMN_NAME}'] = \$data['{$column_set->COLUMN_NAME}'];
sample;
    }

    public function saveColumn($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
            if($type instanceof ISave && isset($pair['save']) && $pair['save'] == 'true'){
                $save_res = $type->saveParse();
                $res .= $save_res;
            }
        }
        if($res){
            return $res;
        }
        return false;
    }

    public function formExtra($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
            if($type instanceof IFormExtra){
                $res = $type->formExtraParse();
            }
        }
        if($res){
            return $res;
        }
        return false;
    }

    public function injectAddTopButton(){
        return <<<sample
->addTopButton('addnew')
sample;

    }

    public function injectEditRightButton(){
        return <<<sample
->addRightButton('edit')
sample;

    }

    public function injectSaveTopButton(){
        return <<<template
->addTopButton('save', array('title' => '保存'))
template;

    }

    public function funSuccessJumpUrl(){
        return <<<sample
javascript:location.href=document.referrer;
sample;

    }

    public function funFormDisplay(){
        return <<<sample
->setNIDByNode(MODULE_NAME, CONTROLLER_NAME, 'index')
                ->display();
sample;

    }

    public function editMetaTitle(){
        return '编辑{DummyModelTitle}';
    }

    public function addMetaTitle(){
        return '新增{DummyModelTitle}';
    }

    public function editExtraFun(){
        return '';
    }

    public function addExtraFun(){
        return '';
    }

    public function tableBtnPlaceholder(){
        return '';
    }

}