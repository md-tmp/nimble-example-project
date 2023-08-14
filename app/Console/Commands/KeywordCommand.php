<?php
/**
 * Keyword Command
 * php version 8.2.4
 * 
 * @category KeywordCommand
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Keyword;

/**
 * Keyword Command
 * 
 * @category KeywordCommand
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class KeywordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:keyword-command {keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $keywordArg = $this->argument('keyword');
        $user = User::first();
        $keyword = Keyword::firstOrNew(
            [
            'user_id' => $user->id,
            'keyword' => $keywordArg
            ]
        );
        $keyword->touch();
        $keyword->save();

        $this->info("Keyword Saved: " . $keywordArg);

        $keyword->enqueueGenerateReport();
    }
}
