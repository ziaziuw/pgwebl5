<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use App\Models\PolylinesModel;
use App\Models\PolygonsModel;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct() {
        $this->points = new PointsModel();
        $this->polylines = new PolylinesModel();
        $this->polygons = new PolygonsModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Table',
            'points' => $this->points->all(), //memanggil semua data dari tabel points lalu ditampilkan pd view table
        ];
        return view('table', $data);
    }
}
