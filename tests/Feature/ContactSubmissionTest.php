<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_submit_a_contact_form()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thank you for contacting us. We will get back to you soon.',
            ]);

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'This is a test message',
            'status' => 'new',
        ]);
    }

    /** @test */
    public function it_requires_name()
    {
        $data = [
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_requires_phone()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /** @test */
    public function it_requires_email()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_requires_valid_email()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'invalid-email',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_requires_message()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function it_limits_message_length()
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => str_repeat('a', 5001), // 5001 characters
        ];

        $response = $this->postJson('/api/v1/contact', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
