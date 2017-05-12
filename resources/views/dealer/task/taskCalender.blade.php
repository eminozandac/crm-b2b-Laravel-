@extends('dealer.layouts.masterdealer')

@section('pagecss')

    <style>
        a,a:hover, a:active, a:focus, a.class-refresh{
            outline: 0;
        }
        .fc-event{
            line-height: 1.4em;
        }
    </style>
@stop()

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Calendar</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/dealer')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/dealer/task')}}">All Task</a>
                    </li>
                    <li class="active">
                        <strong> Calendar </strong>
                    </li>
                </ol>
            </div>
        </div>

            @include('dealer.task.taskCalenderData')
		@stop()

        @section('pagescript')
            @include('dealer.includes.commonscript')
            <!-- Full Calendar -->
            <script src="{{ asset('assets/js/plugins/fullcalendar/fullcalendar.min.js') }}"></script>

            @include('dealer.task.taskCalenderScript')
        @stop()