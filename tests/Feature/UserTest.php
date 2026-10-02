<?php

use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create();
});

it('deletes other users', function () {
	$other = User::factory()->create();

	$this->actingAs($this->user)->deleteJson("/api/dashboard/users/{$other->uuid}")->assertNoContent();

	expect(User::count())->toBe(1);
});

it('does not delete the own account', function () {
	$this->actingAs($this->user)
		->deleteJson("/api/dashboard/users/{$this->user->uuid}")
		->assertForbidden()
		->assertJsonPath('message', 'Das eigene Konto kann nicht gelöscht werden.');

	expect($this->user->fresh()->trashed())->toBeFalse();
});

it('marks the own account in the list', function () {
	User::factory()->create();

	$users = $this->actingAs($this->user)->getJson('/api/dashboard/users')->assertOk()->json('data');

	expect(collect($users)->where('is_self', true)->pluck('uuid')->all())->toBe([$this->user->uuid]);
});
