@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')

    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <a  class="btn btn-primary col-md-2 ml-auto mb-2" href="{{route('leave-request-create')}}" >
                    Apply for leave here
                </a>
                <div class="col-sm-12" id="request">
                    @if(!empty($requests))
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                                   data-pagination="true"
                                   data-search="true"
                                   data-side-pagination="server"
                                   data-show-refresh="true"
                                   data-unique-id="id"
                                   data-id-field="id"
                                   data-row-attributes="processRows"
                                   data-url="{{ route('leave-request-search') . '?' . http_build_query($args) }}"
                                   data-page-list="[10,25,50,100,200,300,500]"
                                   data-sort-class="sortable"
                                   data-search-on-enter-key="true"
                                   id="request-table"
                                   v-on:click="clickAction($event)">
                                <thead>
                                <tr>
                                    <th data-field="approval_title">Approval</th>
                                    <th data-field="leave_type">Leave Type</th>
                                    <th data-field="days_utilized">Days Utilized</th>
                                    <th data-field="days_remaining">Days Remaining</th>
                                    <th data-field="days_requesting">Days Requesting</th>
                                    <th data-field="start_date">Start Date</th>
                                    <th data-field="report_back">Reporting  Back</th>
                                    <th data-field="status">Status</th>
                                    <th data-field="created_at">Added on </th>
                                    <th data-field="buttons">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                    @include('modules-people-leaves::modals.rejections')
                    @else
                        <div class="col s12" >
                            @component('layouts.blocks.tabler.empty-fullpage')
                                @slot('title')
                                    No Leave Request   Generated
                                @endslot
                                <a href="/mpe/leave-request/create" class="btn btn-primary" >Add Leave Request </a>
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
            el: '#request',
            data:{
              comments: null,
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
                            break;
                        case 'view_rejections':
                            this.viewRejections(id);
                            break;
                    }

                },
                async viewRejections(id){
                  const self = this;
                  await axios.get("/mpe/leave-request/single/" + id)
                      .then(function (response) {
                        self.comments = response.data[0].rejection_comments;
                          $('#rejections').modal('show')

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

                      console.log(self.comments);
                }
            },
            mounted(){
            }
        })
        function processRows(row, index) {
            row.report_back = moment(row.report_back).format('DD MMM, YYYY');
            row.leave_type = row.leavetype.data[0]['title']
            row.approval_title = row.leavetype.data[0]['approvals']['data']['title']
            row.start_date = moment(row.start_date).format('DD MMM, YYYY');
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            if(row.status === 'active'){
                row.status = '<a class="badge badge-pill badge-primary text-white">Active</a>'

            }
            else if(row.status === 'approved'){
                row.status = '<a class="badge badge-pill badge-success text-white">Approved</a>'

            }
            else{
                row.status = '<a class="badge badge-pill badge-danger text-white">Declined</a>';
                row.buttons = '<a class="btn btn-sm btn-primary text-white"   href="/mpe/leave-request/'+row.id+'">Update</a> &nbsp; ' +
                '<a class="btn btn-sm text-white btn-danger " data-id="'+row.id+'" data-action="view_rejections">View  Comments</a>';

            }
        }
    </script>
@endsection
