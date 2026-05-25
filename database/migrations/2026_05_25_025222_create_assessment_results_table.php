<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('test_name', 50);
            $table->longText('result')->nullable();
            $table->longText('scores')->nullable();
            $table->longText('answers')->nullable();
            $table->string('stt', 20)->nullable();
            $table->longText('transkrip')->nullable();
            $table->string('status', 20)->default('completed');
            $table->string('share_token', 100)->nullable()->unique();
            $table->unsignedInteger('share_views')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('test_name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
