<div class="modal fade" id="programTypeModal">
    <div class="modal-dialog">
        <form id="programTypeForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="programTypeId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Program Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="name" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="description" class="form-control"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" id="image" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
