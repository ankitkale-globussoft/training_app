@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Requested Programs')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Programs /</span> Requested Programs
    </h4>

    @if ($requirements->count())
        <div class="row g-4 mb-4">
            @foreach ($requirements as $req)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @if ($req->program->image)
                            <img src="{{ asset('storage/' . $req->program->image) }}"
                                 class="card-img-top"
                                 style="height:200px; object-fit:cover;">
                        @else
                            <div class="card-img-top bg-label-primary d-flex align-items-center justify-content-center"
                                 style="height:200px;">
                                <i class="bx bx-book-open" style="font-size:4rem;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $req->program->title }}</h5>
                                <span class="badge bg-label-info">
                                    {{ ucfirst($req->mode) }}
                                </span>
                            </div>

                            <p class="text-muted small mb-3">
                                {{ Str::limit($req->program->description, 90) }}
                            </p>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-1">
                                    <i class="bx bx-calendar"></i>
                                    <strong>Requested On:</strong>
                                    {{ $req->created_at->format('d M Y') }}
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-time-five"></i>
                                    <strong>Duration:</strong>
                                    {{ $req->program->duration }} months
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-check-circle"></i>
                                    <strong>Status:</strong>
                                    <span class="badge bg-label-warning">
                                        {{ ucfirst($req->status ?? 'pending') }}
                                    </span>
                                </li>
                            </ul>

                            <div class="mt-auto">
                                <button class="btn btn-outline-danger w-100"
                                    onclick="cancelRequest({{ $req->requirement_id }})">
                                    <i class="bx bx-x-circle"></i> Cancel Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination (Sneat style) -->
        <div class="card">
            <div class="card-body">
                {{ $requirements->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-package" style="font-size:4rem;color:#ddd;"></i>
                <h5 class="mt-3">No Requested Programs</h5>
                <p class="text-muted">You have not requested any programs yet.</p>
            </div>
        </div>
    @endif

</div>
@endsection
@push('ajax')
<script>
function cancelRequest(id) {
    Swal.fire({
        title: 'Cancel Request?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3e1d',
        confirmButtonText: 'Yes, Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('org/programs/request') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('Cancelled!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush
