<?php

namespace Delosfei\Services\Makes;



class makeFacade
{
    use MakerTrait;

    private function start()
    {
        $name = $this->scaffoldCommandObj->getObjName('Name') . 'Facade';
        $path = $this->getPath($name);
        if ($this->files->exists($path))
        {
            return $this->scaffoldCommandObj->comment("x " . $path);
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileControllerStub());

        $this->scaffoldCommandObj->info('+ ' . $path);
    }


    protected function compileControllerStub()
    {


        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/facade.stub');


        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);
        // $this->replaceValidator($stub);

        return $stub;
    }





}
