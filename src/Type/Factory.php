<?php
namespace CurdGen\Type;

class Factory{

    public static function getInstance($type, $comment){
        $type = ucfirst($type);
        $cls = "CurdGen\\Type\\{$type}";
        if(class_exists($cls)){
            return new $cls($comment);
        }
        else{
            throw new \Exception('未知类型');
        }
    }
}