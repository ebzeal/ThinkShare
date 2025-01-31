<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testHomePage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Welcome to Laravel');
        $response->assertSeeText('The home page');
    }

    public function testContactPage() {
        $response = $this->get('/contact');

        $response->assertSeeText('Contact Page');
        $response->assertSeeText('This is contact');
    }
}
