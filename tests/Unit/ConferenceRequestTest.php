<?php

namespace Tests\Unit;

use App\Http\Requests\ConferenceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ConferenceRequestTest extends TestCase
{
    /** @test */
    public function it_validates_the_conference_request_fields()
    {
        // Arrange
        $data = [
            'title' => 'Conference Title',
            'description' => 'Conference description',
            'date' => now()->addDays(10),
            'address' => 'Some address',
            'participants' => 100,
        ];

        // Act
        $request = new ConferenceRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_when_validation_rules_are_not_met()
    {
        // Arrange
        $data = [
            'title' => '', // Invalid
            'description' => '',
            'date' => now()->subDays(10), // Invalid
            'address' => '',
            'participants' => 1, // Invalid
        ];

        // Act
        $request = new ConferenceRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
        $this->assertArrayHasKey('participants', $validator->errors()->toArray());
    }
}
