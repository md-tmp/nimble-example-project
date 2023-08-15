<?php
/**
 * Result Model
 * php version 8.2.4
 * 
 * @category Result
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Keyword;

/**
 * Result Model
 * 
 * @category Result
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class Result extends Model
{
    use HasFactory;

    /**
     * Fillable fields for mass-assignment
     */
    protected $fillable = [
        'keyword_id',
        'total_links',
        'total_ads',
        'total_results',
        'cache'
    ];

    /**
     * Get the Keyword that owns this Result.
     * 
     * @return BelongsTo Returns Eloquent BelongsTo Relation
     */
    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
