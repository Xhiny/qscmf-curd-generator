<?php
namespace CurdGen;

use CurdGen\Type\Factory;
use CurdGen\Type\IAuto;
use CurdGen\Type\IForm;
use CurdGen\Type\ISave;
use CurdGen\Type\ITable;
use CurdGen\Type\IValidate;
use PHPUnit\TextUI\Help;

class Parser{

    static protected $table_item = [
        'name' => '',
        'title' => '',
    ];

    static protected $form_item = [
        'name' => '',
        'type' => '',
        'title' => ''
    ];

    static public function exec($comment){
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

    static public function tableColumn($column_set){
        $table_item = self::$table_item;

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

        $type = Factory::getInstance($pair['type'], $pair, $column_set);
        if($type instanceof ITable){
            $res = $type->tableParse();
            $table_item = array_merge($table_item, $res);
        }

        $param_str = join(', ', $table_item);

        return <<<sample
            ->addTableColumn({$param_str})
sample;

    }



    static public function formColumn($column_set){
        $form_item = self::$form_item;

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

        $type = Factory::getInstance($pair['type'], $pair, $column_set);
        if($type instanceof IForm){
            $res = $type->formParse();
            $form_item = array_merge($form_item, $res);
        }

        $param_str = join(', ', $form_item);

        return <<<sample
                ->addFormItem({$param_str})
sample;
    }

    static public function editColumn($column_set){
        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        return <<<sample
            \$ent['{$column_set->COLUMN_NAME}'] = \$data['{$column_set->COLUMN_NAME}'];
sample;
    }

    static public function modelValidate($column_set){
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


    static public function modelAuto($column_set){
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

    static protected function validateLength($column_set, $pair){
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

    static public function saveColumn($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set);
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
}