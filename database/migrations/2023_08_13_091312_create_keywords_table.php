<?php
/**
 * Create Keywords Table Migration
 * php version 8.2.4
 * 
 * @category CreateKeywordsTableMigration
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'keywords', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->index();
                $table->string('keyword')->index();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
