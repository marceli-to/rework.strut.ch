<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('landing_meta_description', 500)->nullable();
            $table->string('projects_meta_description', 500)->nullable();
            $table->string('werkliste_meta_description', 500)->nullable();
            $table->string('profile_meta_description', 500)->nullable();
            $table->string('team_meta_description', 500)->nullable();
            $table->string('jobs_meta_description', 500)->nullable();
            $table->string('contact_meta_description', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
