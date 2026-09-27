<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('projects', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('title');
			$table->string('location');
			$table->string('slug')->unique();
			$table->string('subtitle')->nullable();
			$table->integer('year');
			$table->text('description')->nullable();
			$table->text('info')->nullable();
			$table->string('meta_description')->nullable();
			$table->boolean('publish')->default(false);
			$table->boolean('feature')->default(false);
			$table->integer('sort_order')->default(0);
			$table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('projects');
	}
};
