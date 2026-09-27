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
			$table->foreignId('category_type_id')->constrained()->restrictOnDelete();
			$table->string('title')->nullable();
			$table->string('name');
			$table->string('location');
			$table->string('slug')->unique();
			$table->unsignedSmallInteger('year')->index();
			$table->text('description')->nullable();
			$table->text('info')->nullable();
			$table->string('status');
			$table->string('competition')->nullable();
			$table->boolean('has_detail')->default(false);
			$table->string('meta_description')->nullable();
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();

			$table->index(['category_type_id', 'sort_order']);
			$table->index(['publish', 'has_detail']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('projects');
	}
};
