@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Upcoming Trainings')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Trainings /</span> Upcoming
    </h4>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-type="assigned">
                Assigned
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-type="payment_pending">
                Payment Pending
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-type="cancelled">
                Cancelled
            </button>
        </li>
    </ul>

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <!-- Loader -->
            <div id="loadingSpinner" class="text-center py-5 d-none">
                <div class="spinner-border text-primary"></div>
            </div>

            <!-- Table -->
            <div class="table-responsive" id="tableWrapper">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Organisation</th>
                            <th>Mode</th>
                            <th>Schedule</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="trainingTableBody"></tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between mt-3">
                <span class="text-muted" id="showingText"></span>
                <ul class="pagination mb-0" id="pagination"></ul>
            </div>

        </div>
    </div>
</div>
@endsection

@push('ajax')
<script>
let currentType = 'assigned';
let currentPage = 1;

$(document).ready(function () {
    loadTrainings();

    $('.nav-link').on('click', function () {
        $('.nav-link').removeClass('active');
        $(this).addClass('active');

        currentType = $(this).data('type');
        currentPage = 1;
        loadTrainings();
    });
});

function loadTrainings(page = currentPage) {
    currentPage = page;

    $('#loadingSpinner').removeClass('d-none');
    $('#tableWrapper').addClass('d-none');

    $.get("{{ route('trainer.trainings.upcomming.list') }}", {
        type: currentType,
        page: page
    }, function (res) {

        $('#loadingSpinner').addClass('d-none');
        $('#tableWrapper').removeClass('d-none');

        renderTable(res.data);
        renderPagination(res);
        updateShowing(res);
    });
}

function renderTable(data) {
    let html = '';

    if (data.length === 0) {
        html = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No trainings found
                </td>
            </tr>`;
    } else {
        data.forEach(t => {
            html += `
                <tr>
                    <td class="fw-semibold">${t.program?.title ?? '-'}</td>
                    <td>${t.organisation?.name ?? '-'}</td>
                    <td>
                        <span class="badge bg-label-info">${t.mode}</span>
                    </td>
                    <td>
                        <small>
                            ${t.schedule_start} <br>
                            ${t.schedule_end}
                        </small>
                    </td>
                    <td>${t.location ?? '-'}</td>
                    <td>${statusBadge(t.status)}</td>
                </tr>
            `;
        });
    }

    $('#trainingTableBody').html(html);
}

function statusBadge(status) {
    let map = {
        assigned: 'primary',
        pending_payment: 'warning',
        cancelled: 'danger'
    };

    return `<span class="badge bg-label-${map[status]}">${status.replace('_',' ')}</span>`;
}

function renderPagination(res) {
    let html = '';

    if (res.current_page > 1) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadTrainings(${res.current_page - 1})">&laquo;</a>
        </li>`;
    }

    for (let i = 1; i <= res.last_page; i++) {
        html += `
            <li class="page-item ${i === res.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadTrainings(${i})">${i}</a>
            </li>`;
    }

    if (res.current_page < res.last_page) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadTrainings(${res.current_page + 1})">&raquo;</a>
        </li>`;
    }

    $('#pagination').html(html);
}

function updateShowing(res) {
    const from = res.from ?? 0;
    const to = res.to ?? 0;
    $('#showingText').text(`Showing ${from} to ${to} of ${res.total}`);
}
</script>
@endpush
