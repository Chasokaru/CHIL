<?php

namespace Tests\Unit;

use App\Models\Conference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ConferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_conference_index_page()
    {
// Arrange: Create some conferences
        $conferences = Conference::factory()->count(5)->create();

// Act: Fetch the conferences index
        $response = $this->get(route('conferences.index'));

// Assert: Check the response and data
        $response->assertStatus(200);
        $response->assertViewHas('conferences');
    }

    /** @test */
    public function it_creates_a_new_conference()
    {
// Arrange: Data for creating a new conference
        $conferenceData = [
            'title' => 'New Conference',
            'description' => 'A detailed description.',
            'date' => now()->addDays(10),
            'address' => 'Random Street 10, City, Country',
            'participants' => 100,
        ];

// Act: Post request to store a new conference
        $response = $this->post(route('conferences.store'), $conferenceData);

// Assert: Check if conference was created and database contains it
        $response->assertRedirect(route('conferences.index'));
        $this->assertDatabaseHas('conferences', $conferenceData);
    }

    /** @test */
    public function it_shows_the_create_conference_form()
    {
// Act: Visit the create form
        $response = $this->get(route('conferences.create'));

// Assert: Ensure the form is displayed
        $response->assertStatus(200);
        $response->assertViewHas('instructions');
    }

    /** @test */
    public function it_updates_a_conference()
    {
// Arrange: Create a conference to update
        $conference = Conference::factory()->create();
        $updatedData = [
            'title' => 'Updated Conference Title',
            'description' => 'Updated description.',
            'date' => now()->addDays(15),
            'address' => 'Updated Street 100, City, Country',
            'participants' => 150,
        ];

// Act: Send request to update conference
        $response = $this->put(route('conferences.update', $conference), $updatedData);

// Assert: Check if the conference is updated
        $response->assertRedirect(route('conferences.index'));
        $this->assertDatabaseHas('conferences', $updatedData);
    }

    /** @test */
    public function it_deletes_a_conference()
    {
// Arrange: Create a conference to delete
        $conference = Conference::factory()->create();

// Act: Send request to delete the conference
        $response = $this->delete(route('conferences.destroy', $conference));

// Assert: Check if the conference is deleted
        $response->assertRedirect(route('conferences.index'));
        $this->assertDeleted($conference);
    }

    /** @test */
    public function it_validates_sorting_parameters()
    {
// Act: Make a request with invalid sorting parameters
        $response = $this->get(route('conferences.index', ['sortField' => 'invalidField', 'sortDirection' => 'asc']));

// Assert: Check if an error message is returned
        $response->assertRedirect(route('conferences.index'));
        $response->assertSessionHasErrors('Invalid sorting parameters.');
    }
}
