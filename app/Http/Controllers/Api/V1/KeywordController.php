<?php
/**
 * Keyword Controller
 * php version 8.2.4
 * 
 * @category KeywordController
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keyword;
use Illuminate\Support\Facades\Queue;
use Carbon\CarbonInterval;
use Illuminate\Validation\ValidationException;

/**
 * KeywordController
 * 
 * @category KeywordController
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class KeywordController extends Controller
{

    /**
     * Constructor, applies token permissions middleware
     */
    public function __construct()
    {
        $this->middleware('abilities:read')->only(['index', 'show']);
        $this->middleware('abilities:import')->only(['store']);
    }

    /**
     * Renders Keyword List via API
     * 
     * @param Request $request Laravel HTTP Request
     * 
     * @return array Response
     */
    public function index(Request $request): array
    {
        return ['keyword' => $request->user()->keywords];
    }

    /**
     * Show Results for a given Keyword via API
     * 
     * @param Request $request Laravel HTTP Request
     * @param string  $id      Requested Keyword ID
     * 
     * @return array Response
     */
    public function show(Request $request, string $id): array
    {
        $keyword = Keyword::find($id);
        // Check that keyword is found, or return 404.
        if ($keyword === null) {
            return abort(404);
        }

        // Check that the user owns the keyword, or return 404.
        if ($keyword->user_id !== $request->user()->id) {
            return abort(404);
        }

        return ['keyword' => $keyword];
    }

    /**
     * Process Import CSV Upload.
     * 
     * Almost a copy-and-paste of the main KeywordController. Unfortunately a side
     * effect of versioning the API where we'll always want this version to behave
     * the same, and will introduce changes in a V2 namespace.
     *
     * @param Request $request Laravel HTTP Request
     * 
     * @return array Response
     */
    public function store(Request $request): array
    {
        $request->validate(
            ['import_file' => 'required|mimes:csv,txt']
        );

        $csvContents = $request->file('import_file')->get();
        $csvLines = explode("\n", $csvContents);
        $csvHeaders = str_getcsv(strtolower($csvLines[0]));

        if (sizeof($csvLines) < 2) {
            throw ValidationException::withMessages(
                ['import_file' => 'CSV must include at least 1 keyword']
            );
        }

        // Implemented per requirements; this limit can be increased substantially.
        // Switch to Redis queue driver to speed up upload processing at scale.
        if (sizeof($csvLines) > 101) {
            throw ValidationException::withMessages(
                ['import_file' =>
                    'CSV must contain less than or equal to 100 keywords']
            );
        }

        $keywordColumnIndex = array_search('keyword', $csvHeaders);
        if ($keywordColumnIndex === false) {
            throw ValidationException::withMessages(
                ['import_file' =>
                    'CSV must contain a column with the header Keyword']
            );
        }

        $userId = $request->user()->id;

        for ($i = 1; $i < sizeof($csvLines); $i++) {
            $csvLine = str_getcsv($csvLines[$i]);
            $keyword = $csvLine[$keywordColumnIndex];
            
            $keywordObj = Keyword::firstOrNew(
                [
                    'user_id' => $userId,
                    'keyword' => $keyword
                ]
            );
            $keywordObj->save();
            $keywordObj->enqueueGenerateReport();
        }

        $queueSize = Queue::size();
        $queueTimeReadable = CarbonInterval::seconds($queueSize * 30)
            ->cascade()->forHumans();

        return [
            'success' =>
                'Upload successful, estimated completion time: '
                    . $queueTimeReadable,
        ];
    }

}
