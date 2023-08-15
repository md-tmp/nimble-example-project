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
     * Returns a random user agent
     * 
     * @return string Returns a User Agent string.
     * 
     * @todo Implement admin setting for managing user agents.
     */
    protected function getUserAgent(): string
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $userAgentList = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
        ];
        // phpcs:enable
        return $userAgentList[array_rand($userAgentList)];
    }

    /**
     * Gets preferred language for scraper.
     * 
     * @return string Returns a 2-Letter Language Code (ISO 639‑1)    
     * 
     * @todo Implement admin setting for configuring this and other settings.
     */
    protected function getScraperLanguage(): string
    {
        return 'en';
    }

    /**
     * Converts an array into CLI style arguments.
     * 
     * @param array $array Array of arguments.
     * 
     * @return array Array of arguments reformatted for CLI use.
     */
    protected function arrayToArgs(array $array): array
    {
        $responseArray = [];
        foreach ($array as $key => $value) {
            $argument = '--' . $key;
            if ($value !== false) {
                $argument .= '="' . $value . '"';
            }
            array_push($responseArray, $argument);
        }
        return $responseArray;
    }

    /**
     * Configures Chrome Web Driver.
     * 
     * @return RemoteWebDriver Chrome Web Driver
     * 
     * @todo --proxy-server can be used to add HTTP proxy support in the future.
     */
    protected function buildChromeDriver(): RemoteWebDriver
    {
        $capabilities = DesiredCapabilities::chrome();
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(
            $this->arrayToArgs(
                [
                    'lang' => $this->getScraperLanguage(),
                    'accept-lang' => $this->getScraperLanguage(),
                    'user-agent' => $this->getUserAgent(),
                    'headless' => false
                ]
            )
        );
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

        return RemoteWebDriver::create('http://localhost:9515', $capabilities);
    }

    /**
     * Generate a report array by evaluating JavaScript on the currently loaded page.
     * 
     * @param RemoteWebDriver $driver Chrome Driver
     * 
     * @return array Report of data collected from the current page.
     * 
     * @todo Create an admin interface for managing $reportConfig items.
     */
    protected function runReport(RemoteWebDriver $driver): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $reportConfig = [
            'total_links' => 'return document.querySelectorAll(\'a\').length;',
            'total_ads' => 'return document.querySelectorAll(\'[title="Why this ad?"]\').length;',
            'total_results' => 'return parseInt(document.querySelector(\'#result-stats\').innerHTML.match(/About (\d{1,3}(?:,\d{3})*) results/)[1].replaceAll(\',\',\'\'));'
        ];
        // phpcs:enable

        $report = [];
        foreach ($reportConfig as $key => $script) {
            $report[$key] = $driver->executeScript($script);
        }

        return $report;
    }

    /**
     * Loads google, pauses, then searches for the current job Keyword.
     * 
     * @param RemoteWebDriver $driver Chrome Driver to run the Google Search on.
     * 
     * @return void
     * 
     * @todo Support alternate google domains (e.g. google.co.th)
     */
    protected function executeGoogleSearch(RemoteWebDriver $driver): void
    {
        $queryString = http_build_query(
            [
                'hl' => $this->getScraperLanguage(),
                'lr' => 'lang_' . $this->getScraperLanguage()
            ]
        );
        $driver->get('https://www.google.com/?' . $queryString);

        $this->randomPause(1, 5);

        $driver->findElement(WebDriverBy::cssSelector('[type="Search"]'))
            ->sendKeys($this->keyword->keyword)
            ->submit();

        $this->randomPause(1, 5);
    }

    /**
     * Sleep for a random amount of time between $min and $max.
     * 
     * @param int $min Minimum sleep time in seconds
     * @param int $max Maximum sleep time in seconds
     * 
     * @return void
     */
    protected function randomPause(int $min, int $max): void
    {
        sleep(rand($min, $max));
    }

    /**
     * Execute the job.
     * 
     * @return void
     * 
     * @todo Progressively increase backoff time.
     * @todo Move worker into a separate class to eliminate concerns around Laravel
     * serializing properties for the queueing implementation. This would allow us
     * to move $driver into a class property, and to more easily mock/test.
     */
    public function handle(): void
    {
        // Get Chrome Driver, we use a real browser.
        $driver = $this->buildChromeDriver();

        // Load Google.com, then trigger a search on the frontend.
        $this->executeGoogleSearch($driver);

        if (str_contains($driver->getCurrentUrl(), '/sorry/index?continue=')) {
            // We encountered a captcha... randomPause then start again.
            
            // Close the web browser
            $driver->quit();
            $this->randomPause(1800, 3600); // Pause for 30 mins to 1 hr
            $this->handle();
            return;
        }

        // Trigger JavaScript to collect several report items from the results page.
        $report = $this->runReport($driver);

        // Save Keyword in case it doesn't exist yet.
        $this->keyword->save();

        // Save results to the database
        $result = $this->keyword->results()->create(
            array_merge(
                $report,
                [
                    'cache' => $driver->getPageSource()
                ]
            )
        );

        // Update updated_at for keyword
        $this->keyword->refresh();
        $this->keyword->touch();
        $this->keyword->save();

        // Close the web browser
        $driver->quit();
        
        // Random pauses rate-limit our traffic and make it look more human-like.
        $this->randomPause(17, 25);
    }
}

