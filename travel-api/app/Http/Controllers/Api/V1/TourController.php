<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Travel $travel, Request $request) {
        $query = $travel->tours()->getQuery();

        $request['price_from'] && $query->where('price', '>=', $request['price_from'] * 100);
        $request['price_to'] && $query->where('price', '<=', $request['price_to'] * 100);
        $request['date_from'] && $query->where('starting_date', '>=', $request['date_from']);
        $request['date_to'] && $query->where('ending_date', '<=', $request['date_to']);
        $request['sort_by_price'] && $query->orderBy('price', $request['sort_by_price']);

        $tours = $query
        ->orderBy('starting_date')
        ->paginate(10);

        return TourResource::collection($tours);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
