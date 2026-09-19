<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root displays welcome portal.
     */
    public function test_the_application_root_returns_portal_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SMA KARTIKA III-1 BANYUBIRU');
    }

    public function test_portal_route_returns_portal_page(): void
    {
        $response = $this->get('/portal');
        $response->assertStatus(200);
    }
}
