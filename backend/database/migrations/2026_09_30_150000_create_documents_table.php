<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('responsible_unit');
            $table->date('created_on');
            $table->string('url');
            $table->string('file_type', 32);
            $table->unsignedInteger('reading_time_minutes');
            $table->string('importance', 32);
            $table->string('category', 32);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(
                ['importance', 'category', 'is_active', 'created_on'],
                'documents_filter_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
