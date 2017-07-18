<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BaseControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use WithoutMiddleware;
    use DatabaseTransactions;


    public function testFetch()
    {
        $this->visit("/api/v1/units?where=name&equals=PHL-112,PHY-113,COM-113")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/fetch/units/shift?units=PHL-112,PHY-113,COM-113")->assertResponseStatus(200)->seeJsonStructure(['results']);

    }
}
