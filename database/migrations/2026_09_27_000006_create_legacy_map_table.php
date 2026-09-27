<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('legacy_map', function (Blueprint $table) {
			$table->id();
			$table->string('legacy_table');
			$table->unsignedBigInteger('legacy_id');
			$table->string('legacy_column')->default('');
			$table->string('model_type');
			$table->unsignedBigInteger('model_id');
			$table->timestamps();

			$table->unique(['legacy_table', 'legacy_id', 'legacy_column']);
			$table->index(['model_type', 'model_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('legacy_map');
	}
};
