<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('share_token', 64)->unique();
            $table->string('access_code_hash', 255);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('share_token');
            $table->index(['assessment_result_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_shares');
    }
};
