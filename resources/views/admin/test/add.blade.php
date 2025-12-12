@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Add Test')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Tests /</span> Add Question
  </h4>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Add New Question</h5>
    </div>

    <div class="card-body">
      <form action="#" method="POST">
        <!-- Select Test -->
        <div class="mb-3">
          <label class="form-label" for="testSelect">Select Test</label>
          <select id="testSelect" name="test_id" class="form-select" required>
            <option value="" disabled selected>Choose a test...</option>
            <option value="1">Test 1</option>
            <option value="2">Test 2</option>
            <option value="3">Test 3</option>
          </select>
        </div>

        <!-- Question Text -->
        <div class="mb-3">
          <label class="form-label" for="questionText">Question Text</label>
          <textarea
            id="questionText"
            name="ques_text"
            class="form-control"
            rows="3"
            placeholder="Enter question here..."
            required
          ></textarea>
        </div>

        <!-- Options -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="optA">Option A</label>
            <input type="text" id="optA" name="opt_a" class="form-control" required />
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label" for="optB">Option B</label>
            <input type="text" id="optB" name="opt_b" class="form-control" required />
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="optC">Option C</label>
            <input type="text" id="optC" name="opt_c" class="form-control" required />
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label" for="optD">Option D</label>
            <input type="text" id="optD" name="opt_d" class="form-control" required />
          </div>
        </div>

        <!-- Correct Answer -->
        <div class="mb-3">
          <label class="form-label" for="ansOpt">Correct Answer</label>
          <select id="ansOpt" name="ans_opt" class="form-select" required>
            <option value="" disabled selected>Choose correct option...</option>
            <option value="A">Option A</option>
            <option value="B">Option B</option>
            <option value="C">Option C</option>
            <option value="D">Option D</option>
          </select>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save"></i> Save Question
        </button>
      </form>
    </div>
  </div>
</div>

@endsection