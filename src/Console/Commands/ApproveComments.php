<?php

namespace Yarmat\Comment\Console\Commands;

use Illuminate\Console\Command;

class ApproveComments extends Command
{
    protected $model;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve all comments on your site';

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
        $this->model::where('approved_at', '=', null)->update([
            'approved_at' => now()
        ]);

        return 'Comments are approved';
    }
}
