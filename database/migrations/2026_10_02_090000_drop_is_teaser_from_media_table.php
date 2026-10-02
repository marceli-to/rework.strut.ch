<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The teaser flag (from the Template) had no effect on the site; legacy had none.
 */
return new class extends Migration
{
	public function up(): void
	{
		Schema::table('media', function (Blueprint $table) {
			$table->dropColumn('is_teaser');
		});
	}

	public function down(): void
	{
		Schema::table('media', function (Blueprint $table) {
			$table->boolean('is_teaser')->default(false)->after('variant');
		});
	}
};
