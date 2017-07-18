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
        $this->visit("/api/v1/units?where=shift&equals=Day")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=shift&equals=Athi")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=shift&equals=Evening")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=section&equals=A")->assertResponseStatus(200)->seeJsonStructure(['results']);



    }
}
