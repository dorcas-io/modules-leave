@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

    @include('layouts.blocks.tabler.alert')


    <div class="row">

        @include('layouts.blocks.tabler.sub-menu')
        <div class="col-md-9 col-xl-9">
            <div class="row row-cards row-deck " >
               @if (auth()->user()->is_employee !== 1)
                    <div class="col-sm-6">
                        @component('layouts.blocks.tabler.empty-fullpage')
                            @slot('title')
                                Configuration
                            @endslot
                            @slot('buttons')
                            @endslot
                            <div class="card-body">
                                <div class="card-text">
                                    Manage multiple settings such as Leave Types, Leave Groups
                                </div>
                            </div>
                            <div class="row row-cards row-deck">
                                <div class="col-md-4 mt-4 ">
                                    <a  href="{{route('leave-types-main')}}" class="btn btn-primary text-white  ">  Types </a>

                                </div>
                                <div class="col-md-4 mt-4">
                                    <a href="{{route('leave-groups-main')}}" class="btn btn-primary text-white"> Groups </a>


                                </div>

                            </div>
                        @endcomponent
                    </div>
               @endif
                   <div class="col-sm-6">
                       @component('layouts.blocks.tabler.empty-fullpage')
                           @slot('title')
                                Requests
                           @endslot
                           @slot('buttons')
                           @endslot
                           <div class="card-body">
                               <div class="card-text">
                                   Leave Request: Apply for a Leave Here
                               </div>
                           </div>
                           <div class="row row-cards row-deck">
                               <div class="col-md-4 mt-4">
                                   <a href="{{route('leave-request-main')}}" class="btn btn-primary text-white"> Apply  </a>


                               </div>

                           </div>
                       @endcomponent
                   </div>

            </div>

        </div>
    </div>

@endsection
@section('body_js')

@endsection
