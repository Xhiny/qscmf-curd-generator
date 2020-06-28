<?php
namespace CurdGen\Type;

use Illuminate\Support\Str;

class Email extends AbstractType implements IValidate  {

    public function validateParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', 'filter_var', '邮箱格式不正确', parent::EXISTS_VALIDATE, 'function', parent::MODEL_BOTH, FILTER_VALIDATE_EMAIL],
p
            . PHP_EOL;
    }
}