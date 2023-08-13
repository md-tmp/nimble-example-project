<?php
/**
 * Keyword Model
 * php version 8.2.4
 * 
 * @category Keyword
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * Keyword Model
 * 
 * @category Keyword
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */
class Keyword extends Model
{
    use HasFactory;

    /**
     * Get the User that owns this Keyword.
     * 
     * @return BelongsTo Returns Eloquent BelongsTo Relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
