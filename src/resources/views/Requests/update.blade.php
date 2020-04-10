@extends('layouts.tabler')
@section('body_content_header_extras')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')

    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
                <div class="col-sm-12" id="approvals">
                    <div class="col-md-12" >
                        <div class="col-md-12 align-items-end" >
                            <a class="float-le btn btn-primary btn-pill" href="{{route('leave-request-main')}}">
                                <span><i class="fe fe-arrow-left"></i></span>
                                Back to Requests
                            </a>
                        </div>
                        <br/>
                        <form action="{{route('leave-request-update',['id'=>$leaveRequest->id])}}"  method="post">
                            {{csrf_field()}}
                            <fieldset>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="transaction">Select Leave Type </label>
                                            <select   class="form-control  " id="select2"  name="type_id"  >
                                                <option value="{{$leaveRequest->leavetype['data'][0]['id']}}" >{{$leaveRequest->leavetype['data'][0]['title']}}</option>
                                                @foreach($leaveTypes as $type)
                                                    <option value="{{$type['id']}}">{{$type['title']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" >Days Requesting</label>
                                            <input class="form-control" value="{{$leaveRequest->days_requesting}}" placeholder="Number of days requesting"  type="number" name="count_requesting" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <label class="form-label" >Start Date</label>
                                                    <input  type="text" value="{{$leaveRequest->start_date}}"  class="form-control custom-datepicker1" name="data_start_date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" >Contact Address</label>
                                            <input class="form-control" value="{{$leaveRequest->contact_address}}" placeholder="Contact Address"  type="text" name="data_contact_address" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" >Contact Phone</label>
                                            <input class="form-control" value="{{$leaveRequest->contact_phone}}" placeholder="Contact Phone"  type="text" name="data_contact_phone" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" >Backup  Staff</label>
                                            <input class="form-control" value="{{$leaveRequest->backup_staff}}" placeholder="Backup Staff"  type="text" name="data_backup_staff" required>
                                        </div>


                                        <div class="form-group">
                                            <label class="form-label" >  Remarks</label>
                                            <input class="form-control" value="{{$leaveRequest->remarks}}" placeholder="Enter Request Remarks"  type="text" name="data_remarks" required>
                                        </div>

                                    </div>

                                </div>
                                <button type="submit"   class="btn btn-primary">Submit</button>

                            </fieldset>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
@section('body_js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>

    <script>
        function Match (params, data) {
            // If there are no search terms, return all of the data
            if ($.trim(params.term) === '') { return data; }

            // Do not display the item if there is no 'text' property
            if (typeof data.text === 'undefined') { return null; }

            // `params.term` is the user's search term
            // `data.id` should be checked against
            // `data.text` should be checked against
            var q = params.term.toLowerCase();
            if (data.text.toLowerCase().indexOf(q) > -1 || data.id.toLowerCase().indexOf(q) > -1) {
                return $.extend({}, data, true);
            }

            // Return `null` if the term should not be displayed
            return null;
        };

        $('.custom-datepicker1').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('.custom-datepicker2').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $("#select3").select2({
            matcher: Match
        });
        $("#select4").select2({
            matcher: Match
        });


    </script>
@endsection

