<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ChatImageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    protected function createCustomer(string $name = 'Chat Customer'): User
    {
        return User::create([
            'name' => $name,
            'email' => uniqid('chat-cust-', true) . '@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);
    }

    protected function createAdmin(string $name = 'Chat Admin'): User
    {
        return User::create([
            'name' => $name,
            'email' => uniqid('chat-admin-', true) . '@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
    }

    public function test_user_can_send_chat_with_image(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('kerusakan_pipa.jpg', 600, 600);

        $response = $this->actingAs($user, 'web')->post(route('chatuser.send'), [
            'admin_id' => $admin->id,
            'message' => 'Ini foto pipa yang bocor',
            'image' => $file,
        ]);

        $response->assertRedirect(route('chatuser'));

        $this->assertDatabaseHas('chats', [
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => 'Ini foto pipa yang bocor',
        ]);

        $chat = Chat::where('user_id', $user->id)->first();
        $this->assertNotNull($chat->image);
        Storage::disk('public')->assertExists($chat->image);

        // Test polling endpoint includes image_url
        $pollResponse = $this->actingAs($user, 'web')->get(route('chatuser.messages'));
        $pollResponse->assertOk();
        $pollResponse->assertJsonFragment([
            'id' => $chat->id,
            'sender' => 'user',
            'message' => 'Ini foto pipa yang bocor',
            'image_url' => asset('storage/' . $chat->image),
        ]);
    }

    public function test_admin_can_send_chat_with_image(): void
    {
        $user = $this->createCustomer();
        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('suku_cadang.jpg', 400, 400);

        $response = $this->actingAs($admin, 'admin')->post(route('chat.send'), [
            'user_id' => $user->id,
            'message' => 'Ini suku cadang pengganti yang kami sarankan',
            'image' => $file,
        ]);

        $response->assertRedirect(route('chat', ['user_id' => $user->id]));

        $chat = Chat::where('user_id', $user->id)->where('sender', 'admin')->first();
        $this->assertNotNull($chat);
        $this->assertSame('Ini suku cadang pengganti yang kami sarankan', $chat->message);
        $this->assertNotNull($chat->image);
        Storage::disk('public')->assertExists($chat->image);

        // Test admin messages endpoint includes image_url
        $pollResponse = $this->actingAs($admin, 'admin')->get(route('chat.messages', ['user_id' => $user->id]));
        $pollResponse->assertOk();
        $pollResponse->assertJsonFragment([
            'id' => $chat->id,
            'sender' => 'admin',
            'image_url' => asset('storage/' . $chat->image),
        ]);
    }

    public function test_user_can_send_image_without_text(): void
    {
        $user = $this->createCustomer();
        $file = UploadedFile::fake()->image('hanya_foto.png', 500, 500);

        $response = $this->actingAs($user, 'web')->post(route('chatuser.send'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('chatuser'));

        $chat = Chat::where('user_id', $user->id)->first();
        $this->assertNotNull($chat);
        $this->assertNull($chat->message);
        $this->assertNotNull($chat->image);
    }
}
