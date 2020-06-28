<?php
namespace CurdGen;

use CurdGen\Type\Factory;
use CurdGen\Type\IForm;
use CurdGen\Type\ITable;
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

        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair);
            if($type instanceof ITable){
                $res = $type->tableParse();
                $table_item = array_merge($table_item, $res);
            }
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
        $form_item['type'] = Helper::wrap('text');

        $pair = self::exec($column_set->COLUMN_COMMENT);
        if(isset($pair['title'])){
            $form_item['title'] = Helper::wrap($pair['title']);
        }
        else{
            $form_item['title'] = Helper::wrap($column_set->COLUMN_NAME);
        }

        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair);
            if($type instanceof IForm){
                $res = $type->formParse();
                $form_item = array_merge($form_item, $res);
            }
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
}