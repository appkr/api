<?php

namespace Appkr\Fractal\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeTransformerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:transformer
        {subject : The string name of the model class. e.g. App\\\\Book}
        {--includes= : Optional list of resources to include. e.g. App\\\\User:author,App\\\\Comment:comments:true If the third element is provided as true, yes, or 1, the command will interpret the include as a collection.}';

    /**
     * Key value pair of required names list.
     *
     * @var array
     */
    protected $var = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new transformer class';

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $this->calculateVariables();

        $name = $this->getVar('subject.transformer');
        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error("{$name} already exists!");

            return false;
        }

        $this->files->put($path, $this->buildClassContent());

        $this->info("{$name} created successfully.");
    }

    /**
     * Calculate required variables out of the command argument and option.
     */
    protected function calculateVariables()
    {
        $subject = $this->argument('subject');
        $includes = $this->option('includes');

        $this->var = [
            'subject' => (new ArgumentConverter)->convert($subject),
            'includes' => $includes
                ? (new OptionParser)->parse($includes)
                : [],
        ];
    }

    /**
     * Get array value out of $this->var property.
     *
     * @param $name
     * @return mixed
     */
    protected function getVar($name)
    {
        return array_get($this->var, $name);
    }

    /**
     * Get the path to where the file be created.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path(config('fractal.transformer.dir') . "/{$name}.php");
    }

    /**
     * Build transformer class content based on the Stub.
     *
     * @return mixed
     */
    protected function buildClassContent()
    {
        $bodyStub = $this->files->get(__DIR__ . '/../../stubs/transformer.stub');

        if ($includes = $this->getVar('includes')) {
            $propertyStub = $this->files->get(__DIR__ . '/../../stubs/include.property.stub');
            $methods = null;

            foreach($includes as $include) {
                $methodStub = $include['type'] == 'collection'
                    ? $this->files->get(__DIR__ . '/../../stubs/include.method.collection.stub')
                    : $this->files->get(__DIR__ . '/../../stubs/include.method.item.stub');

                $methods .= str_replace(
                    ['{{include.model}}', '{{include.basename}}', '{{include.relationship}}', '{{include.method}}', '{{include.transformer}}'],
                    [$include['model'], $include['basename'], $include['relationship'], $include['method'], $include['transformer']],
                    $methodStub
                );
            }

            $propertyStub = str_replace(
                '{{include.value}}',
                "'" . implode("', '", array_pluck($includes, 'relationship')) . "'",
                $propertyStub
            );

            $bodyStub = str_replace(
                ['{{include.methods}}', '{{include.property}}'],
                [$methods, $propertyStub],
                $bodyStub
            );
        } else {
            $bodyStub = str_replace(
                ['{{include.methods}}', '{{include.property}}'],
                ['', ''],
                $bodyStub
            );
        }

        $subject = $this->getVar('subject');

        return str_replace(
            ['{{subject.model}}', '{{subject.basename}}', '{{subject.object}}', '{{subject.transformer}}', '{{subject.route}}'],
            [$subject['model'], $subject['basename'], $subject['object'], $subject['transformer'], $subject['route']],
            $bodyStub
        );
    }
}