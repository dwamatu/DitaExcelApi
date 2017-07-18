<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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
        $this->visit("/api/v1/units?where=true&names=PHL-112,PHY-113,COM-113")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&shift=Day")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&shift=Athi")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&shift=Evening")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&names=PHL-112,PHY-113,COM-113?shift=Day")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&names=PHL-112?shift=Athi?section=A")->assertResponseStatus(200)->seeJsonStructure(['results']);
        //$this->visit("/api/v1/units?where=true&equals=A")->assertResponseStatus(200)->seeJsonStructure(['results']);



    }
}
