<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head')
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            {{-- Sidebar --}}
            @include("layouts.$panel.sidebar")

            <div class="layout-page">
                
                {{-- Navbar --}}
                @include("layouts.$panel.navbar")

                <div class="content-wrapper">
                    {{-- Page Content --}}
                    @yield('content')
                </div>

            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @include('layouts.scripts')
</body>
</html>
