<div class="modal fade" id="leave-type-add-modal" tabindex="-1" role="dialog" aria-labelledby="entries-add-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add   Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" @submit.prevent="submitForm()"  id="leave_type_add" method="post">
                    <fieldset>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label" for="approval">Title</label>
                                <input class="form-control" id="approval" v-model="form_data.title" placeholder="Enter  Type Title " type="text" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="approval">Select  Approval</label>
                                <select class="form-control"  v-model="form_data.approval_id" required>
                                    <option disabled selected :value="undefined">Select Approval </option>
                                    <option v-for="approval in approvals " v-if="approval.active === 1" :value="approval.id">@{{ approval.title }}</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit"  name="action" id="submit-leave-type" form="leave_type_add" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
