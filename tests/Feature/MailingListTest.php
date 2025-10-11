<?php

namespace Tests\Feature;

use App\Models\MailingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailingListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_subscribe_to_mailing_list()
    {
        $data = [
            'email' => 'subscriber@example.com',
        ];

        $response = $this->postJson('/api/v1/subscribe', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully subscribed to our mailing list!',
            ]);

        $this->assertDatabaseHas('mailing_list', [
            'email' => 'subscriber@example.com',
        ]);
    }

    /** @test */
    public function it_requires_email()
    {
        $data = [];

        $response = $this->postJson('/api/v1/subscribe', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_requires_valid_email()
    {
        $data = [
            'email' => 'invalid-email',
        ];

        $response = $this->postJson('/api/v1/subscribe', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_prevents_duplicate_subscriptions()
    {
        MailingList::create([
            'email' => 'subscriber@example.com',
        ]);

        $data = [
            'email' => 'subscriber@example.com',
        ];

        $response = $this->postJson('/api/v1/subscribe', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_limits_email_length()
    {
        $data = [
            'email' => str_repeat('a', 250) . '@example.com', // Very long email
        ];

        $response = $this->postJson('/api/v1/subscribe', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
