<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public static function getWeatherInCity($city){
        try{
            $result = Http::get(env('WEATHER_API_URL','').'weather',[
                "q" => $city,
                "appid"=>env('WEATHER_API_TOKEN',''),
                "units"=>"metric"
            ]);
            logger($result);
            return $result;
        }
        catch (\Exception) {return null;}

    }
}
