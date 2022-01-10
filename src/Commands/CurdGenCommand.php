<?php
namespace CurdGen\Commands;

use CurdGen\Helper;
use CurdGen\Parser;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CurdGenCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qscmf:curd-gen {table_name : The name of table}
                                           {--M|mode=standard :  standard(default):create a new page to operate add or edit data;float:use modal with form to operate add or edit data}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto build curd code feel like rocket';

    const DUMMY_MODEL = '{DummyModel}';
    const DUMMY_MODEL_VALIDATE = '{DummyValidate}';
    const DUMMY_MODEL_AUTO = '{DummyAuto}';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;

    }

    public function handle(){
        $table = $this->input->getArgument('table_name');
        $mode = $this->option('mode');

        $schema = env('DB_DATABASE');
        $columns_res = DB::select("select * from information_schema.columns where TABLE_SCHEMA='{$schema}' and TABLE_NAME='{$table}'");
        $table_res = DB::select("select * from information_schema.tables where TABLE_SCHEMA='{$schema}' and TABLE_NAME='{$table}'");

        $stub = $this->getStub('controller');

        $path = $this->populateControllerStub1($stub, $columns_res, $table_res, $mode);
        $path_arr = explode('/', $path);
        if($this->files->exists($path)){
            $confirm = $this->confirm( $path_arr[count($path_arr) - 1]. '已经存在，确定要覆盖吗');
            $confirm && $this->makeFile($path, $stub);
        }
        else{
            $this->makeFile($path, $stub);
        }

        $stub = $this->getStub('model');

        $path = $this->populateModelStub($stub, $columns_res, $table_res);
        $path_arr = explode('/', $path);
        if($this->files->exists($path)){
            $confirm = $this->confirm( $path_arr[count($path_arr) - 1]. '已经存在，确定要覆盖吗');
            $confirm && $this->makeFile($path, $stub);
        }
        else{
            $this->makeFile($path, $stub);
        }

    }

    protected function makeFile($path, $stub){
        $this->files->put($path, $stub);
        $path_arr = explode('/', $path);
        $this->info($path_arr[count($path_arr) - 1] . ' 成功生成');
    }


    protected function getStub($stub_type){
        return $this->files->get(__DIR__ ."/../Stubs/{$stub_type}.stub");
    }

    protected function populateModelStub(&$stub, $columns_set, $table_set){
        $dummy_model = Helper::getDummyModel($table_set[0]->TABLE_NAME);

        $dummy_validate = '';
        $dummy_auto = '';

        foreach($columns_set as $column){
            $dummy_validate_str = Parser::modelValidate($column);
            if($dummy_validate_str !== false){
                $dummy_validate .= $dummy_validate_str;
            }

            $dummy_auto_str = Parser::modelAuto($column);
            if($dummy_auto_str !== false){
                $dummy_auto .= $dummy_auto_str;
            }
        }

        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);
        $stub = str_replace(self::DUMMY_MODEL_VALIDATE, rtrim($dummy_validate), $stub);
        $stub = str_replace(self::DUMMY_MODEL_AUTO, rtrim($dummy_auto), $stub);

        return LARA_DIR . '/../app/Common/Model/' . $dummy_model . 'Model.class.php';
    }

    protected function populateControllerStub1(&$stub, $columns_set, $table_set, $mode = null){
        $dummy_model = Helper::getDummyModel($table_set[0]->TABLE_NAME);

        $mode = $this->getMode($mode);
        Parser::modeBuild($stub, $columns_set, $table_set, $mode);

        return LARA_DIR . '/../app/Admin/Controller/' . $dummy_model . 'Controller.class.php';
    }

    protected function getMode($mode = null){
        $mode =  is_null($mode) ? 'standard' : $mode;
        if (!in_array($mode, ['standard','float'])){
            throw new InvalidArgumentException("mode 值无效");
        }

        return $mode;
    }

}