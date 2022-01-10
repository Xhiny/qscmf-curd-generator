<?php
namespace CurdGen;

use CurdGen\Type\Factory;
use CurdGen\Type\IAuto;
use CurdGen\Type\IValidate;

class Parser{

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

    static public function modeBuild(&$stub, $columns_set, $table_set, $type){
        $mode = \CurdGen\Mode\Factory::getInstance($type);
        (new $mode())->build($stub, $columns_set, $table_set);

    }
}