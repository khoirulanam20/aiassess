<?php

use App\Models\AssessmentBatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_batches', function (Blueprint $table) {
            $table->string('guest_share_token', 64)->nullable()->unique()->after('status');
        });

        AssessmentBatch::query()->each(function (AssessmentBatch $batch) {
            $token = Str::random(48);
            $batch->update(['guest_share_token' => $token]);
            $batch->shares()->update(['share_token' => $token]);
        });
    }

    public function down(): void
    {
        Schema::table('assessment_batches', function (Blueprint $table) {
            $table->dropColumn('guest_share_token');
        });
    }
};
