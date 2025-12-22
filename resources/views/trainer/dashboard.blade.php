@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Trainer Dashboard')

@section('content')
    <div class="container-xxl container-p-y">
        @if(Auth::guard('trainer_web')->user()->verified === 'pending')
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="bx bx-time-five text-warning" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="fw-bold">Waiting for Account Verification</h3>
                            <p class="text-muted fs-5 mb-4">
                                Your account is currently under review by our administration team. <br>
                                Most accounts are verified within 24-48 hours. You will receive an email once your account is
                                approved.
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('trainer.logout') }}" class="btn btn-label-secondary">
                                    <i class="bx bx-log-out me-1"></i> Log Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(Auth::guard('trainer_web')->user()->verified === 'suspended')
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card text-center py-5 border border-danger">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="bx bx-error text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="fw-bold text-danger">Account Suspended</h3>
                            <p class="text-muted fs-5 mb-4">
                                Your account has been suspended by the administration. <br>
                                Access to platform features is restricted. Please contact support or the administrator for
                                further clarification.
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('trainer.logout') }}" class="btn btn-label-secondary">
                                    <i class="bx bx-log-out me-1"></i> Log Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <!-- Welcome Card -->
                <div class="col-lg-8 mb-4 order-0">
                    <div class="card">
                        <div class="d-flex align-items-end row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Welcome back,
                                        {{ Auth::guard('trainer_web')->user()->name }}! 🎉
                                    </h5>
                                    <p class="mb-4">
                                        You have done <span class="fw-bold">{{ $totalBookings }}</span> bookings so far. Check
                                        your earnings and active programs below.
                                    </p>
                                    <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                                </div>
                            </div>
                            <div class="col-sm-5 text-center text-sm-left">
                                <div class="card-body pb-0 px-0 px-md-4">
                                    <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                        alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                        data-app-light-img="illustrations/man-with-laptop-light.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metric Cards -->
                <div class="col-lg-4 col-md-4 order-1">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="avatar-initial rounded bg-label-success"><i
                                                    class="bx bx-dollar"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Total Earnings</span>
                                    <h3 class="card-title mb-2">${{ number_format($totalEarnings, 2) }}</h3>
                                    <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> +12%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="avatar-initial rounded bg-label-warning"><i
                                                    class="bx bx-book-open"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Active Programs</span>
                                    <h3 class="card-title mb-2">{{ $activePrograms }}</h3>
                                    <small class="text-secondary fw-semibold">Current</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Monthly Earnings Bar Chart -->
                <div class="col-md-6 col-lg-8 order-2 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Monthly Earnings ({{ date('Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div id="earningsChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Program Distribution Pie Chart -->
                <div class="col-md-6 col-lg-4 order-3 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Most Popular Programs</h5>
                        </div>
                        <div class="card-body">
                            <div id="programChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="row">
                <div class="col-12 order-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0">Recent Bookings</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Program</th>
                                        <th>Organization</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($recentBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->created_at->format('d M Y') }}</td>
                                            <td><span class="fw-semibold">{{ $booking->requirement->program->title }}</span></td>
                                            <td>{{ $booking->organization->name }}</td>
                                            <td>${{ number_format($booking->amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusClass = match ($booking->payment_status) {
                                                        'paid', 'completed' => 'bg-label-success',
                                                        'pending' => 'bg-label-warning',
                                                        'failed' => 'bg-label-danger',
                                                        default => 'bg-label-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} me-1">{{ $booking->payment_status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No recent bookings.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('ajax')
    data: earningsData
    }],
    chart: {
    height: 350,
    type: 'bar',
    toolbar: { show: false }
    },
    plotOptions: {
    bar: {
    borderRadius: 5,
    dataLabels: { position: 'top' }, // top, center, bottom
    columnWidth: '40%'
    }
    },
    dataLabels: {
    enabled: true,
    formatter: function (val) {
    return val > 0 ? "$" + val : "";
    },
    offsetY: -20,
    style: {
    fontSize: '12px',
    colors: ["#304758"]
    }
    },
    xaxis: {
    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    position: 'bottom',
    axisBorder: { show: false },
    axisTicks: { show: false },
    tooltip: { enabled: true }
    },
    yaxis: {
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: {
    show: true,
    formatter: function (val) {
    return "$" + val;
    }
    }
    },
    colors: ['#696cff'], // Primary color from Sneat
    grid: {
    borderColor: '#f1f1f1'
    }
    };

    const earningsChart = new ApexCharts(document.querySelector("#earningsChart"), earningsChartOptions);
    earningsChart.render();


    // --- Program Popularity Chart ---
    const programLabels = @json($programLabels);
    const programData = @json($programData);

    // If no data, show placeholder or empty chart behavior
    if (programData.length > 0) {
    const programChartOptions = {
    series: programData,
    chart: {
    width: '100%',
    type: 'donut',
    },
    labels: programLabels,
    plotOptions: {
    pie: {
    donut: {
    size: '65%',
    labels: {
    show: true,
    name: {
    fontSize: '1rem',
    fontFamily: 'Public Sans'
    },
    value: {
    fontSize: '1.2rem',
    color: '#566a7f',
    fontFamily: 'Public Sans',
    formatter: function (val) {
    return val;
    }
    }
    }
    }
    }
    },
    legend: {
    position: 'bottom'
    },
    colors: ['#696cff', '#71dd37', '#03c3ec', '#8592a3', '#ff3e1d'] // Sneat colors
    };

    const programChart = new ApexCharts(document.querySelector("#programChart"), programChartOptions);
    programChart.render();
    } else {
    document.querySelector("#programChart").innerHTML = "<p class='text-center mt-5 text-muted'>No booking data available.
    </p>";
    }

    });
    </script>
@endpush