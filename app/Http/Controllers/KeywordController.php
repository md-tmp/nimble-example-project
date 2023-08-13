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
use Inertia\Response;

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
    public function index(Request $request) : Response
    {

        $keywordsQuery = $request->user()->keywords();
        $keywordsQuery->select(['id', 'keyword', 'updated_at']);

        // Sorting Support
        $sortKey = 'updated_at';
        $sortParam = $request->query('sort');
        if ($sortParam && in_array($sortParam, ['keyword', 'updated_at'])) {
            $sortKey = $sortParam;
        }

        $sortDirection = 'DESC';
        $dirParam = $request->query('dir');
        if ($dirParam && in_array($dirParam, ['asc', 'desc'])) {
            $sortDirection = $dirParam;
        }
        $keywordsQuery->orderBy($sortKey, $sortDirection);

        // Query & Pagination
        $keywordsCollection = $keywordsQuery->paginate(5)->withQueryString()
            ->through(
                function ($keyword) {
                    return  $keyword->toArray() + [
                    'updated_at_ago' => optional($keyword->updated_at)
                        ->diffForHumans(),
                    ];
                }
            );

        return Inertia::render(
            'Keyword/Index', [
            'keywords' => $keywordsCollection,
            'sort_key' => $sortKey,
            'sort_direction' => $sortDirection
            ]
        );
    }
}