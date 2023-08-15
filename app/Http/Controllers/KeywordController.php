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

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use \Illuminate\Http\RedirectResponse;
use Inertia\Response;
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
     * Renders Keyword List via Vue
     * 
     * @param Request $request Laravel HTTP Request
     * 
     * @return Response Inertia Response
     */
    public function index(Request $request): Response
    {

        $keywordsQuery = $request->user()->keywords();
        $keywordsQuery->select(['id', 'keyword', 'updated_at']);

        // Search Support
        if ($request->query('search')) {
            $searchParam = $request->query('search');
            $keywordsQuery->where(
                'keywords.keyword',
                'LIKE',
                '%' . $searchParam . '%'
            );
        }

        // Sorting Support
        $sortKey = 'updated_at';
        $sortParam = $request->query('sort');
        if ($sortParam && in_array($sortParam, ['keyword', 'updated_at'])) {
            $sortKey = $sortParam;
        }

        $sortDirection = 'desc';
        $dirParam = $request->query('dir');
        if ($dirParam && in_array(strtolower($dirParam), ['asc', 'desc'])) {
            $sortDirection = $dirParam;
        }
        $keywordsQuery->orderBy($sortKey, $sortDirection);

        // Query & Pagination
        $keywordsCollection = $keywordsQuery->paginate(10)->withQueryString()
            ->through(
                function ($keyword) {
                    return  $keyword->toArray() + [
                    'updated_at_ago' => optional($keyword->updated_at)
                        ->diffForHumans(),
                    ];
                }
            );
        
        $queueSize = Queue::size();
        $queueTime = $queueSize * 30;
        $queueTimeReadable = CarbonInterval::seconds($queueTime)
            ->cascade()->forHumans();

        return Inertia::render(
            'Keyword/Index', [
                'keywords' => $keywordsCollection,
                'sort_key' => $sortKey,
                'sort_direction' => $sortDirection,
                'search' => $request->query('search'),
                'queue_size' => $queueSize,
                'queue_time' => $queueTimeReadable
            ]
        );
    }

    /**
     * Show Results for a given Keyword.
     * 
     * @param Request $request Laravel HTTP Request
     * @param string  $id      Requested Keyword ID
     * 
     * @return Response Inertia Response
     */
    public function show(Request $request, string $id): Response
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

        $results = $keyword->results->map(
            function ($result) {
                return $result->toArray() + [
                'created_at_ago' => optional($result->created_at)->diffForHumans(),
                ];
            }
        );

        return Inertia::render(
            'Keyword/Show', [
                'keyword' => $keyword->keyword,
                'reports' => $results
            ]
        );
    }

    /**
     * Process Import CSV Upload.
     *
     * @param Request $request Laravel HTTP Request
     * 
     * @return Response Inertia Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'import_file' => 'required|mimes:csv,txt'
            ]
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

        return back()->with(
            'flash', [
                'upload_success' =>
                    'Upload successful, estimated completion time: '
                        . $queueTimeReadable,
            ]
        );
    }
}