<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Jobs\KeywordJob;
use App\Models\Keyword;

class KeywordJobTest extends TestCase
{
    /**
     * Test that a valid user agent is returned.
     */
    public function test_user_agent_valid(): void
    {
        $keywordJob = new KeywordJob(new Keyword());
        $userAgent = $this->runProtectedKeywordJobMethod($keywordJob, 'getUserAgent', []);

        $this->assertIsString($userAgent);
    }

    /**
     * Test that a valid language is returned (ISO 639-1)
     */
    public function test_scraper_language_valid(): void
    {
        $validLanguageCodes = ['ab','aa','af','ak','sq','am','ar','an','hy','as','av','ae','ay','az','bm','ba','eu','be','bn','bh','bi','bs','br','bg','my','ca','km','ch','ce','ny','zh','cu','cv','kw','co','cr','hr','cs','da','dv','nl','dz','en','eo','et','ee','fo','fj','fi','fr','ff','gd','gl','lg','ka','de','ki','el','kl','gn','gu','ht','ha','he','hz','hi','ho','hu','is','io','ig','id','ia','ie','iu','ik','ga','it','ja','jv','kn','kr','ks','kk','rw','kv','kg','ko','kj','ku','ky','lo','la','lv','lb','li','ln','lt','lu','mk','mg','ms','ml','mt','gv','mi','mr','mh','ro','mn','na','nv','nd','ng','ne','se','no','nb','nn','ii','oc','oj','or','om','os','pi','pa','ps','fa','pl','pt','qu','rm','rn','ru','sm','sg','sa','sc','sr','sn','sd','si','sk','sl','so','st','nr','es','su','sw','ss','sv','tl','ty','tg','ta','tt','te','th','bo','ti','to','ts','tn','tr','tk','tw','ug','uk','ur','uz','ve','vi','vo','wa','cy','fy','wo','xh','yi','yo','za','zu'];
        $keywordJob = new KeywordJob(new Keyword());
        $scraperLanguage = $this->runProtectedKeywordJobMethod($keywordJob, 'getScraperLanguage', []);

        $this->assertContains($scraperLanguage, $validLanguageCodes);
    }

    /**
     * Test that the arrayToArgs method correctly transforms an array.
     */
    public function test_array_to_args(): void
    {
        $example = [
            'test' => 'value',
            'no-value' => false
        ];
        $exampleExpected = [
            '--test="value"',
            '--no-value'
        ];
        $keywordJob = new KeywordJob(new Keyword());
        $exampleOutput = $this->runProtectedKeywordJobMethod($keywordJob, 'arrayToArgs', [$example]);

        $this->assertEquals($exampleOutput, $exampleExpected);
    }

    /**
     * Triggers a protected method on the KeywordJob class.
     * 
     * @param string $method Name of the method to trigger
     * @param array $args Array of arguments for the method
     * 
     * Testing of the public handle() method would be preferred to testing the individual protected methods,
     * however time constraints prevent getting ChromeDriver running in our Unit Tests (on GitHub Actions).
     * 
     * Testing by mocking is also an option here.
     */
    protected function runProtectedKeywordJobMethod(KeywordJob $obj, string $method, array $args = []): mixed
    {
        $class = new \ReflectionClass('App\Jobs\KeywordJob');
        $methodInstance = $class->getMethod($method);

        return $methodInstance->invokeArgs($obj, $args);
    }
}