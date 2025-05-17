<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Neighborhood;
use App\Models\State;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getLocationByPostalCode($postalCode)
    {
        if ($postalCode) {
            $neighborhoods = Neighborhood::where('postal_code', $postalCode)->get();
            $citiesId = $neighborhoods->pluck('city_id')->unique()->toArray();
            $cities = City::whereIn('id', $citiesId)->get();
            $stateIds = City::whereIn('id', $citiesId)->get()->pluck('state_id')->unique()->toArray();
            $stateId = State::whereIn('id', $stateIds)->first()->id;

            return response([
                'neighborhoods' => $neighborhoods,
                'cities' => $cities,
                'state_id' => $stateId,
            ], 200);
        }
    }

    public function getLocationByState($stateId)
    {
        $cities = City::where('state_id', $stateId)->orderBy('name', 'asc')->get();

        return response([
            'cities' => $cities,
        ], 200);
    }

    public function getLocationByCity($cityId)
    {
        $neighborhoods = Neighborhood::where('city_id', $cityId)->orderBy('name', 'asc')->get();

        return response([
            'neighborhoods' => $neighborhoods,
        ], 200);
    }

    public function getLocationByNeighborhood($neighborhoodId)
    {
        $postalCode = Neighborhood::where('id', $neighborhoodId)->value('postal_code');

        return response([
            'postalCode' => $postalCode,
        ], 200);
    }
}
