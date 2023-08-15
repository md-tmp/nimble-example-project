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
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Result;
use App\Exceptions\ModelMustBeSavedException;
use App\Jobs\KeywordJob;

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
     * Fillable fields for mass-assignment
     */
    protected $fillable = ['user_id', 'keyword'];

    /**
     * Get the User that owns this Keyword.
     * 
     * @return BelongsTo Returns Eloquent BelongsTo Relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get Results for this Keyword.
     * 
     * @return HasMany Returns Eloquent HasMany Relation
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Enqueues generating a new report.
     * 
     * @return void
     */
    public function enqueueGenerateReport(): void
    {
        if (!$this->id) {
            throw new ModelMustBeSavedException('Keyword must be saved.');
        }

        KeywordJob::dispatch($this);
    }
}