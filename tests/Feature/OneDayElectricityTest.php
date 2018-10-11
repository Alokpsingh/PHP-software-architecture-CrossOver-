<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

use App\Models\OneHourElectricity;
use App\Models\Panel;

class OneDayElectricityTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndexForPanelWithElectricity()
    {
        $panel = factory(Panel::class)->make();
        $panel->save();

        $nowDateTime = Carbon::now();

        $firstKilowattsHour  = rand(0, 1000);
        $secondKilowattsHour = rand(0, 1000);
        $kilowattsArray = array($firstKilowattsHour, $secondKilowattsHour);

        factory(OneHourElectricity::class)->make([ 'panel_id' => $panel->id, 'kilowatts' => $firstKilowattsHour, 'hour' => $nowDateTime->toDateTimeString()])->save();

        $nowDateTime->addHour();
        factory(OneHourElectricity::class)->make([ 'panel_id' => $panel->id, 'kilowatts' => $secondKilowattsHour, 'hour' => $nowDateTime->toDateTimeString()])->save();

        $response = $this->json('GET', '/api/one_day_electricities?panel_serial='.$panel->serial);

        $response->assertStatus(200);
        $responseArray = json_decode($response->getContent());
                 
        $this->assertCount(1, json_decode($response->getContent()));
        $this->assertEquals($nowDateTime->format('d-m-Y'), $responseArray[0]->day);
        $this->assertEquals(array_sum($kilowattsArray), $responseArray[0]->sum);
        $this->assertEquals(min($kilowattsArray), $responseArray[0]->min);
        $this->assertEquals(max($kilowattsArray), $responseArray[0]->max);
        $this->assertEquals(array_sum($kilowattsArray) / count($kilowattsArray), $responseArray[0]->average);
    }
}
