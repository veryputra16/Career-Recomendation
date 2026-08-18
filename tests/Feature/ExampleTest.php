<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the initial Inertia Welcome page is rendered with required props.
     */
    public function test_the_application_renders_inertia_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('appName')
            ->has('laravelVersion')
            ->has('phpVersion')
        );
    }
}
