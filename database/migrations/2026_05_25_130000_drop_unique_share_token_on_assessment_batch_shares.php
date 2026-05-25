<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_batch_shares', function (Blueprint $table) {
            $table->dropUnique('assessment_batch_shares_share_token_unique');
            $table->string('share_token', 64)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assessment_batch_shares', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable(false)->change();
            $table->unique('share_token');
        });
    }
};
