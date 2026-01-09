@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Test Management')

@section('content')
  <div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Tests</h4>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Tests List</h5>
          <a href="{{ route('admin.test.create') }}" class="btn btn-primary">Create New Test</a>
        </div>

        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <div class="table-responsive text-nowrap">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Program</th>
                  <th>Duration (mins)</th>
                  <th>Total Marks</th>
                  <th>Questions</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tests as $test)
                  <tr>
                    <td>{{ $test->test_id }}</td>
                    <td>{{ $test->title }}</td>
                    <td>{{ $test->program->program_name ?? 'N/A' }}</td>
                    <td>{{ $test->duration }}</td>
                    <td>{{ $test->total_marks }}</td>
                    <td>{{ $test->questions_count ?? $test->questions()->count() }}</td>
                    <td>
                      <a href="{{ route('admin.test.edit', $test->test_id) }}" class="btn btn-sm btn-info">Edit/Manage</a>
                      <form action="{{ route('admin.test.destroy', $test->test_id) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">No tests found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-center mt-3">
            {{ $tests->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection