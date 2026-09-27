<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('landing_slides', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->enum('type', ['image', 'image_text']);
			$table->text('text')->nullable();
			$table->boolean('publish')->default(false);
			$table->integer('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('landing_slides');
	}
};
