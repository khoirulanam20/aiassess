<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_batch_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('share_token', 64)->unique();
            $table->string('access_code_hash', 255);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['assessment_batch_id', 'user_id']);
            $table->index('share_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_batch_shares');
    }
};
