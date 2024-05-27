<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rituals', function (Blueprint $table) {
            $table->id();
            $table->string('company_ritual')->nullable();
            $table->string('phone_ritual')->nullable();
            $table->text('description_ritual')->nullable();
            $table->string('address_ritual')->nullable();
            $table->string('work_time_ritual')->nullable();
            $table->string('service_ritual')->nullable();
            $table->string('site_ritual')->nullable();
            $table->string('prices')->nullable();
            $table->string('social_network_ritual')->nullable();
            $table->string('path')->nullable();
            $table->string('name')->nullable();
            $table->string('comment')->nullable();
            $table->string('ratings')->nullable();
            $table->string('average_rating')->nullable();
            $table->integer('total_rating_requests')->default(0)->nullable();
            $table->integer('min_rating')->default(0)->nullable();
            $table->integer('max_rating')->default(0)->nullable();
            $table->string('files')->nullable();
            $table->string('views_count')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rituals');
    }
};
