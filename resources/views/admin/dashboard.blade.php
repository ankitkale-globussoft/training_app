@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-xxl container-p-y">
        <!-- Top Stats Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="avatar bg-label-primary rounded p-2">
                                <i class="bx bx-building fs-3"></i>
                            </div>
                            <div class="badge bg-label-info">+12%</div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Organizations</span>
                        <h4 class="card-title mb-1">{{ $orgCount }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="avatar bg-label-success rounded p-2">
                                <i class="bx bx-user-voice fs-3"></i>
                            </div>
                            <div class="badge bg-label-success">+8%</div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Trainers</span>
                        <h4 class="card-title mb-1">{{ $trainerCount }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="avatar bg-label-warning rounded p-2">
                                <i class="bx bx-group fs-3"></i>
                            </div>
                            <div class="badge bg-label-warning">+15%</div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Candidates</span>
                        <h4 class="card-title mb-1">{{ $candidateCount }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="avatar bg-label-danger rounded p-2">
                                <i class="bx bx-dollar fs-3"></i>
                            </div>
                            <div class="badge bg-label-danger">+10%</div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Revenue</span>
                        <h4 class="card-title mb-1">₹{{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth & Geographic Row -->
        <div class="row">
            <!-- Platform Growth Chart -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Platform Growth Trends ({{ date('Y') }})</h5>
                        <small class="text-muted">Organizations vs Trainers</small>
                    </div>
                    <div class="card-body">
                        <div id="growthChart"></div>
                    </div>
                </div>
            </div>

            <!-- Geographic Distribution -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribution by State</h5>
                    </div>
                    <div class="card-body">
                        <div id="geoChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement & Popularity Row -->
        <div class="row">
            <!-- Popular Programs -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top Programs</h5>
                    </div>
                    <div class="card-body">
                        <div id="popularityChart"></div>
                        <ul class="p-0 m-0 mt-4">
                            @foreach($popularPrograms as $program)
                                <li class="d-flex mb-3 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-book"></i></span>
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{ \Illuminate\Support\Str::limit($program->title, 25) }}</h6>
                                        </div>
                                        <div class="user-progress text-end">
                                            <small class="fw-semibold">{{ $program->total }} Bookings</small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="col-12 col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Bookings</h5>
                        <a href="javascript:;" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover border-top">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Organization</th>
                                    <th>Program</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->created_at->format('d M, Y') }}</td>
                                        <td>{{ $booking->organization->name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($booking->requirement->program->title, 20) }}</td>
                                        <td>₹{{ number_format($booking->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match ($booking->payment_status) {
                                                    'paid', 'completed' => 'success',
                                                    'pending' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-label-{{ $statusClass }}">{{ $booking->payment_status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="row">
            <!-- Recent Organizations -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">New Organizations</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="orecent" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orecent">
                                <a class="dropdown-item" href="javascript:void(0);">View Registered</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @foreach($recentOrgs as $org)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{ $org->org_image ? asset('storage/' . $org->org_image) : asset('assets/img/avatars/1.png') }}"
                                            alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">{{ $org->city }}, {{ $org->state }}</small>
                                            <h6 class="mb-0">{{ $org->name }}</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <span class="text-muted">{{ $org->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Trainers -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">New Trainers</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @foreach($recentTrainers as $trainer)
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{ $trainer->profile_pic ? asset('storage/' . $trainer->profile_pic) : asset('assets/img/avatars/5.png') }}"
                                            alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">{{ $trainer->city }},
                                                {{ $trainer->state }}</small>
                                            <h6 class="mb-0">{{ $trainer->name }}</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <span class="text-muted">{{ $trainer->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- Growth Chart ---
            const growthOptions = {
                series: [{
                    name: 'Organizations',
                    data: @json($orgGrowth)
                }, {
                    name: 'Trainers',
                    data: @json($trainerGrowth)
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: { show: false },
                    dropShadow: { enabled: true, top: 18, left: 7, blur: 10, opacity: 0.2 }
                },
                colors: ['#696cff', '#71dd37'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
                markers: { size: 1 },
                xaxis: {
                    categories: @json($months),
                    title: { text: 'Month' }
                },
                legend: { position: 'top', horizontalAlign: 'right', floating: true, offsetY: -25, offsetX: -5 }
            };
            new ApexCharts(document.querySelector("#growthChart"), growthOptions).render();

            // --- Geographic Chart ---
            const geoOptions = {
                series: [{
                    name: 'Registrations',
                    data: @json($orgGeoData->pluck('total'))
                }],
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '50%' } },
                colors: ['#03c3ec'],
                xaxis: {
                    categories: @json($orgGeoData->pluck('state'))
                }
            };
            new ApexCharts(document.querySelector("#geoChart"), geoOptions).render();

            // --- Popularity Chart ---
            const popularityOptions = {
                series: @json($popularPrograms->pluck('total')),
                chart: { type: 'donut', height: 250 },
                labels: @json($popularPrograms->pluck('title')),
                legend: { show: false },
                colors: ['#696cff', '#03c3ec', '#71dd37', '#ff3e1d', '#8592a3']
            };
            new ApexCharts(document.querySelector("#popularityChart"), popularityOptions).render();
        });
    </script>
@endpush