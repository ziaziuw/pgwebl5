<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    protected $points;
    protected $imageFolder;

    public function __construct()
    {
        $this->points = new PointsModel();
        // Tentukan folder gambar di public/storage/images
        $this->imageFolder = public_path('storage/images');
    }

    /**
     * Display a listing of the resource.
     */
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name'        => 'required|unique:points,name',
            'description' => 'required',
            'geom_point'  => 'required',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ],[
            'name.required'        => 'Name is required',
            'name.unique'          => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_point.required'  => 'Geometry point is required',
            'image.image'          => 'File harus berupa gambar',
            'image.mimes'          => 'Format gambar hanya jpeg,png,jpg,gif,svg',
            'image.max'            => 'Ukuran gambar maksimal 10MB',
        ]);

        // Buat folder jika belum ada
        if (!is_dir($this->imageFolder)) {
            mkdir($this->imageFolder, 0777, true);
        }

        // Proses upload gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name_image = time() . '_point.' . $file->getClientOriginalExtension();
            $file->move($this->imageFolder, $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'geom'        => $request->geom_point,
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $name_image,
        ];

        // Membuat data
        if (! $this->points->create($data)) {
            return redirect()->route('map')->with('error', 'Point failed to add');
        }

        return redirect()->route('map')->with('success', 'Point has been added');
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
        $data = [
            'title' => 'Edit Point',
            'id'    => $id,
        ];

        return view('edit-point', $data);
    }

    /**
     * Update request sesuai id.
     */
    public function update(Request $request, string $id)
    {
        // Validate request
        $request->validate([
            'name'        => 'required|unique:points,name,' . $id,
            'description' => 'required',
            'geom_point'  => 'required',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ],[
            'name.required'        => 'Name is required',
            'name.unique'          => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_point.required'  => 'Geometry point is required',
            'image.image'          => 'File harus berupa gambar',
            'image.mimes'          => 'Format gambar hanya jpeg,png,jpg,gif,svg',
            'image.max'            => 'Ukuran gambar maksimal 10MB',
        ]);

        // Buat folder jika belum ada
        if (!is_dir($this->imageFolder)) {
            mkdir($this->imageFolder, 0777, true);
        }

        // Ambil nama file lama
        $old_image = $this->points->find($id)->image;

        // Proses upload gambar baru dan hapus yang lama
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name_image = time() . '_point.' . $file->getClientOriginalExtension();
            $file->move($this->imageFolder, $name_image);

            if ($old_image && file_exists($this->imageFolder . '/' . $old_image)) {
                unlink($this->imageFolder . '/' . $old_image);
            }
        } else {
            $name_image = $old_image;
        }

        $data = [
            'geom'        => $request->geom_point,
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $name_image,
        ];

        // Update data
        if (! $this->points->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Point failed to update');
        }

        return redirect()->route('map')->with('success', 'Point has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagefile = $this->points->find($id)->image;

        if (! $this->points->destroy($id)) {
            return redirect()->route('map')->with('error', 'Point failed to delete');
        }

        if ($imagefile && file_exists($this->imageFolder . '/' . $imagefile)) {
            unlink($this->imageFolder . '/' . $imagefile);
        }

        return redirect()->route('map')->with('success', 'Point has been deleted');
    }
}
