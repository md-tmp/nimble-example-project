<?php
/**
 * Result Controller
 * php version 8.2.4
 * 
 * @category ResultController
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Result;

/**
 * ResultController
 * 
 * @category ResultController
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class ResultController extends Controller
{
    /**
     * Render result cache directly.
     * 
     * @param Request $request Laravel HTTP Request
     * @param string  $id      Requested Result ID
     * 
     * @return string Result Page Cache, Laravel will convert to 200 response
     */
    public function cache(Request $request, string $id): string
    {
        $result = Result::find($id);
        // Check that result is found, or return 404.
        if ($result === null) {
            return abort(404);
        }

        // Check that the user owns the keyword, or return 404.
        if ($result->keyword->user_id !== $request->user()->id) {
            return abort(404);
        }

        return $result->cache;
    }
}
