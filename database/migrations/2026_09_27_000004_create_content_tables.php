<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('pages', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('key')->unique();
			$table->string('title');
			$table->text('text')->nullable();
			$table->string('meta_description')->nullable();
			$table->boolean('publish')->default(true);
			$table->timestamps();
		});

		Schema::create('news', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('date_label')->nullable();
			$table->string('title');
			$table->string('subtitle')->nullable();
			$table->text('text')->nullable();
			$table->string('link_url')->nullable();
			$table->string('link_label')->nullable();
			$table->boolean('publish')->default(false)->index();
			$table->timestamps();
		});

		Schema::create('team_members', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('firstname');
			$table->string('lastname');
			$table->string('role')->nullable();
			$table->string('position')->nullable();
			$table->string('phone')->nullable();
			$table->string('email')->nullable();
			$table->text('cv')->nullable();
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0)->index();
			$table->timestamps();
		});

		Schema::create('job_listings', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('title');
			$table->string('lead')->nullable();
			$table->text('info')->nullable();
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0)->index();
			$table->timestamps();
		});

		Schema::create('books', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('title');
			$table->text('description')->nullable();
			$table->text('info')->nullable();
			$table->string('url')->nullable();
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0)->index();
			$table->timestamps();
		});

		Schema::create('entries', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('type');
			$table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
			$table->string('title');
			$table->string('description')->nullable();
			$table->unsignedSmallInteger('year');
			$table->string('url')->nullable();
			$table->boolean('publish')->default(false);
			$table->timestamps();

			$table->index(['type', 'year']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('entries');
		Schema::dropIfExists('books');
		Schema::dropIfExists('job_listings');
		Schema::dropIfExists('team_members');
		Schema::dropIfExists('news');
		Schema::dropIfExists('pages');
	}
};
