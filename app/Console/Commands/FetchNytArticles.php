<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobs\NytController;

class FetchNytArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:nyt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from New York Times';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new NytController(new \App\Services\NytService());
        $response = $controller->fetchArticles();
        $data = json_decode($response->getContent(), true);

        $this->info($data['message']);
    }
}
