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
    //use WithoutMiddleware;
    use DatabaseTransactions;


    public function testFetch()
    {
        $units = factory(\App\Unit::class, 100)->create();

        //$this->visit("/api/v1/units?where=true&names=PHL-112,PHY-113,COM-113")->assertResponseStatus(200)->seeJsonStructure(['results']);
        //$this->visit("/api/v1/units?where=true&shift=Day")->assertResponseStatus(200)->seeJsonStructure(['results']);
        //$this->visit("/api/v1/units?where=true&shift=Athi")->assertResponseStatus(200)->seeJsonStructure(['results']);
        //$this->visit("/api/v1/units?where=true&shift=Evening")->assertResponseStatus(200)->seeJsonStructure(['results']);
        //$this->visit("/api/v1/units?where=true&names=PHL-112,PHY-113,COM-113?shift=Day")->assertResponseStatus(200)->seeJsonStructure(['results']);
        $this->visit("/api/v1/units?where=true&names=".$units[0]->name. "," . $units[1]->name . "&shift=athi")
            ->assertResponseStatus(200)
            ->seeJsonStructure(['results'])
            ->seeJsonContains([
                'name' => $units[0]->name
            ]);
    }
}
