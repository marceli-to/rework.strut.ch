<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('landing_slides', function (Blueprint $table) {
			$table->string('link_type')->nullable()->after('text');
			$table->string('link_url')->nullable()->after('link_type');
		});
	}

	public function down(): void
	{
		Schema::table('landing_slides', function (Blueprint $table) {
			$table->dropColumn(['link_type', 'link_url']);
		});
	}
};
