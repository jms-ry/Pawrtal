<?php

namespace Tests\Feature;

use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdTest extends TestCase
{
  use RefreshDatabase;

  // Household model tests
  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private function makeHousehold(array $attributes = []): Household
  {
    return Household::factory()->make($attributes);
  }

  // -------------------------------------------------------------------------
  // houseStructure()
  // -------------------------------------------------------------------------

  public function test_house_structure_returns_ucfirst_value(): void
  {
    $household = $this->makeHousehold(['house_structure' => 'apartment']);

    $this->assertSame('Apartment', $household->houseStructure());
  }

  public function test_house_structure_handles_all_caps_input(): void
  {
    $household = $this->makeHousehold(['house_structure' => 'HOUSE']);

    $this->assertSame('HOUSE', $household->houseStructure());
  }

  // -------------------------------------------------------------------------
  // numberOfChildren()
  // -------------------------------------------------------------------------

  public function test_number_of_children_returns_singular_message_when_count_is_one(): void
  {
    $household = $this->makeHousehold(['number_of_children' => 1]);

    $this->assertSame('There is 1 child in this household.', $household->numberOfChildren());
  }

  public function test_number_of_children_returns_plural_message_when_count_is_greater_than_one(): void
  {
    $household = $this->makeHousehold(['number_of_children' => 3]);

    $this->assertSame('There are 3 children in this household.', $household->numberOfChildren());
  }

  public function test_number_of_children_returns_na_when_count_is_zero(): void
  {
    $household = $this->makeHousehold(['number_of_children' => 0]);

    $this->assertSame('N/A', $household->numberOfChildren());
  }

  public function test_number_of_children_returns_na_when_count_is_null(): void
  {
    $household = $this->makeHousehold(['number_of_children' => null]);

    $this->assertSame('N/A', $household->numberOfChildren());
  }

  // -------------------------------------------------------------------------
  // numberOfCurrentPets()
  // -------------------------------------------------------------------------

  public function test_number_of_current_pets_returns_singular_message_when_count_is_one(): void
  {
    $household = $this->makeHousehold(['number_of_current_pets' => 1]);

    $this->assertSame('There is 1 pet in this household.', $household->numberOfCurrentPets());
  }

  public function test_number_of_current_pets_returns_plural_message_when_count_is_greater_than_one(): void
  {
    $household = $this->makeHousehold(['number_of_current_pets' => 4]);

    $this->assertSame('There are 4 pets in this household.', $household->numberOfCurrentPets());
  }

  public function test_number_of_current_pets_returns_na_when_count_is_zero(): void
  {
    $household = $this->makeHousehold(['number_of_current_pets' => 0]);

    $this->assertSame('N/A', $household->numberOfCurrentPets());
  }

  public function test_number_of_current_pets_returns_na_when_count_is_null(): void
  {
    $household = $this->makeHousehold(['number_of_current_pets' => null]);

    $this->assertSame('N/A', $household->numberOfCurrentPets());
  }

  // -------------------------------------------------------------------------
  // currentPets()
  // -------------------------------------------------------------------------

  public function test_current_pets_returns_the_value_when_set(): void
  {
    $household = $this->makeHousehold(['current_pets' => 'Golden Retriever, Siamese Cat']);

    $this->assertSame('Golden Retriever, Siamese Cat', $household->currentPets());
  }

  public function test_current_pets_returns_na_when_null(): void
  {
    $household = $this->makeHousehold(['current_pets' => null]);

    $this->assertSame('N/A', $household->currentPets());
  }

  public function test_current_pets_returns_na_when_empty_string(): void
  {
    $household = $this->makeHousehold(['current_pets' => '']);

    $this->assertSame('N/A', $household->currentPets());
  }
}