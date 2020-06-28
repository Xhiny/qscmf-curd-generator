<?php
namespace CurdGen;

class Helper{

    static public function wrap($value){
        return "\"{$value}\"";
    }
}