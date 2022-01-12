<?php
namespace CurdGen;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

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

    const DUMMY_MODEL = '{DummyModel}';
    const DUMMY_MODEL_VALIDATE = '{DummyValidate}';
    const DUMMY_MODEL_AUTO = '{DummyAuto}';

    const DUMMY_MODEL_TITLE = '{DummyModelTitle}';
    const DUMMY_TABLE_COLUMNS = '{DummyTableColumns}';
    const DUMMY_EDIT_COLUMNS = '{DummyEditColumns}';
    const DUMMY_FORM_COLUMNS = '{DummyFormColumns}';
    const DUMMY_SAVE_TOP_BUTTON = '{DummySaveTopButton}';
    const DUMMY_SAVE = '{DummySave}';
    const DUMMY_SAVE_COLUMNS = '{DummySaveColumns}';
    const DUMMY_FORM_EXTRA = '{DummyFormExtra}';

    const DUMMY_TABLE_DATA_LIST = '{DummyTableDataList}';
    const DUMMY_TABLE_BTN_PLACEHOLDER = '{DummyTableBtnPlaceholder}';
    const DUMMY_ADD_TOP_BUTTON = '{DummyAddTopButton}';
    const DUMMY_EDIT_RIGHT_BUTTON = '{DummyEditRightButton}';
    const DUMMY_FUN_SUCCESS_JUMP_URL = '{DummyFunSuccessJumpUrl}';
    const DUMMY_FUN_FORM_DISPLAY = '{DummyFunFormDisplay}';
    const DUMMY_EDIT_META_TITLE = '{DummyEditMetaTitle}';
    const DUMMY_ADD_META_TITLE = '{DummyAddMetaTitle}';
    const DUMMY_EDIT_EXTRA_FUN = '{DummyEditExtraFun}';
    const DUMMY_ADD_EXTRA_FUN = '{DummyAddExtraFun}';

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

    static public function modeController(&$stub, $columns_set, $table_set, $mode){
        $dummy_model = Helper::getDummyModel($table_set[0]->TABLE_NAME);
        $mode_cls = \CurdGen\Mode\Factory::getInstance($mode);

        $key_pair = self::exec($table_set[0]->TABLE_COMMENT);

        if(!isset($key_pair['title'])){
            throw new InvalidArgumentException("请设置表 @title");
        }
        $dummy_model_title = $key_pair['title'];

        $dummy_table_columns = '';
        $dummy_form_columns = '';
        $dummy_edit_columns = '';
        $dummy_save_columns = '';
        $dummy_form_extra = '';

        foreach($columns_set as $column){
            $dump_table_str = $mode_cls->tableColumn($column);
            if($dump_table_str !== false){
                $dummy_table_columns .= $dump_table_str . PHP_EOL;
            }

            $dump_form_str = $mode_cls->formColumn($column);
            if($dump_form_str !== false){
                $dummy_form_columns .= $dump_form_str . PHP_EOL;
            }

            $dump_form_extra_str = $mode_cls->formExtra($column);
            if($dump_form_extra_str !== false){
                $dummy_form_extra .= $dump_form_extra_str . PHP_EOL;
            }

            $dump_edit_str = $mode_cls->editColumn($column);
            if($dump_edit_str !== false){
                $dummy_edit_columns .= $dump_edit_str . PHP_EOL;
            }

            $dummy_save_str = $mode_cls->saveColumn($column);
            if($dummy_save_str !== false){
                $dummy_save_columns .= $dummy_save_str . PHP_EOL;
            }
        }

        if(strlen($dummy_save_columns) > 0){
            self::injectSaveTemplate($stub);

            $stub = str_replace(self::DUMMY_SAVE_TOP_BUTTON, trim($mode_cls->injectSaveTopButton()), $stub);
            $stub = str_replace(self::DUMMY_SAVE_COLUMNS, trim($dummy_save_columns), $stub);
        }
        else{
            $stub = str_replace(self::DUMMY_SAVE, '', $stub);
            $stub = str_replace(self::DUMMY_SAVE_TOP_BUTTON, '', $stub);
        }

        $stub = str_replace(self::DUMMY_ADD_TOP_BUTTON, trim($mode_cls->injectAddTopButton()), $stub);
        $stub = str_replace(self::DUMMY_EDIT_RIGHT_BUTTON, trim($mode_cls->injectEditRightButton()), $stub);

        $table_btn_placeholder =  $mode_cls->tableBtnPlaceholder();

        if(strlen($table_btn_placeholder) > 0){
            self::injectTableDataListTemplate($stub);
            $stub = str_replace(self::DUMMY_TABLE_BTN_PLACEHOLDER, trim($table_btn_placeholder), $stub);
        }
        else{
            $stub = str_replace(self::DUMMY_TABLE_DATA_LIST, '', $stub);
        }

        $stub = str_replace(self::DUMMY_FUN_SUCCESS_JUMP_URL, trim($mode_cls->funSuccessJumpUrl()), $stub);
        $stub = str_replace(self::DUMMY_FUN_FORM_DISPLAY, trim($mode_cls->funFormDisplay()), $stub);
        $stub = str_replace(self::DUMMY_EDIT_META_TITLE, trim($mode_cls->editMetaTitle()), $stub);
        $stub = str_replace(self::DUMMY_ADD_META_TITLE, trim($mode_cls->addMetaTitle()), $stub);
        $stub = str_replace(self::DUMMY_EDIT_EXTRA_FUN, trim($mode_cls->editExtraFun()), $stub);
        $stub = str_replace(self::DUMMY_ADD_EXTRA_FUN, trim($mode_cls->addExtraFun()), $stub);

        $stub = str_replace(self::DUMMY_TABLE_COLUMNS, trim($dummy_table_columns), $stub);
        $stub = str_replace(self::DUMMY_EDIT_COLUMNS, trim($dummy_edit_columns), $stub);
        $stub = str_replace(self::DUMMY_FORM_COLUMNS, trim($dummy_form_columns), $stub);
        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);
        $stub = str_replace(self::DUMMY_MODEL_TITLE, trim($dummy_model_title), $stub);
        $stub = str_replace(self::DUMMY_FORM_EXTRA, trim($dummy_form_extra), $stub);

    }

    static protected function injectSaveTemplate(&$stub){
        $save_stub = (new Filesystem())->get(__DIR__ ."/Stubs/save.stub");
        $stub = str_replace(self::DUMMY_SAVE, $save_stub, $stub);
    }

    static protected function injectTableDataListTemplate(&$stub){
        $table_data_list = (new Filesystem())->get(__DIR__ ."/Stubs/tableDataList.stub");
        $stub = str_replace(self::DUMMY_TABLE_DATA_LIST, $table_data_list, $stub);
    }

    static public function modeModel(&$stub, $columns_set, $table_set, $mode= null){
        $dummy_model = Helper::getDummyModel($table_set[0]->TABLE_NAME);
        $mode_cls = \CurdGen\Mode\Factory::getInstance($mode);

        $dummy_validate = '';
        $dummy_auto = '';

        foreach($columns_set as $column){
            $dummy_validate_str = $mode_cls->modelValidate($column);
            if($dummy_validate_str !== false){
                $dummy_validate .= $dummy_validate_str;
            }

            $dummy_auto_str = $mode_cls->modelAuto($column);
            if($dummy_auto_str !== false){
                $dummy_auto .= $dummy_auto_str;
            }
        }

        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);
        $stub = str_replace(self::DUMMY_MODEL_VALIDATE, rtrim($dummy_validate), $stub);
        $stub = str_replace(self::DUMMY_MODEL_AUTO, rtrim($dummy_auto), $stub);
    }
}