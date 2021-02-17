<?php

namespace Delosfei\Services\Makes;

use Delosfei\Services\Commands\MakeServicesCommand;
use Illuminate\Filesystem\Filesystem;

trait MakerTrait
{
    protected $files;
    protected $scaffoldCommandObj;

    public function __construct(MakeServicesCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->start();
    }

    protected function getArrayRecursive(array $array, $parent = '')
    {
        $data = [];

        foreach ($array as $key => $value) {
            if (gettype($value) == 'array') {
                array_merge(
                    $data,
                    $this->getArrayRecursive($value, "$parent")
                );
                continue;
            }

            $data["$parent.$key"] = $value;
        }

        return $data;
    }


    protected function getFilesRecursive($path)
    {
        $files = [];
        $scan = array_diff(scandir($path), ['.', '..']);

        foreach ($scan as $file) {
            $file = realpath("$path$file");

            if (is_dir($file)) {
                $files = array_merge
                (
                    $files,
                    $this->getFilesRecursive($file.DIRECTORY_SEPARATOR)
                );
                continue;
            }

            $files[] = $file;
        }

        return $files;
    }


    protected function getStubPath()
    {
        return substr(__DIR__, 0, -5).'Stubs'.DIRECTORY_SEPARATOR;
    }


    protected function getStubFields($ui, $type)
    {
        $stubsFieldsPath = $this->getStubPath().join(DIRECTORY_SEPARATOR, ['views', $ui, 'fields', $type, '']);

        if ($this->existsDirectory($stubsFieldsPath)) {
            $this->scaffoldCommandObj->error('Stub not found');

            return;
        }

        $stubsFieldsFiles = $this->getFilesRecursive($stubsFieldsPath);

        $stubs = [];

        foreach ($stubsFieldsFiles as $file) {
            $stubs[str_replace($stubsFieldsPath, '', $file)] = $this->getFile($file);
        }

        return $stubs;
    }

    protected function buildStub(array $metas, &$template)
    {
        foreach ($metas as $k => $v) {
            $template = str_replace("{{".$k."}}", $v, $template);
        }

        return $template;
    }


    protected function getPath($file_name)
    {
        return 'App/Services/'.$this->scaffoldCommandObj->getObjName('Name').'/'.$file_name.'.php';
    }

    protected function getFile($file)
    {
        return $this->files->get($file);
    }

    protected function existsDirectory($path)
    {
        return !$this->files->isDirectory($path);
    }


    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function compileStub($filename)
    {
        $stub = $this->files->get(substr(__DIR__, 0, -5).'Stubs/'.$filename.'.stub');

        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);
        // $this->replaceValidator($stub);

        return $stub;
    }


}
