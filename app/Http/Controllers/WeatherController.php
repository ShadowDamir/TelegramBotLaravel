<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public static function getWeatherInCity($city){
        try{
            $result = Http::get(config('bots.weather_api').'weather',[
                "q" => $city,
                "appid"=>config('bots.weather_token'),
                "units"=>"metric"
            ]);
            return $result;
        }
        catch (\Exception) {return null;}

    }
}
