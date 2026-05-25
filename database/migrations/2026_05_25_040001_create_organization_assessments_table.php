<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('cooldown_days')->nullable();
            $table->string('custom_name')->nullable();
            $table->text('custom_description')->nullable();
            $table->text('custom_instructions')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_assessments');
    }
};
