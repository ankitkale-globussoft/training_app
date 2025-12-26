@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Assigned Trainings')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Trainings /</span> Assigned Trainings
        </h4>

        @if ($bookings->count())
            <div class="row g-4 mb-4">
                @foreach ($bookings as $booking)
                    @php
                        $latestProgress = $booking->progress->first();
                        $status = $latestProgress ? $latestProgress->status : 'assigned';
                        $percentage = $latestProgress ? $latestProgress->percentage : 0;
                        $note = $latestProgress ? $latestProgress->note : '';
                        $mode = $booking->requirement->mode;
                    @endphp

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-truncate" title="{{ $booking->requirement->program->title }}">
                                    {{ $booking->requirement->program->title }}
                                </h5>
                                <span class="badge bg-label-success">Assigned</span>
                            </div>
                            <div class="card-body d-flex flex-column">

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="fw-bold">Progress: {{ $percentage }}%</small>
                                        <small class="text-muted">{{ ucfirst($status) }}</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-3 text-muted">
                                    <li class="mb-1">
                                        <i class="bx bx-buildings me-1"></i>
                                        <strong>Org:</strong> {{ $booking->organization->name }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bx bx-broadcast me-1"></i>
                                        <strong>Mode:</strong> {{ ucfirst($mode) }}
                                    </li>
                                    @if ($booking->requirement->schedule_date)
                                        <li class="mb-1">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($booking->requirement->schedule_date)->format('d M Y') }}
                                            at {{ $booking->requirement->schedule_time }}
                                        </li>
                                    @endif
                                </ul>

                                <div class="mt-auto">
                                    <button class="btn btn-primary w-100"
                                        onclick="openUpdateModal({{ $booking->booking_id }}, '{{ $status }}', {{ $percentage }}, '{{ $mode }}', '{{ $note }}')">
                                        <i class="bx bx-edit"></i> Update Status
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-task-x" style="font-size:4rem;color:#ddd;"></i>
                    <h5 class="mt-3">No Assigned Trainings</h5>
                    <p class="text-muted">You haven't been assigned any trainings yet.</p>
                </div>
            </div>
        @endif

    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Training Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="booking_id" name="booking_id">
                        <input type="hidden" id="training_mode" name="mode">

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="status_select" name="status" class="form-select">
                                <option value="assigned">Assigned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <!-- Offline only options will be toggled via JS -->
                                <option value="enroute" class="offline-only">Enroute</option>
                                <option value="arrived" class="offline-only">Arrived</option>
                                <option value="teaching_started" class="offline-only">Teaching Started</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Completion Percentage (0-100)</label>
                            <input type="number" id="percentage_input" name="percentage" class="form-control" min="0"
                                max="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea id="note_input" name="note" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('ajax')
    <script>
        const statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));

        function openUpdateModal(bookingId, currentStatus, currentPercentage, mode, currentNote) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('training_mode').value = mode;
            document.getElementById('percentage_input').value = currentPercentage;
            document.getElementById('note_input').value = currentNote;
            document.getElementById('status_select').value = currentStatus;

            // Handle Offline specific options
            const offlineOptions = document.querySelectorAll('.offline-only');
            offlineOptions.forEach(opt => {
                if (mode === 'offline') {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });

            statusModal.show();
        }

        function submitStatusUpdate() {
            const form = document.getElementById('updateStatusForm');
            const data = {
                booking_id: form.booking_id.value,
                status: form.status.value,
                percentage: form.percentage.value,
                note: form.note.value
            };

            fetch("{{ route('trainer.trainings.update_status') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status) {
                        statusModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                });
        }
    </script>
@endpush