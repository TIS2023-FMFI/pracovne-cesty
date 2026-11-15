<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\DatabaseTestCase;

class HomepageTest extends DatabaseTestCase
{
    public function test_root_address_redirects_to_the_homepage(): void
    {
        $this->get('/')->assertRedirect(route('homepage'));
    }

    public function test_guest_sees_the_public_homepage_with_the_login_form(): void
    {
        $this->get(route('homepage'))
            ->assertOk()
            ->assertViewIs('homepage');
    }

    public function test_logged_in_user_sees_the_list_of_trips_instead(): void
    {
        $user = User::factory()->employee()->create();

        $this->actingAs($user)->get(route('homepage'))
            ->assertOk()
            ->assertViewIs('dashboard');
    }
}
