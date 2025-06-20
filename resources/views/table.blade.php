@extends('layout.template')

@section('content')
    <div class="container mt-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h4>Points Data</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="pointstable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($points as $p) <!-- memanggil berulang dari semua data yang mengandung point */ -->
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->description }}</td>
                            <td>
                                <img src="{{asset('storage/images/' . $p->image) }}" alt=""
                                width="200" title="{{ $p->image }}">
                            </td>
                            <td>{{ $p->created_at }}</td>
                            <td>{{ $p->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    <!-- duplikat card */ -->
        <div class="card mt-4">
            <div class="card-header">
                <h4>Polylines Data</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="polylinestable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($polylines as $p) <!-- memanggil berulang dari semua data yang mengandung polyline */ -->
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->description }}</td>
                            <td>
                                <img src="{{asset('storage/images/' . $p->image) }}" alt=""
                                width="200" title="{{ $p->image }}">
                            </td>
                            <td>{{ $p->created_at }}</td>
                            <td>{{ $p->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- polygon */ -->
        <div class="card mt-4">
            <div class="card-header">
                <h4>Polygons Data</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="polygonstable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($polygons as $p) <!-- memanggil berulang dari semua data yang mengandung polygons */ -->
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->description }}</td>
                            <td>
                                <img src="{{asset('storage/images/' . $p->image) }}" alt=""
                                width="200" title="{{ $p->image }}">
                            </td>
                            <td>{{ $p->created_at }}</td>
                            <td>{{ $p->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<!-- tambah section styles */ -->
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.min.css">
@endsection

<!-- tambah section scripts */ -->
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
<script>
    let tablepoints = new DataTable('#pointstable');
    let tablepolylines = new DataTable('#polylinestable');
    let tablepolygons = new DataTable('#polygonstable');
</script>
@endsection
