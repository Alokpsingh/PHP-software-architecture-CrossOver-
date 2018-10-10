<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Panel;

class OneDayElectricityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $panel = Panel::where('serial', $request->panel_serial)->first();
        $oneHourElectricities = $panel -> oneHourElectricities -> groupBy(function($date) {
                return Carbon::parse($date->hour)->format('d-m-Y');
            })->map(function ($item, $key) {
                $kilowattsArray = array_column($item->toArray(), "kilowatts");
                return [
                    'day' => $key,
                    'sum' => array_sum($kilowattsArray),
                    'min' => min($kilowattsArray),
                    'max' => max($kilowattsArray),
                    'average' => array_sum($kilowattsArray) / count($kilowattsArray)
                ];
            });

        return array_values($oneHourElectricities->toArray());
    }
}
