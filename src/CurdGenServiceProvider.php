<?php
namespace CurdGen;

use CurdGen\Commands\CurdGenCommand;
use Illuminate\Support\ServiceProvider;

class CurdGenServiceProvider extends ServiceProvider{

    protected $commands = [
        CurdGenCommand::class
    ];

    public function register(){
        $this->commands($this->commands);
    }
}