@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Programs')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Programs List</h5>
            </div>

            <div class="card-body p-2"> <!-- Adjusted padding here -->

                <form method="GET" action="{{ route('program.index') }}" class="row g-2">
                    <!-- Reduced gutter between columns -->

                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search program...">
                    </div>

                    <!-- Program Type Filter -->
                    <div class="col-md-4">
                        <label class="form-label">Program Type</label>
                        <select name="program_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($programTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('program_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-4 d-flex align-items-end ">
                        <button class="btn btn-primary me-2" type="submit">
                            <i class="ti ti-search"></i> Filter
                        </button>
                        <a href="{{ route('program.index') }}" class="btn btn-secondary">
                            <i class="ti ti-refresh"></i> Reset
                        </a>
                    </div>

                </form>

            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Cost</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($programs as $index => $prog)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $prog->title }}</td>
                                <td>{{ $prog->programType->type_name ?? 'N/A' }}</td>
                                <td>{{ $prog->duration }}</td>
                                <td>₹{{ number_format($prog->cost, 2) }}</td>
                                <td>{{ $prog->created_at->format('d M Y') }}</td>

                                <td class="text-center">

                                    <!-- View -->
                                    <a href="{{ route('program.show', $prog->program_id) }}"
                                        class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                        data-bs-placement="top" data-bs-html="true" title
                                        data-bs-original-title="<i class='fa-regular fa-eye' ></i> <span>View Program</span>">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.program.edit', $prog->program_id) }}" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                        data-bs-html="true" title
                                        data-bs-original-title="<i class='fa-regular fa-pen-to-square' ></i> <span>Edit Program</span>">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.program.destroy', $prog->program_id) }}" method="POST"
                                        class="d-inline" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                        data-bs-placement="top" data-bs-html="true" title
                                        data-bs-original-title="<i class='fa-solid fa-trash' ></i> <span>Delete Program</span>"
                                        onsubmit="return confirm('Are you sure to delete this?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    No programs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
