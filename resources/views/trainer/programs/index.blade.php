@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Selected Programs')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Programs /</span> Selected Programs
    </h4>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($programs as $program)
                    <tr>
                        <td class="fw-semibold">{{ $program->title }}</td>
                        <td>{{ $program->duration }}</td>
                        <td>
                            <form method="POST" action="{{ route('trainer.programs.remove') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="program_id" value="{{ $program->program_id }}">
                                <button class="btn btn-sm btn-outline-danger">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $programs->links() }}
        </div>
    </div>
</div>
@endsection
