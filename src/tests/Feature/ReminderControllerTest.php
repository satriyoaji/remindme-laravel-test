<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reminder;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReminderControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /** @test */
    public function it_can_create_a_reminder()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = Auth::loginUsingId(1);

        $data = [
            'title' => 'Meeting with Bob',
            'description' => 'Discuss about new project related to new system',
            'remind_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'event_at' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ];
        $response = $this->postJson('/api/reminders', $data, [
            'Authorization' => 'Bearer '. $user->createToken('access_token')->accessToken
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Reminder created successfully',
                'data' => [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'remind_at' => strtotime($data['remind_at']),
                    'event_at' => strtotime($data['event_at']),
                ]
            ]);

        $this->assertDatabaseHas('reminders', $data);
    }

    /** @test */
    public function it_requires_title_remind_at_and_event_at_fields()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = Auth::loginUsingId(1);

        $response = $this->postJson('/api/reminders', [], [
            'Authorization' => 'Bearer '. $user->createToken('access_token')->accessToken
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'msg' => [
                    'title', 'remind_at', 'event_at'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_reminder()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = Auth::loginUsingId(1);

        // Arrange
        $reminder = Reminder::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'remind_at' => now()->addDay(),
            'event_at' => now()->addWeek(),
        ];

        // Act
        $response = $this->put("/api/reminders/{$reminder->id}", $data, [
            'Authorization' => 'Bearer '. $user->createToken('access_token')->accessToken
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Reminder updated successfully',
                'data' => [
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                ],
            ]);

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function it_can_view_a_reminder()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        // Arrange
        $user = Auth::loginUsingId(1); // You may need to adjust this based on your user model
        $reminder = Reminder::factory()->create();

        // Act
        $response = $this->get("/api/reminders/{$reminder->id}", [
            'Authorization' => 'Bearer ' . $user->createToken('access_token')->accessToken,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'data' => [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'description' => $reminder->description,
                ],
            ]);
    }

    /** @test */
    public function it_can_delete_a_reminder()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        // Arrange
        $user = Auth::loginUsingId(1); // You may need to adjust this based on your user model
        $reminder = Reminder::factory()->create();

        // Act
        $response = $this->delete("/api/reminders/{$reminder->id}", [
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
            ]);

        $this->assertDatabaseMissing('reminders', ['id' => $reminder->id]);
    }
}
