<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PolylinesModel;
use Illuminate\Http\Request;

class PolylinesController extends Controller
{
    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    public function index()
    {
        $polylines = DB::table('polylines')
            ->selectRaw('id, ST_AsGeoJSON(geom) as geom, name, description, image, created_at, updated_at')
            ->get();

        return response()->json($polylines);
    }


    public function getPolylines()
    {
        $polylines = $this->polylines->all();
        return response()->json($polylines);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('polylines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $request->validate(
            [
                'name' => 'required|unique:polylines,name',
                'description' => 'required',
                'geom_polyline' => 'required',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exists',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry is required',
            ]
        );

        // Simpan data dengan ST_GeomFromText biar geom masuk format geometry di DB
        $data = [
            'geom' => DB::raw("ST_GeomFromText('" . $request->geom_polyline . "', 4326)"),
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image ?? null,
        ];

        // Gagal
        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to add');
        }

        // Berhasil
        return redirect()->route('map')->with('success', 'Polyline has been added');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $polyline = $this->polylines->findOrFail($id);
        return view('polylines.show', compact('polyline'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $polyline = $this->polylines->findOrFail($id);
        return view('polylines.edit', compact('polyline'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:polylines,name,' . $id,
            'description' => 'required',
            'geom_polyline' => 'required',
        ]);

        $data = [
            'geom' => DB::raw("ST_GeomFromText('" . $request->geom_polyline . "', 4326)"),
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image ?? null,
        ];

        if (!$this->polylines->where('id', $id)->update($data)) {
            return redirect()->route('map')->with('error', 'Failed to update polyline');
        }

        return redirect()->route('map')->with('success', 'Polyline updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!$this->polylines->destroy($id)) {
            return redirect()->route('map')->with('error', 'Failed to delete polyline');
        }

        return redirect()->route('map')->with('success', 'Polyline deleted successfully');
    }
}
