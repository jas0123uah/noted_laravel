<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MailController;

class GenerateReviewNotecards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-review-notecards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates review notecards and generates daily email to notify users of their review notecards.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mailController = new MailController();
        $mailController->index();

        $this->info('Review notecards generated and daily emails sent.');
    }
}
