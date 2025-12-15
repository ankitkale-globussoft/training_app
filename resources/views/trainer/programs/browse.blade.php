@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'My Programs')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                <span class="text-muted fw-light">Programs /</span> Browse Programs
            </h4>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Program Type</label>
                        <select id="programTypeFilter" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($programTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <select id="durationFilter" class="form-select">
                            <option value="">All</option>
                            <option value="1 Day">1 Day</option>
                            <option value="3 Days">3 Days</option>
                            <option value="1 Week">1 Week</option>
                        </select>
                    </div> --}}

                    <span class="badge bg-label-success fs-6">
                        Selected: <span id="selectedCount">0</span>
                    </span>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body table-responsive">
                <div id="loadingSpinner" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary"></div>
                </div>
                <div id="tableWrapper">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody id="programTable"></tbody>
                    </table>

                </div>
                <ul class="pagination mt-3" id="pagination"></ul>
            </div>
        </div>
    </div>

    @include('trainer.programs.modal')
@endsection

@push('ajax')
    <script>
        let currentPage = 1;

        $(document).ready(function() {
            loadPrograms();

            $('#programTypeFilter, #durationFilter').on('change', function() {
                currentPage = 1;
                loadPrograms();
            });
        });

        function loadPrograms(page = currentPage) {
            currentPage = page;

            $('#loadingSpinner').removeClass('d-none');
            $('#tableWrapper').addClass('d-none');

            $.get("{{ route('trainer.programs.list') }}", {
                page: page,
                program_type_id: $('#programTypeFilter').val(),
                duration: $('#durationFilter').val()
            }, function(res) {

                $('#loadingSpinner').addClass('d-none');
                $('#tableWrapper').removeClass('d-none');

                renderPrograms(res.programs.data);
                $('#selectedCount').text(res.selected_count);
            });
        }

        function renderPrograms(programs) {
            let html = '';

            programs.forEach(p => {
                let actionBtn = p.is_selected ?
                    `<button class="btn btn-sm btn-outline-danger"
                        onclick="removeProgram(${p.program_id}, this)">
                    <i class="bx bx-x"></i> Remove
               </button>` :
                    `<button class="btn btn-sm btn-primary"
                        onclick="selectProgram(${p.program_id}, this)">
                    <i class="bx bx-check"></i> Select
               </button>`;

                html += `
            <tr>
                <td>
                    <div class="fw-semibold">${p.title}</div>
                    <small class="text-muted">${p.short_description ?? ''}</small>
                </td>
                <td>${p.program_type_id}</td>
                <td>${p.duration ?? '-'}</td>
                <td>
                    <button class="btn btn-sm btn-icon"
                            onclick='viewProgram(${JSON.stringify(p)})'>
                        <i class="bx bx-show"></i>
                    </button>
                    ${actionBtn}
                </td>
            </tr>
        `;
            });

            $('#programTable').html(html);
        }

        function selectProgram(id, btn) {
            $(btn).prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

            $.post("{{ route('trainer.programs.select') }}", {
                _token: '{{ csrf_token() }}',
                program_id: id
            }, () => loadPrograms());
        }

        function removeProgram(id, btn) {
            $(btn).prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

            $.ajax({
                url: "{{ route('trainer.programs.remove') }}",
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    program_id: id
                },
                success: () => loadPrograms()
            });
        }
    </script>
@endpush
