<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('media', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->nullableMorphs('mediable');
			$table->string('collection')->default('images');
			$table->string('file');
			$table->string('original_name')->nullable();
			$table->string('mime_type')->nullable();
			$table->unsignedBigInteger('size')->nullable();
			$table->string('alt')->nullable();
			$table->text('caption')->nullable();
			$table->unsignedInteger('width')->nullable();
			$table->unsignedInteger('height')->nullable();
			$table->json('crop')->nullable();
			$table->string('variant')->default('desktop');
			$table->boolean('is_teaser')->default(false);
			$table->boolean('is_og')->default(false);
			$table->integer('sort_order')->default(0);
			$table->timestamps();

			$table->index(['mediable_type', 'mediable_id', 'collection', 'sort_order']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('media');
	}
};
