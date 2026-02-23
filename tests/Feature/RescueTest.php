<?php

namespace Tests\Feature;

use App\Models\AdoptionApplication;
use App\Models\Rescue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescueTest extends TestCase
{
  use RefreshDatabase;

  // Rescue Model Tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function makeRescue(array $attributes = []): Rescue
  {
    return Rescue::factory()->make($attributes);
  }

  private function createRescue(array $attributes = []): Rescue
  {
    return Rescue::factory()->create($attributes);
  }

  // -------------------------------------------------------------------------
  // isAdopted(), isAvailable(), isUnavailable()
  // -------------------------------------------------------------------------

  public function test_is_adopted_returns_true_when_adoption_status_is_adopted(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'adopted']);

    $this->assertTrue($rescue->isAdopted());
  }

  public function test_is_adopted_returns_false_when_adoption_status_is_not_adopted(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'available']);

    $this->assertFalse($rescue->isAdopted());
  }

  public function test_is_available_returns_true_when_adoption_status_is_available(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'available']);

    $this->assertTrue($rescue->isAvailable());
  }

  public function test_is_available_returns_false_when_adoption_status_is_not_available(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'adopted']);

    $this->assertFalse($rescue->isAvailable());
  }

  public function test_is_unavailable_returns_true_when_adoption_status_is_unavailable(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'unavailable']);

    $this->assertTrue($rescue->isUnavailable());
  }

  public function test_is_unavailable_returns_false_when_adoption_status_is_not_unavailable(): void
  {
    $rescue = $this->makeRescue(['adoption_status' => 'available']);

    $this->assertFalse($rescue->isUnavailable());
  }

  // -------------------------------------------------------------------------
  // getNameFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_name_formatted_returns_headline_cased_name_for_active_rescue(): void
  {
    $rescue = $this->makeRescue(['name' => 'buddy']);

    $this->assertSame('Buddy', $rescue->name_formatted);
  }

  public function test_name_formatted_appends_archived_label_for_soft_deleted_rescue(): void
  {
    $rescue = $this->createRescue(['name' => 'buddy']);
    $rescue->delete();

    $this->assertSame('Buddy (Archived)', $rescue->fresh()->name_formatted);
  }

  // -------------------------------------------------------------------------
  // getProfileImageUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_profile_image_url_uses_storage_path_when_image_contains_profile_images_directory(): void
  {
    $rescue = $this->makeRescue(['profile_image' => 'profile_images/dog.jpg']);

    $this->assertSame(asset('storage/profile_images/dog.jpg'), $rescue->profile_image_url);
  }

  public function test_profile_image_url_uses_asset_path_when_image_does_not_contain_profile_images_directory(): void
  {
    $rescue = $this->makeRescue(['profile_image' => 'images/default.jpg']);

    $this->assertSame(asset('images/default.jpg'), $rescue->profile_image_url);
  }

  // -------------------------------------------------------------------------
  // getImagesUrlAttribute()
  // -------------------------------------------------------------------------

  public function test_images_url_returns_empty_array_when_images_is_empty(): void
  {
    $rescue = $this->makeRescue(['images' => []]);

    $this->assertSame([], $rescue->images_url);
  }

  public function test_images_url_uses_storage_path_for_gallery_images(): void
  {
    $rescue = $this->makeRescue(['images' => ['/gallery_images/photo.jpg']]);

    $this->assertSame([asset('storage//gallery_images/photo.jpg')], $rescue->images_url);
  }

  public function test_images_url_uses_asset_path_for_non_gallery_images(): void
  {
    $rescue = $this->makeRescue(['images' => ['images/photo.jpg']]);

    $this->assertSame([asset('images/photo.jpg')], $rescue->images_url);
  }

  public function test_images_url_handles_mixed_gallery_and_non_gallery_images(): void
  {
    $rescue = $this->makeRescue(['images' => [
      '/gallery_images/photo1.jpg',
      'images/photo2.jpg',
    ]]);

    $expected = [
      asset('storage//gallery_images/photo1.jpg'),
      asset('images/photo2.jpg'),
    ];

    $this->assertSame($expected, $rescue->images_url);
  }

  // -------------------------------------------------------------------------
  // getAgeFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_age_formatted_returns_ucfirst_lowercased_age_when_set(): void
  {
    $rescue = $this->makeRescue(['age' => 'YOUNG']);

    $this->assertSame('Young', $rescue->age_formatted);
  }

  public function test_age_formatted_returns_unknown_when_age_is_null(): void
  {
    $rescue = $this->makeRescue(['age' => null]);

    $this->assertSame('Unknown', $rescue->age_formatted);
  }

  // -------------------------------------------------------------------------
  // getSizeFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_size_formatted_returns_headline_cased_size_when_set(): void
  {
    $rescue = $this->makeRescue(['size' => 'medium']);

    $this->assertSame('Medium', $rescue->size_formatted);
  }

  public function test_size_formatted_returns_unknown_when_size_is_null(): void
  {
    $rescue = $this->makeRescue(['size' => null]);

    $this->assertSame('Unknown', $rescue->size_formatted);
  }

  // -------------------------------------------------------------------------
  // getSpayedNeuteredFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_spayed_neutered_formatted_returns_yes_when_true(): void
  {
    $rescue = $this->makeRescue(['spayed_neutered' => true]);

    $this->assertSame('Yes', $rescue->spayed_neutered_formatted);
  }

  public function test_spayed_neutered_formatted_returns_no_when_false(): void
  {
    $rescue = $this->makeRescue(['spayed_neutered' => false]);

    $this->assertSame('No', $rescue->spayed_neutered_formatted);
  }

  // -------------------------------------------------------------------------
  // getDescriptionFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_description_formatted_adds_period_if_missing(): void
  {
    $rescue = $this->makeRescue(['description' => 'he is a good dog']);

    $this->assertStringEndsWith('.', $rescue->description_formatted);
  }

  public function test_description_formatted_does_not_add_extra_period_if_already_present(): void
  {
    $rescue = $this->makeRescue(['description' => 'He is a good dog.']);

    $this->assertSame('He is a good dog.', $rescue->description_formatted);
  }

  public function test_description_formatted_capitalizes_each_sentence(): void
  {
    $rescue = $this->makeRescue(['description' => 'he is friendly.she loves kids.']);

    $this->assertSame('He is friendly. She loves kids.', $rescue->description_formatted);
  }

  public function test_description_formatted_lowercases_then_capitalizes_sentences(): void
  {
    $rescue = $this->makeRescue(['description' => 'HE IS FRIENDLY. SHE LOVES KIDS.']);

    $this->assertSame('He is friendly. She loves kids.', $rescue->description_formatted);
  }

  // -------------------------------------------------------------------------
  // distinctiveFeatures() / getDistinctiveFeaturesFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_distinctive_features_returns_none_when_null(): void
  {
    $rescue = $this->makeRescue(['distinctive_features' => null]);

    $this->assertSame('None', $rescue->distinctive_features_formatted);
  }

  public function test_distinctive_features_adds_period_if_missing(): void
  {
    $rescue = $this->makeRescue(['distinctive_features' => 'has a white patch on chest']);

    $this->assertStringEndsWith('.', $rescue->distinctive_features_formatted);
  }

  public function test_distinctive_features_capitalizes_each_sentence(): void
  {
    $rescue = $this->makeRescue(['distinctive_features' => 'white patch on chest.missing left ear.']);

    $this->assertSame('White patch on chest. Missing left ear.', $rescue->distinctive_features_formatted);
  }

  public function test_distinctive_features_lowercases_then_capitalizes_sentences(): void
  {
    $rescue = $this->makeRescue(['distinctive_features' => 'WHITE PATCH.MISSING EAR.']);

    $this->assertSame('White patch. Missing ear.', $rescue->distinctive_features_formatted);
  }

  // -------------------------------------------------------------------------
  // tagLabel()
  // -------------------------------------------------------------------------

  public function test_tag_label_combines_sex_formatted_and_age_formatted(): void
  {
    $rescue = $this->makeRescue(['sex' => 'male', 'age' => 'young']);

    $this->assertSame('Male, Young', $rescue->tag_label);
  }

  public function test_tag_label_shows_unknown_age_when_age_is_null(): void
  {
    $rescue = $this->makeRescue(['sex' => 'female', 'age' => null]);

    $this->assertSame('Female, Unknown', $rescue->tag_label);
  }

  // -------------------------------------------------------------------------
  // getAdoptionApplicationsCountFormattedAttribute()
  // -------------------------------------------------------------------------

  public function test_adoption_applications_count_formatted_returns_no_applications_message_when_zero(): void
  {
    $rescue = $this->createRescue();

    $rescue->adoption_applications_count = 0;

    $this->assertSame(
      'This rescue has no adoption applications.',
      $rescue->adoption_applications_count_formatted
    );
  }

  public function test_adoption_applications_count_formatted_returns_singular_when_count_is_one(): void
  {
    $rescue = $this->createRescue();

    $rescue->adoption_applications_count = 1;

    $this->assertSame(
      'This rescue has 1 adoption application.',
      $rescue->adoption_applications_count_formatted
    );
  }

  public function test_adoption_applications_count_formatted_returns_plural_when_count_is_more_than_one(): void
  {
    $rescue = $this->createRescue();

    $rescue->adoption_applications_count = 3;

    $this->assertSame(
      'This rescue has 3 adoption applications.',
      $rescue->adoption_applications_count_formatted
    );
  }

  // -------------------------------------------------------------------------
  // scopeVisibleTo()
  // -------------------------------------------------------------------------

  public function test_scope_visible_to_includes_soft_deleted_rescues_for_admin(): void
  {
    $admin  = User::factory()->create(['role' => 'admin']);
    $rescue = $this->createRescue();
    $rescue->delete();

    $results = Rescue::visibleTo($admin)->get();

    $this->assertTrue($results->contains('id', $rescue->id));
  }

  public function test_scope_visible_to_includes_soft_deleted_rescues_for_staff(): void
  {
    $staff  = User::factory()->create(['role' => 'staff']);
    $rescue = $this->createRescue();
    $rescue->delete();

    $results = Rescue::visibleTo($staff)->get();

    $this->assertTrue($results->contains('id', $rescue->id));
  }

  public function test_scope_visible_to_excludes_soft_deleted_rescues_for_regular_user(): void
  {
    $user   = User::factory()->create(['role' => 'regular_user']);
    $rescue = $this->createRescue();
    $rescue->delete();

    $results = Rescue::visibleTo($user)->get();

    $this->assertFalse($results->contains('id', $rescue->id));
  }

  public function test_scope_visible_to_excludes_soft_deleted_rescues_when_user_is_null(): void
  {
    $rescue = $this->createRescue();
    $rescue->delete();

    $results = Rescue::visibleTo(null)->get();

    $this->assertFalse($results->contains('id', $rescue->id));
  }
}