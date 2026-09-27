<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('grid_rows', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->morphs('gridable');
			$table->string('area')->default('main');
			$table->string('layout');
			$table->boolean('publish')->default(true);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();

			$table->index(['gridable_type', 'gridable_id', 'area', 'sort_order']);
		});

		Schema::create('grid_items', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->foreignId('grid_row_id')->constrained()->cascadeOnDelete();
			$table->unsignedInteger('position');
			$table->foreignId('media_id')->nullable()->constrained('media')->cascadeOnDelete();
			$table->foreignId('news_id')->nullable()->constrained('news')->cascadeOnDelete();
			$table->timestamps();

			$table->unique(['grid_row_id', 'position']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('grid_items');
		Schema::dropIfExists('grid_rows');
	}
};
