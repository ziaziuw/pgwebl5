<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-earth-asia"></i> {{ $title }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa-solid fa-house-chimney"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('map') }}"><i class="fa-solid fa-map-location-dot"></i> Peta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('table') }}"><i class="fa-solid fa-table"></i> Tabel</a>
                </li>
                @auth
                    {{-- @auth memunculkan dropdown data hanya saat pengguna sudah login --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-database"></i> Data
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('api.points') }}" target="_blank"><i
                                        class="fa-solid fa-location-dot"></i> Points</a></li>
                            <li><a class="dropdown-item" href="{{ route('api.polylines') }}" target="_blank"><i
                                        class="fa-solid fa-grip-lines"></i> Polylines</a></li>
                            <li><a class="dropdown-item" href="{{ route('api.polygons') }}" target="_blank"><i
                                        class="fa-solid fa-draw-polygon"></i> Polygons</a></li>
                        </ul>
                    </li>

                    {{-- @auth memunculkan tombol logout hanya saat pengguna sudah login --}}
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="nav-link text-danger" type="submit">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout</button>
                        </form>
                    </li>
                @endauth

                {{-- jika blm login maka muncul tombol login --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-primary" href="{{ route('login') }}">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Login</a>
                        </form>
                    </li>
                @endguest
            </ul>
            </li>
            </ul>
        </div>
    </div>
</nav>
