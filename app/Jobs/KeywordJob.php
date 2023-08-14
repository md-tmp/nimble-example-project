<?php
/**
 * Keyword Job
 * php version 8.2.4
 * 
 * @category KeywordJob
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Keyword;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

/**
 * Keyword Job
 * 
 * @category KeywordJob
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class KeywordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * 
     * @param Keyword $keyword Assigns to class as a protected property.
     */
    public function __construct(protected Keyword $keyword)
    {
        //
    }

    /**
     * Execute the job.
     * 
     * @return void
     * 
     * @todo Refactor into smaller methods that can be unit tested.
     */
    public function handle(): void
    {
        /**
         * @TODO At some point Google may suspect our traffic of being a bot.
         * We should implement some sort of backoff when this happens.
         * I have been unable to trigger the captchas with the current code.
         * 
         * When reports start including 0s where not expected we can start to replicate this.
         * 
         * Potential solutions include a significant timeout (simple), outsourcing the CAPTCHA (complex), or presenting the CAPTCHA on the frontend (complex).
         * 
         * I'm going to avoid intentionally getting my IP or dev server on any IP list that might encounter more frequent captchas.
         */

        // We use a real browser (Chrome) to make our traffic more human-like and avoid captchas.
        $desiredCapabilities = DesiredCapabilities::chrome();
        $chromeOptions = new ChromeOptions();
        $userAgentList = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
        ];
        $userAgent = $userAgentList[array_rand($userAgentList)];
        $chromeOptions->addArguments(
            [
            '--lang=en', // Set Browser To English
            '--accept-lang=en',
            '--user-agent="' . $userAgent . '"' // Pick a random user agent (change operating systems, identifying as the latest chrome)
            ]
        );
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

        $driver = RemoteWebDriver::create('http://localhost:9515', $desiredCapabilities);

        // Force English with URL Params, this could make our script easier to profile but not a concern unless running at significant scale
        // Loading the home page alongside pauses will make our traffic seem more human-like to Google.
        $driver->get('https://www.google.com/?hl=en&lr=lang_en');

        sleep(rand(1, 5)); // Randomism: Pause for 1 to 5 seconds before searching.

        $driver->findElement(WebDriverBy::cssSelector('[type="Search"]'))
            ->sendKeys($this->keyword->keyword) // fill the search box
            ->submit();

        $totalLinks = $driver->executeScript('return document.querySelectorAll(\'a\').length;');
        // Carousels are counted as a single ad
        // Possible implementation for counting carousel items:
        // https://github.com/md-tmp/nimble-example-project/issues/14
        $totalAds = $driver->executeScript('return document.querySelectorAll(\'[title="Why this ad?"]\').length;');
        $totalResults = $driver->executeScript('return parseInt(document.querySelector(\'#result-stats\').innerHTML.match(/About (\d{1,3}(?:,\d{3})*) results/)[1].replaceAll(\',\',\'\'));');
        $pageSource = $driver->getPageSource();

        // Save result...
        $result = $this->keyword->results()->create(
            [
            'total_links' => $totalLinks,
            'total_ads' => $totalAds,
            'total_results' => $totalResults,
            'cache' => $pageSource
            ]
        );

        sleep(rand(1, 5)); // Randomism: Pause for 1 to 5 seconds before closing the browser
        $driver->quit();

        // Randomism: Pause for 2 to 10 seconds before finishing the job.
        // This is a pause on the current worker.
        sleep(rand(2, 10));
        
    }
}

