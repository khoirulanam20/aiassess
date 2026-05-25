<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('department')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('position')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('position_id');
        });

        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
