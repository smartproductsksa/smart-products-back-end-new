<?php

namespace Tests\Unit;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_contact_submission()
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
            'status' => 'new',
        ]);

        $this->assertInstanceOf(ContactSubmission::class, $submission);
        $this->assertEquals('John Doe', $submission->name);
        $this->assertEquals('+1234567890', $submission->phone);
        $this->assertEquals('john@example.com', $submission->email);
        $this->assertEquals('This is a test message', $submission->message);
        $this->assertEquals('new', $submission->status);
    }

    /** @test */
    public function it_has_default_status_of_new()
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ]);

        $this->assertEquals('new', $submission->fresh()->status);
    }

    /** @test */
    public function it_can_update_status()
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
            'status' => 'new',
        ]);

        $submission->update(['status' => 'resolved']);

        $this->assertEquals('resolved', $submission->fresh()->status);
    }

    /** @test */
    public function it_can_add_admin_notes()
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
            'status' => 'new',
        ]);

        $submission->update(['admin_notes' => 'Customer called back']);

        $this->assertEquals('Customer called back', $submission->fresh()->admin_notes);
    }
}
