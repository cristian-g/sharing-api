<?php

namespace App\Http\Controllers;

use App\Outgo;
use Illuminate\Http\Request;
use App\Vehicle;

class OutgoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $firstVehicle = Vehicle::first();
        $outgoes = Outgo::orderBy('created_at', 'asc')->get();
        return response()->json(['outgoes'=> $outgoes->toArray()], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $firstVehicle = Vehicle::first();

        $outgo = new Outgo([
            'quantity' => $request->quantity,
            'description' => $request->description,
        ]);
        $outgo->vehicle()->associate($firstVehicle);
        $outgo->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
