<div class="modal fade" id="pilgrim-delete-modal" tabindex="-1" aria-labelledby="pilgrim-delete-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pilgrim-delete-modal-label">Delete Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pilgrim-delete-loading" class="text-center py-4 text-muted d-none">
                    <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                    Loading delete preview...
                </div>
                <div id="pilgrim-delete-error" class="alert alert-danger d-none mb-0"></div>
                <div id="pilgrim-delete-content" class="d-none">
                    <p class="mb-3">You are about to delete the following registration. This action cannot be undone.</p>

                    <div class="pilgrim-delete-section">
                        <h6 class="pilgrim-delete-section__title">Registration Details</h6>
                        <dl class="pilgrim-delete-details mb-0" id="pilgrim-delete-details"></dl>
                    </div>

                    <div class="pilgrim-delete-section" id="pilgrim-delete-family-section">
                        <h6 class="pilgrim-delete-section__title">Family Connection</h6>
                        <p class="pilgrim-delete-summary mb-2" id="pilgrim-delete-family-summary"></p>
                        <ul class="pilgrim-delete-list mb-0" id="pilgrim-delete-family-members"></ul>
                    </div>

                    <div class="pilgrim-delete-section d-none" id="pilgrim-delete-rebalance-section">
                        <h6 class="pilgrim-delete-section__title">Family Rebalance</h6>
                        <div class="table-responsive">
                            <table class="table table-sm pilgrim-delete-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Current Code</th>
                                        <th>After Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="pilgrim-delete-rebalance-rows"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pilgrim-delete-section" id="pilgrim-delete-flights-section">
                        <h6 class="pilgrim-delete-section__title">Flight Assignments</h6>
                        <p class="pilgrim-delete-summary mb-2" id="pilgrim-delete-flights-summary"></p>
                        <ul class="pilgrim-delete-list mb-0" id="pilgrim-delete-flights-list"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm d-none" id="pilgrim-delete-confirm">Delete Registration</button>
            </div>
        </div>
    </div>
</div>
