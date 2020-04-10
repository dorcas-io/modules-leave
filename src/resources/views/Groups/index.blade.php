@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <a  class="btn btn-primary col-md-2 ml-auto mb-2" href="/mpe/leave-groups/create" >
                    Add  Group
                </a>
                <div class="col-md-12 align-items-end" >

                    <a href="{{route('leave-main')}}">
                        <span><i class="fe fe-arrow-left"></i></span>
                        Leave Home
                    </a>
                </div>
                <div class="col-sm-12" id="groups">
                    @if(!empty($groups))
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('leave-group-search') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="groups-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="group_type">Group Type</th>
                                    <th data-field="team">Group Team </th>
                                    <th data-field="duration_days">Duration of Days</th>
                                    <th data-field="duration_term">Duration Term</th>
                                    <th data-field="created_at">Added on </th>
                                    <th data-field="buttons">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @else
                        <div class="col s12" >
                            @component('layouts.blocks.tabler.empty-fullpage')
                                @slot('title')
                                    No Leave Groups   Generated
                                @endslot
                                <a href="#" class="btn btn-primary" v-on:click.prevent="showLeaveGroups">Add Leave Group </a>
                                &nbsp;
                                @slot('buttons')
                                @endslot
                            @endcomponent
                        </div>
                    @endif

                </div>



            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script>
        let LeaveGroup =  new Vue({
            el: '#groups',
            data:{
            },
            methods:{

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
                        case 'delete_leave_group':
                            this.deleteLeaveGroup(id,index,name);
                            break;
                    }

                },
                deleteLeaveGroup(id,index,name){
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete  " + name + " from the Leave Groups.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return axios.delete("/mpe/leave-groups/" + id)
                                .then(function (response) {
                                    $('#groups-table').bootstrapTable('removeByUniqueId', response.data.id);
                                    return swal("Deleted!", "The Leave Group was successfully deleted.", "success");
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

            },
            mounted(){
            }
        })
        function processRows(row, index) {
            row.team = (row.teams.data[0] === undefined ? row.departments.data[0].name: row.teams.data[0].name);
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            row.buttons = '<a class="btn btn-sm btn-primary text-white"   href="/mpe/leave-groups/update/'+row.id+'">Update</a> &nbsp; ' +
                '<a class="btn btn-sm btn-danger text-white"   data-index="'+index+'" data-action="delete_leave_group" data-id="'+row.id+'" data-name="'+row.group_type+'">Delete</a> &nbsp;';
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