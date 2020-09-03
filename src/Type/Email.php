<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Email extends AbstractType implements IForm, ITable, IValidate, ISave  {

    public function validateParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', 'filter_var', '邮箱格式不正确', parent::VALUE_VALIDATE, 'function', parent::MODEL_BOTH, FILTER_VALIDATE_EMAIL],
p
            . PHP_EOL;
    }

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('text')
        ];

        return $form_item;
    }

    public function saveParse()
    {
        return <<<sample
                \$save_data['{$this->column_set->COLUMN_NAME}'] = \$data['{$this->column_set->COLUMN_NAME}'][\$k];
sample;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('text')
        ];

        if(isset($this->comment['save']) &&  $this->comment['save'] == 'true'){
            $table_item['value'] = Helper::wrap('');
            $table_item['editable'] = 'true';
        }

        return $table_item;
    }
}