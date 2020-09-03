<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Phone extends AbstractType implements IForm, ITable, IValidate, ISave  {

    public function validateParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', '/^1\d{10}$/', '手机号码格式不正确', parent::VALUE_VALIDATE, 'regex'],
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