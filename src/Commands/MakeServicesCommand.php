<?php

namespace Delosfei\Services\Commands;

use Delosfei\Services\Makes\makeFacade;
use Delosfei\Services\Makes\MakerTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class MakeServicesCommand extends Command
{
    use MakerTrait;

    // protected $signature = 'ds:code';
    protected $name = 'ds:services';
    protected $description = '生成结构代码';
    protected $files;
    private $composer;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = app()['composer'];
    }

    public function handle()
    {
        $header = "scaffolding: {$this->getObjName("Name")}";
        $footer = str_pad('', strlen($header), '-');
        $dump = str_pad('>DUMP AUTOLOAD<', strlen($header), ' ', STR_PAD_BOTH);

        $this->line("\n----------- $header -----------\n");

        $this->makeFacade();
        $this->makeService();
        $this->makeServiceProvider();

        $this->line("\n----------- $footer -----------");
        $this->comment("----------- $dump -----------");

        $this->composer->dumpAutoloads();

    }

    protected function getArguments()
    {
        return
            [
                ['name', InputArgument::REQUIRED, 'The name of the model. (Ex: User)'],
            ];
    }



    public function getObjName($config = 'Name')
    {
        $names = [];
        $args_name = $this->argument('name');

        // Article
        $names['Name'] = \Str::singular(ucfirst($args_name));
        if (!isset($names[$config])) {
            throw new \Exception("Position name is not found");
        };

        return $names[$config];
    }

    protected function makeFacade()
    {
        new MakeFacade($this, $this->files);
    }

    protected function makeService()
    {
        new MakeService($this, $this->files);
    }

    protected function makeServiceProvider()
    {
        new MakeServiceProvider($this, $this->files);
    }

}
