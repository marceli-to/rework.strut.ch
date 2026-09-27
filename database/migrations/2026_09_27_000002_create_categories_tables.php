<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('categories', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('name');
			$table->boolean('show_types')->default(true);
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0)->index();
			$table->timestamps();
		});

		Schema::create('category_types', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->foreignId('category_id')->constrained()->cascadeOnDelete();
			$table->string('name_singular');
			$table->string('name_plural');
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();

			$table->index(['category_id', 'sort_order']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('category_types');
		Schema::dropIfExists('categories');
	}
};
