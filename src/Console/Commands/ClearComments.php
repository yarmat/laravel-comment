<?php

namespace Yarmat\Comment\Console\Commands;

use Illuminate\Console\Command;

class ClearComments extends Command
{
    protected $model;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all comments on your site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = config('comment.models.comment');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->model::query()->truncate();

        return 'Comments are cleared';
    }
}
