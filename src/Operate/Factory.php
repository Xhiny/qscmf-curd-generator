<?php
namespace CurdGen\Operate;

class Factory{

    public static function getInstance($type){
        $type = ucfirst($type);
        $cls = "CurdGen\\Operate\\{$type}";
        if(class_exists($cls)){
            return new $cls();
        }
        else{
            throw new \Exception('未知类型');
        }
    }
}