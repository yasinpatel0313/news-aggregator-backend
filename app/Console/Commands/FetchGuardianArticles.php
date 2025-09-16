<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobs\GuardianController;

class FetchGuardianArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:guardian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from The Guardian';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $controller = new GuardianController(new \App\Services\GuardianService());
       $response = $controller->fetchArticles();
       $data = json_decode($response->getContent(), true);

       $this->info($data['message']);
    }
}
