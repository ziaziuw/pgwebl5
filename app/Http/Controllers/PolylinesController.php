<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PolylinesModel;

class PolylinesController extends Controller
{
    protected $polylines;
    protected $imageFolder;

    public function __construct()
    {
        $this->polylines = new PolylinesModel();
        // Tentukan folder untuk menyimpan gambar
        $this->imageFolder = storage_path('app/public/images');
    }

    /**
     * Return all polylines as GeoJSON with length.
     */
    public function index()
    {
        $polylines = DB::table('polylines')
            ->select(
                'id',
                DB::raw('ST_AsGeoJSON(geom) AS geom'),
                'name',
                'description',
                'image',
                DB::raw('ST_Length(geom, true) / 1000 AS length_km'),
                'created_at',
                'updated_at'
            )
            ->get();

        return response()->json($polylines);
    }

    /**
     * Show form to create a new polyline.
     */
    public function create()
    {
        return view('polylines.create');
    }

    /**
     * Store a newly created polyline.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|unique:polylines,name',
            'description'   => 'required',
            'geom_polyline' => 'required',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ],[
            'name.required'          => 'Name is required',
            'name.unique'            => 'Name already exists',
            'description.required'   => 'Description is required',
            'geom_polyline.required' => 'Geometry is required',
            'image.image'            => 'File harus berupa gambar',
            'image.mimes'            => 'Format gambar hanya jpeg,png,jpg,gif,svg',
            'image.max'              => 'Ukuran gambar maksimal 10MB',
        ]);

        // Buat folder jika belum ada
        if (!file_exists($this->imageFolder)) {
            mkdir($this->imageFolder, 0777, true);
        }

        // Handle image upload
        $fileName = null;
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = time() . '_polyline.' . $file->getClientOriginalExtension();
            $file->move($this->imageFolder, $fileName);
        }

        // Prepare data
        $data = [
            'geom'        => DB::raw("ST_GeomFromText('{$request->geom_polyline}',4326)"),
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $fileName,
            'created_at'  => now(),
            'updated_at'  => now(),
            'user_id'     => auth()->user()->id, //auth user memanggil/mendapatkan id dari user yg login (ini di dlm store)
        ];

        if (!DB::table('polylines')->insert($data)) {
            return redirect()->route('map')->with('error', 'Failed to add polyline');
        }

        return redirect()->route('map')->with('success', 'Polyline has been added');
    }

    /**
     * Display a single polyline.
     */
    public function show($id)
    {
        $polyline = $this->polylines->findOrFail($id);
        return view('polylines.show', compact('polyline'));
    }

    /**
     * Show form to edit polyline.
     */
    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polyline',
            'id'    => $id,
        ];

        return view('edit-polyline', $data);
    }

    /**
     * Update polyline with optional image replacement.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|unique:polylines,name,' . $id,
            'description'   => 'required',
            'geom_polyline' => 'required',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ],[
            'name.required'        => 'Name is required',
            'name.unique'          => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_polyline.required'=> 'Geometry polyline is required',
            'image.image'          => 'File harus berupa gambar',
            'image.mimes'          => 'Format gambar hanya jpeg,png,jpg,gif,svg',
            'image.max'            => 'Ukuran gambar maksimal 10MB',
        ]);

        // Buat folder jika belum ada
        if (!file_exists($this->imageFolder)) {
            mkdir($this->imageFolder, 0777, true);
        }

        // Ambil file lama
        $old_image = $this->polylines->find($id)->image;

        // Upload gambar baru dan hapus lama jika ada
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = time() . '_polyline.' . $file->getClientOriginalExtension();
            $file->move($this->imageFolder, $fileName);
            if ($old_image && file_exists($this->imageFolder . '/' . $old_image)) {
                unlink($this->imageFolder . '/' . $old_image);
            }
        } else {
            $fileName = $old_image;
        }

        // Data update
        $data = [
            'geom'        => $request->geom_polyline,
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $fileName,
        ];

        if (! $this->polylines->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to update');
        }

        return redirect()->route('map')->with('success', 'Polyline has been updated');
    }

    /**
     * Delete a polyline and its image file.
     */
    public function destroy($id)
    {
        $polyline = $this->polylines->findOrFail($id);

        // Hapus record
        if (! $polyline->delete()) {
            return redirect()->route('map')->with('error', 'Failed to delete polyline');
        }

        // Hapus file jika ada
        if ($polyline->image && file_exists($this->imageFolder . '/' . $polyline->image)) {
            unlink($this->imageFolder . '/' . $polyline->image);
        }

        return redirect()->route('map')->with('success', 'Polyline deleted successfully');
    }
}
