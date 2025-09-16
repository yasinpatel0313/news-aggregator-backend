<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobs\NewsApiController;

class FetchNewsApiArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'fetch:newsapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from NewsAPI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $controller = new NewsApiController(new \App\Services\NewsApiService());
       $response = $controller->fetchArticles();
       $data = json_decode($response->getContent(), true);

       $this->info($data['message']);
    }
}
