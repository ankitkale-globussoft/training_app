@extends('layouts.auth')
@section('title', 'Signup')

@section('content')
<!-- Sneat background shapes -->
  <div class="authentication-wrapper authentication-basic container-p-y" 
       style="position: relative; min-height: 100vh; background: #f5f5f9;">
    <span style="position:absolute; top:20px; right:30px; width:90px; height:90px; border-radius:50%; background:#e0e7ff;"></span>
    <span style="position:absolute; bottom:40px; left:20px; width:120px; height:120px; border-radius:50%; background:#ffecec;"></span>

    <div class="authentication-inner d-flex justify-content-center">

      <div class="card" style="max-width: 900px; width:100%; box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <div class="card-body">

          <h4 class="mb-2 text-center">Trainer Registration</h4>
          <p class="mb-4 text-center">Fill in your details to register as a trainer</p>

          <form id="trainerRegisterForm" enctype="multipart/form-data">

            <!-- Row 1 -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
            </div>

            <!-- Row 2 -->
            <div class="row">
              <div class="col-md-6 mb-3 form-password-toggle">
                <label class="form-label">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" name="password" class="form-control" required />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
              </div>
            </div>

            <!-- Row 3 -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Address Line 1</label>
                <input type="text" name="addr_line1" class="form-control">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Address Line 2</label>
                <input type="text" name="addr_line2" class="form-control">
              </div>
            </div>

            <!-- Row 4 -->
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">District</label>
                <input type="text" name="district" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control">
              </div>
            </div>

            <!-- Row 5 -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Resume Link</label>
                <input type="url" name="resume_link" class="form-control">
              </div>
            </div>

            <!-- Profile Picture with preview -->
            <div class="mb-3">
              <label class="form-label">Profile Picture</label>
              <input type="file" class="form-control" id="profilePic" accept="image/*">

              <!-- Preview -->
              <div id="profilePreviewContainer" class="mt-2" style="display:none; position:relative; width:120px;">
                <img id="profilePreview" style="width:120px; height:120px; object-fit:cover; border-radius:8px;">
                <button type="button" 
                        onclick="removeProfilePreview()" 
                        style="position:absolute; top:-8px; right:-8px; background:#ff4d4f; color:white; border:none; border-radius:50%; width:24px; height:24px; font-size:12px;">
                  X
                </button>
              </div>
            </div>

            <!-- Biodata -->
            <div class="mb-3">
              <label class="form-label">Biodata</label>
              <textarea class="form-control" name="biodata" rows="3"></textarea>
            </div>

            <!-- Achievements -->
            <div class="mb-3">
              <label class="form-label">Achievements</label>
              <textarea class="form-control" name="achievements" rows="3"></textarea>
            </div>

            <!-- Row: Org Type + Availability + Mode -->
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Organisation Type</label>
                <select class="form-select" name="for_org_type">
                  <option value="">Select</option>
                  <option value="corporate">Corporate</option>
                  <option value="institution">Institution</option>
                  <option value="freelance">Freelance</option>
                </select>
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Availability</label>
                <input type="text" name="availability" class="form-control" placeholder="Weekdays / Weekends">
              </div>

              <div class="col-md-4 mb-3">
                <label class="form-label">Training Mode</label>
                <select class="form-select" name="training_mode">
                  <option value="">Select</option>
                  <option value="online">Online</option>
                  <option value="offline">Offline</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>
            </div>

            <!-- Signed Form -->
            <div class="mb-3">
              <label class="form-label">Signed Form (PDF)</label>
              <input type="file" class="form-control" name="signed_form_pdf" accept="application/pdf">
            </div>

            <!-- Verified -->
            <div class="mb-3 form-check">
              <input type="checkbox" name="verified" class="form-check-input">
              <label class="form-check-label">Verified</label>
            </div>

            <!-- Submit -->
            <button class="btn btn-primary d-grid w-100">Register</button>

          </form>

        </div>
      </div>

    </div>
  </div>


  <!-- Inline JS for image preview -->
  <script>
    const profileInput = document.getElementById("profilePic");
    const previewContainer = document.getElementById("profilePreviewContainer");
    const previewImage = document.getElementById("profilePreview");

    profileInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        previewImage.src = URL.createObjectURL(file);
        previewContainer.style.display = "block";
      }
    });

    function removeProfilePreview() {
      profileInput.value = "";
      previewImage.src = "";
      previewContainer.style.display = "none";
    }
  </script>
@endsection