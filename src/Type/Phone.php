<?php
namespace CurdGen\Type;

use Illuminate\Support\Str;

class Phone extends AbstractType implements IValidate  {

    public function validateParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', '/^1\d{10}$/', '手机号码格式不正确', parent::EXISTS_VALIDATE, 'regex'],
p
            . PHP_EOL;
    }
}