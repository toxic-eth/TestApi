<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_check_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('owner_uuid');
            $table->boolean('db_ok');
            $table->boolean('cache_ok');
            $table->unsignedSmallInteger('response_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_check_requests');
    }
};