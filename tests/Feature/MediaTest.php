<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

it('uploads an image to temp storage', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $response = $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertOk()
        ->assertJsonStructure(['data' => ['uuid', 'file', 'original_name', 'width', 'height', '_temp']]);

    expect($response->json('data._temp'))->toBeTrue();

    Storage::disk('public')->assertExists('temp/' . $response->json('data.file'));
});

it('accepts pdf uploads', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file, 'profile' => 'document'])
        ->assertOk()
        ->assertJsonPath('data.mime_type', 'application/pdf')
        ->assertJsonPath('data.thumbnail_url', null);
});

it('rejects unsupported uploads', function () {
    $file = UploadedFile::fake()->create('notes.txt', 10, 'text/plain');

    $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertUnprocessable();
});

it('updates media alt and caption', function () {
    $media = Media::factory()->create(['alt' => '', 'caption' => '']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/media/{$media->uuid}", [
            'alt' => 'A nice photo',
            'caption' => 'Taken in Zurich',
        ])
        ->assertOk()
        ->assertJsonPath('data.alt', 'A nice photo')
        ->assertJsonPath('data.caption', 'Taken in Zurich');
});

it('deletes media and removes file from storage', function () {
    Storage::disk('public')->put('uploads/test.jpg', 'fake-content');
    $media = Media::factory()->create(['file' => 'test.jpg']);

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/media/{$media->uuid}")
        ->assertNoContent();

    expect(Media::count())->toBe(0);
    Storage::disk('public')->assertMissing('uploads/test.jpg');
});

it('reorders media', function () {
    $a = Media::factory()->create(['sort_order' => 0]);
    $b = Media::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/media/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with unknown uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/media/reorder', [
            'items' => [['uuid' => 'bad-uuid', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('toggles teaser on', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', true);
});

it('toggles teaser off when already set', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => true,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', false);
});

it('only allows one teaser per entity', function () {
    $project = Project::factory()->create();
    $first = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => true,
    ]);
    $second = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$second->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', true);

    expect($first->fresh()->is_teaser)->toBeFalse();
});

it('toggles og image on', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/og")
        ->assertOk()
        ->assertJsonPath('data.is_og', true);
});

it('only allows one og image per entity', function () {
    $project = Project::factory()->create();
    $first = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => true,
    ]);
    $second = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$second->uuid}/og")
        ->assertOk();

    expect($first->fresh()->is_og)->toBeFalse();
});

it('includes crop in media resource', function () {
    $media = Media::factory()->create([
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/projects/{$media->mediable->uuid}")
        ->assertOk()
        ->assertJsonPath('data.media.0.crop.x', 100)
        ->assertJsonPath('data.media.0.crop.y', 50)
        ->assertJsonPath('data.media.0.crop.w', 800)
        ->assertJsonPath('data.media.0.crop.h', 600);
});

it('appends crop param to thumbnail_url when crop is set', function () {
    $media = Media::factory()->create([
        'file' => 'test-image.jpg',
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/projects/{$media->mediable->uuid}")
        ->assertOk()
        ->assertJsonPath('data.media.0.thumbnail_url', '/img/uploads/test-image.jpg?w=400&h=400&fit=crop&crop=800,600,100,50');
});

it('does not append crop param when crop is null', function () {
    $media = Media::factory()->create([
        'file' => 'test-image.jpg',
        'crop' => null,
    ]);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/projects/{$media->mediable->uuid}")
        ->assertOk()
        ->assertJsonPath('data.media.0.thumbnail_url', '/img/uploads/test-image.jpg?w=400&h=400&fit=crop');
});

it('sets crop on media', function () {
    $media = Media::factory()->create(['crop' => null]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => 100, 'y' => 50, 'w' => 800, 'h' => 600,
        ])
        ->assertOk()
        ->assertJsonPath('data.crop.x', 100)
        ->assertJsonPath('data.crop.y', 50)
        ->assertJsonPath('data.crop.w', 800)
        ->assertJsonPath('data.crop.h', 600);

    expect($media->fresh()->crop)->toBe(['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600]);
});

it('clears crop on media when null values sent', function () {
    $media = Media::factory()->create([
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => null, 'y' => null, 'w' => null, 'h' => null,
        ])
        ->assertOk()
        ->assertJsonPath('data.crop', null);

    expect($media->fresh()->crop)->toBeNull();
});

it('rejects invalid crop values', function () {
    $media = Media::factory()->create();

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => 'bad', 'y' => 50, 'w' => 800, 'h' => 600,
        ])
        ->assertUnprocessable();
});

it('rejects partial crop values', function () {
    $media = Media::factory()->create();

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => 100, 'y' => null, 'w' => null, 'h' => null,
        ])
        ->assertUnprocessable();
});

it('requires authentication for media', function () {
    $this->postJson('/api/dashboard/media/upload')->assertUnauthorized();
});

it('restricts file types per media profile', function (string $profile, string $file, string $mime, bool $allowed) {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->postJson('/api/dashboard/media/upload', [
        'profile' => $profile,
        'file' => UploadedFile::fake()->create($file, 50, $mime),
    ]);

    $allowed ? $response->assertOk() : $response->assertJsonValidationErrors('file');
})->with([
    'project takes video' => ['project', 'clip.mp4', 'video/mp4', true],
    'project rejects pdf' => ['project', 'plan.pdf', 'application/pdf', false],
    'portrait rejects video' => ['portrait', 'clip.mp4', 'video/mp4', false],
    'document takes pdf' => ['document', 'plan.pdf', 'application/pdf', true],
    'document rejects image' => ['document', 'photo.jpg', 'image/jpeg', false],
]);

it('rejects unknown media profiles', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['profile' => 'nope', 'file' => UploadedFile::fake()->image('a.jpg')])
        ->assertJsonValidationErrors('profile');
});

it('exposes media profiles with crop ratios to the admin', function () {
    $this->actingAs($this->user)
        ->getJson('/api/dashboard/options')
        ->assertOk()
        ->assertJsonPath('media_profiles.portrait.crops.0.label', 'Portrait 2:3')
        ->assertJsonPath('media_profiles.portrait.crops.0.value', 0.666667)
        ->assertJsonPath('media_profiles.document.extensions', ['.pdf']);
});
