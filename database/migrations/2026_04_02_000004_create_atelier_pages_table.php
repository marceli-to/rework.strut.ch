<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('atelier_pages', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('slug')->unique();
			$table->string('title')->nullable();
			$table->text('text')->nullable();
			$table->boolean('publish')->default(false);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('atelier_pages');
	}
};
