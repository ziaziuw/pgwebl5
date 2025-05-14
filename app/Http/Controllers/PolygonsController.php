<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel;


class PolygonsController extends Controller
{

    public function __construct()
    {
        $this->polygons = new PolygonsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map',
        ];
        return view('map', $data);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $request->validate(
            [
                'name' => 'required|unique:polygons,name',
                'description' => 'required',
                'geom_polygon' => 'required',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry is required',
                'image.image'          => 'File harus berupa gambar',
                'image.mimes'          => 'Format gambar hanya jpeg,png,jpg,gif,svg',
                'image.max'            => 'Ukuran gambar maksimal 10MB',
            ]
        );

        //Membuat tempat penyimpanan gambar
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //Mendapatkan file gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        // Simpan data
        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image'       => $name_image,
        ];

        // Simpan ke database
        if (!$this->polygons->create($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to add');
        }

        // Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Polygon has been added');
    }

    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id,
        ];

        return view('edit-polygon', $data);
    }

    public function create()
    {
        //return view('polygons.create');
    }

    public function destroy(string $id)
    {
        // Ambil nama file image dari model Polygon
        $imagefile = $this->polygons->find($id)->image;

        // Hapus record Polygon
        if (! $this->polygons->destroy($id)) {
            return redirect()->route('map')
                ->with('error', 'Failed to delete polygon');
        }

        // Kalau ada file-nya, cek dan hapus
        if ($imagefile !== null) {
            $filePath = storage_path('app/public/images/' . $imagefile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Redirect sukses
        return redirect()->route('map')
            ->with('success', 'Polygon deleted successfully');
    }
}
