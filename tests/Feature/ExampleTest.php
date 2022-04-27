<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function convert_image_pdf(): void
    {
        $this->get('api/generate-pdf')
            ->assertSuccessful();
    }
}
