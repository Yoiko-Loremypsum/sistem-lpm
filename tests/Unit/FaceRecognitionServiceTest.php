<?php

namespace Tests\Unit;

use App\Services\FaceRecognitionService;
use PHPUnit\Framework\TestCase;

class FaceRecognitionServiceTest extends TestCase
{
    private FaceRecognitionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FaceRecognitionService();
    }

    public function test_validates_128_float_descriptor()
    {
        $valid = array_fill(0, 128, 0.05);
        $this->assertTrue($this->service->isValidDescriptor($valid));

        $invalidCount = array_fill(0, 50, 0.05);
        $this->assertFalse($this->service->isValidDescriptor($invalidCount));

        $invalidType = array_fill(0, 127, 0.05);
        $invalidType[] = 'not-a-number';
        $this->assertFalse($this->service->isValidDescriptor($invalidType));
    }

    public function test_compares_identical_descriptors()
    {
        $d1 = array_fill(0, 128, 0.123);
        $d2 = array_fill(0, 128, 0.123);

        $result = $this->service->compare($d1, $d2, 0.50);

        $this->assertTrue($result['match']);
        $this->assertEquals(0.0, $result['distance']);
    }

    public function test_compares_different_descriptors()
    {
        $d1 = array_fill(0, 128, 0.0);
        $d2 = array_fill(0, 128, 0.5); // Euclidean distance = sqrt(128 * 0.25) = sqrt(32) ≈ 5.6568

        $result = $this->service->compare($d1, $d2, 0.50);

        $this->assertFalse($result['match']);
        $this->assertGreaterThan(0.50, $result['distance']);
    }
}
