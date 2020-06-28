<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Date extends AbstractType implements ITable , IForm, IAuto {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('date')
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('date')
        ];

        return $table_item;
    }

    public function autoParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', 'strtotime', parent::MODEL_BOTH, 'function'],
p
            . PHP_EOL;
    }
}