@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Open Training Requests')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Trainings /</span> Open Training Requests
    </h4>

    @if ($requirements->count())
        <div class="row g-4 mb-4">
            @foreach ($requirements as $req)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">
                                    {{ $req->program->title }}
                                </h5>
                                <span class="badge bg-label-primary">
                                    {{ ucfirst($req->mode) }}
                                </span>
                            </div>

                            <p class="text-muted small mb-2">
                                {{ Str::limit($req->program->description, 90) }}
                            </p>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-1">
                                    <i class="bx bx-buildings"></i>
                                    <strong>Organisation:</strong>
                                    {{ $req->organisation->name }}
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-calendar"></i>
                                    <strong>Requested On:</strong>
                                    {{ $req->created_at->format('d M Y') }}
                                </li>
                                @if ($req->location)
                                <li class="mb-1">
                                    <i class="bx bx-map"></i>
                                    <strong>Location:</strong>
                                    {{ $req->location }}
                                </li>
                                @endif
                                @if ($req->schedule_start)
                                <li class="mb-1">
                                    <i class="bx bx-time"></i>
                                    <strong>Schedule:</strong>
                                    {{ $req->schedule_start }} – {{ $req->schedule_end }}
                                </li>
                                @endif
                            </ul>

                            <div class="mt-auto d-flex gap-2">
                                <button class="btn btn-outline-primary w-100"
                                    onclick="viewDetails({{ $req->requirement_id }})">
                                    <i class="bx bx-show"></i> View
                                </button>

                                <button class="btn btn-success w-100"
                                    onclick="acceptTraining({{ $req->requirement_id }})">
                                    <i class="bx bx-check-circle"></i> Accept
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                {{ $requirements->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-calendar-x" style="font-size:4rem;color:#ddd;"></i>
                <h5 class="mt-3">No Open Training Requests</h5>
                <p class="text-muted">Please check back later.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('ajax')
<script>
function acceptTraining(requirementId) {
    Swal.fire({
        title: 'Accept Training?',
        text: 'You will be assigned to this training.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Accept',
        confirmButtonColor: '#28c76f'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('trainer.trainings.accept') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    requirement_id: requirementId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('Accepted!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Something went wrong!', 'error');
            });
        }
    });
}

function viewDetails(id) {
    Swal.fire({
        icon: 'info',
        title: 'Details',
        text: 'You can extend this to a modal or page.'
    });
}
</script>
@endpush