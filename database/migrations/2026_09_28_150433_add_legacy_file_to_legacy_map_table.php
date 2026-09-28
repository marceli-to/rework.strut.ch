<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy file name of imported media, so old /storage/media/... and
 * /media/... URLs can be redirected (Q10) without the legacy database.
 */
return new class extends Migration
{
	public function up(): void
	{
		Schema::table('legacy_map', function (Blueprint $table) {
			$table->string('legacy_file')->nullable()->after('legacy_column')->index();
		});
	}

	public function down(): void
	{
		Schema::table('legacy_map', function (Blueprint $table) {
			$table->dropColumn('legacy_file');
		});
	}
};
