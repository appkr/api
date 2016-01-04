<?php

namespace Appkr\Api\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\View\Factory as View;

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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new transformer class';

    /**
     * Key value pair of required names list.
     *
     * @var array
     */
    protected $var = [];

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * @var \stdClass
     */
    protected $subject;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $includes;

    /**
     * @param \Illuminate\Filesystem\Filesystem $file
     * @param \Illuminate\View\Factory          $view
     */
    public function __construct(Filesystem $file, View $view)
    {
        parent::__construct();

        $this->file = $file;
        $this->view = $view;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $this->subject = (new ArgumentConverter)->convert($this->argument('subject'));
        $this->includes = (new OptionParser)->parse($this->option('includes'));

        $name = $this->subject->transformer;
        $path = $this->getPath($name);

        if ($this->file->exists($path)) {
            $this->error("{$path} already exists!");

            return false;
        }

        $this->file->put($path, $this->buildClassContent()->render());

        $this->info("{$path} created successfully.");
    }

    /**
     * Get the path to where the file be created.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path(config('api.transformer.dir') . "/{$name}.php");
    }

    /**
     * Build transformer class content based on the Stub.
     *
     * @return mixed
     */
    protected function buildClassContent()
    {
        return $this->view->make('api::transformer', [
            'subject' => $this->subject,
            'includes' => $this->includes,
        ]);
    }
}