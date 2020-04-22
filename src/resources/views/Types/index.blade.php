@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <a href="#" class="btn btn-primary col-md-2 ml-auto mb-2" data-toggle="modal" data-target="#leave-type-add-modal"  >
                    Add  Type
                </a>
                <div class="col-md-12 align-items-end" >

                    <a href="{{route('leave-main')}}">
                        <span><i class="fe fe-arrow-left"></i></span>
                        Leave Home
                    </a>
                </div>
                <div class="col-sm-12" id="types">
                    @if(!empty($types))
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('leave-type-search') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="types-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="title">Title</th>
                                    <th data-field="approval">Approval</th>
                                    <th data-field="created_at">Added On</th>
                                    <th data-field="buttons">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @else
                        <div class="col s12" >
                            @component('layouts.blocks.tabler.empty-fullpage')
                                @slot('title')
                                    No Leave Type  Generated
                                @endslot
                                <a href="#" class="btn btn-primary" v-on:click.prevent="showLeaveType">Add Leave Type </a>
                                &nbsp;
                                @slot('buttons')
                                @endslot
                            @endcomponent
                        </div>
                    @endif
                    @include('modules-people-leaves::modals.add-leave-type')
                    @include('modules-people-leaves::modals.edit-leave-type')

                </div>



            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        app.currentUser = {!! json_encode($dorcasUser) !!};
        const table = $('.bootstrap-table');
        let LeaveType =  new Vue({
            el: '#types',
            data:{
                types: {!! $types !!},
                approvals: {!! $approvals !!},
                form_data:{
                    title:null,
                    leave_type_id: null,
                }
            },
            methods:{
                submitForm: function () {
                    $('#submit-leave-type').addClass('btn-loading btn-icon')
                    // console.log(this.form_data)
                    axios.post('/mpe/leave-types',this.form_data)
                        .then(response=>{
                            $('#submit-leave-type').removeClass('btn-loading btn-icon')
                            this.form_data = {};
                            $('#leave-type-add-modal').modal('hide')
                            swal({
                                title:"Success!",
                                text:" Leave Type Successfully Created",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            console.log(e.response.data);
                            $('#submit-leave-type').removeClass('btn-loading btn-icon')

                            swal.fire({
                                title:"Error!",
                                text:e.response.data.message,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        })
                },
                showLeaveType: function(){
                    $('#leave-type-add-modal').modal('show')
                },
                clickAction: function (event) {
                    let target = event.target;
                    if (!target.hasAttribute('data-action')) {
                        target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                    }

                    let action = target.getAttribute('data-action');
                    let name = target.getAttribute('data-name');
                    let id = target.getAttribute('data-id');
                    let index = parseInt(target.getAttribute('data-index'), 10);
                    switch (action) {
                        case 'view':
                            return true;
                        case 'delete_leave_type':
                            this.deleteLeaveType(id,index,name);
                            break;
                        case 'edit_leave_type':
                            this.editLeaveType(id,index,name);
                            break;
                    }

                },
                async editLeaveType(id)
                {
                    const self = this;
                    await axios.get("/mpe/leave-types/" + id)
                        .then(function (response) {
                            self.form_data = {
                                title:response.data[0].title,
                                leave_type_id:response.data[0].id,
                            }
                            $('#leave-type-edit-modal').modal('show')

                        })
                        .catch(function (error) {
                            var message = '';
                            console.log(error);
                            swal.fire({
                                title:"Error!",
                                text:error.response.data,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        });

                    console.log(self.form_data)

                },
                deleteLeaveType(id,index,name){
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete  " + name + " from this Leave Types.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return axios.delete("/mpe/leave-types/" + id)
                                .then(function (response) {
                                    $('#types-table').bootstrapTable('removeByUniqueId', response.data.id);
                                    return swal("Deleted!", "The Leave Type was successfully deleted.", "success");
                                }).catch(function (error) {
                                    var message = '';
                                    console.log(error);
                                    swal.fire({
                                        title:"Error!",
                                        text:error.response.data.message,
                                        type:"error",
                                        showLoaderOnConfirm: true,
                                    });
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()


                    });
                },
                updateLeaveType: function () {
                    $('#edit-leave-type').addClass('btn-loading btn-icon')
                    axios.put('/mpe/leave-types/'+this.form_data.leave_type_id,this.form_data)
                        .then(response=>{
                            $('#edit-leave-type').removeClass('btn-loading btn-icon')
                            form_data = {};
                            $('#leave-type-edit-model').modal('hide')

                            swal({
                                title:"Success!",
                                text:"Leave Type  Successfully Updated",
                                type:"success",
                                showLoaderOnConfirm: true,
                            }).then(function () {
                                location.reload()
                            });
                        })
                        .catch(e=>{
                            console.log(e.response.data);
                            $('#edit-leave-type').removeClass('btn-loading btn-icon')
                            swal.fire({
                                title:"Error!",
                                text:e.response.data.message,
                                type:"error",
                                showLoaderOnConfirm: true,
                            });
                        })
                },
            },
            mounted(){
            }
        })

        function processRows(row, index) {
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            row.approval = row.approvals.data.title
            row.buttons = '<a class="btn btn-sm btn-primary text-white"  data-index="'+index+'"  data-action="edit_leave_type" data-id="'+row.id+'" data-name="'+row.title+'">Update</a> &nbsp; ' +
                '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_leave_type" data-id="'+row.id+'" data-name="'+row.title+'">Delete</a> &nbsp;';
            // row.account_link = '<a href="/mfn/finance-entries?account=' + row.account.data.id + '">' + row.account.data.display_name + '</a>';
            // row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            // row.buttons = '<a class="btn btn-danger btn-sm remove" data-action="remove" href="#" data-id="'+row.id+'">Delete</a>';
            // if (typeof row.account.data !== 'undefined' && row.account.data.name == 'unconfirmed') {
            //     row.buttons += '<a class="btn btn-warning btn-sm views" data-action="views" href="/mfn/finance-entries/' + row.id + '" >Confirm</a>'
            // }
            // return row;
        }
    </script>
@endsection
