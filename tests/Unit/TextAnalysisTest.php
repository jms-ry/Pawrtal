<?php

namespace Tests\Unit;

use App\Helpers\TextAnalysisHelper;
use Tests\TestCase;

class TextAnalysisTest extends TestCase
{
  protected $analyzer;

  protected function setUp(): void
  {
    parent::setUp();
    $this->analyzer = new TextAnalysisHelper();
  }

  public function test_extracts_energetic_trait()
  {
    $description = "A very energetic and playful dog who loves to run.";
    $traits = $this->analyzer->extractTraits($description);
        
    $this->assertTrue($traits['energetic']);
    $this->assertTrue($traits['playful']);
  }

  public function test_extracts_calm_trait()
  {
    $description = "A calm and gentle cat, perfect for a quiet home.";
    $traits = $this->analyzer->extractTraits($description);
        
    $this->assertTrue($traits['calm']);
    $this->assertTrue($traits['gentle']);
  }

  public function test_extracts_child_friendly_trait()
  {
    $description = "Great with kids and very patient with children.";
    $traits = $this->analyzer->extractTraits($description);
        
    $this->assertTrue($traits['good_with_children']);
    $this->assertTrue($traits['patient']);
  }

  public function test_parses_age_in_years()
  {
    $this->assertEquals(24, $this->analyzer->parseAge("2 years old"));
    $this->assertEquals(12, $this->analyzer->parseAge("1 year"));
    $this->assertEquals(60, $this->analyzer->parseAge("5 years"));
  }

  public function test_parses_age_in_months()
  {
    $this->assertEquals(6, $this->analyzer->parseAge("6 months old"));
    $this->assertEquals(18, $this->analyzer->parseAge("18 months"));
  }

  public function test_parses_age_in_weeks()
  {
    $this->assertEquals(0.5, $this->analyzer->parseAge("2 weeks old"));
    $this->assertEquals(2, $this->analyzer->parseAge("8 weeks"));
  }

  public function test_handles_invalid_age()
  {
    $this->assertNull($this->analyzer->parseAge("unknown"));
    $this->assertNull($this->analyzer->parseAge(""));
    $this->assertNull($this->analyzer->parseAge(null));
  }
}