@extends('dealer.layouts.masterdealer')

@section('pagecss')
    <style>
        *,a{
            outline: 0;
        }
    </style>
@stop

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Newsletter</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/dealer')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/dealer/newslatter')}}">All Newsletter</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Newsletter</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="panel-body">
                                <div class="panel-group" id="accordion">
                                    @if(!empty($campaignslist) && (count($campaignslist['campaigns']) != 0) )
                                        <?php
                                            $no = 0;
                                            $campaigns = array_reverse($campaignslist['campaigns'],true);
                                        ?>
                                        @foreach($campaigns as $key => $value)
                                            <?php
                                                $no++;
                                                $subject_line = '';
                                                $title = '';
                                                if(isset($value['settings']['subject_line'])){
                                                    $subject_line = $value['settings']['subject_line'];
                                                }

                                                if(isset($value['settings']['title'])){
                                                    $title = $value['settings']['title'];
                                                }
                                                $url  = 'dealer/campaigncontent/'.$value["id"];

                                                $sent_date = $sent_time = '';
                                                if(isset($value['send_time']) && !empty($value['send_time'])){
                                                    $sent_date = date('d-m-Y',strtotime($value['send_time']));
                                                    $sent_time = date('h:i A',strtotime($value['send_time']));
                                                }
                                            ?>
                                            @if($title != '')
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h5 class="panel-title">
                                                            <a onclick="triggerClick('{{ $no }}')" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{ $no }}" style="outline: 0;">{{ $title }}</a>
                                                            <a onclick="triggerClick('{{ $no }}')" id="a_{{ $no }}" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{ $no }}" style="outline: 0;float:right">
                                                                <i class="fa fa-chevron-down"></i>
                                                            </a>
                                                        </h5>
                                                        <span>{{ $subject_line }}</span>
                                                        <span style="float: right;color: #18A689;">{{ $sent_date }} {{ $sent_time }} </span>
                                                    </div>
                                                    <div id="collapse_{{ $no }}" class="panel-collapse collapse">
                                                        <div class="panel-body" style="height: 100vh;">
                                                            <iframe src="{{ URL::to($url) }}" title="{{ $title }}" width="100%" height="100%"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
		@stop()

        @section('pagescript')
            @include('dealer.includes.commonscript')
            <script type="text/javascript">
                new_triggerClick = null;
                $(function ()
                {
                    function triggerClicked(no)
                    {
                        $('.collapsed i.fa').addClass('fa-chevron-down').removeClass('fa-chevron-up');

                        var a_tag = '#a_'+no+' i.fa';
                        var className = $(a_tag).attr('class');
                        if(className == 'fa fa-chevron-down')
                        {
                            $(a_tag).addClass('fa-chevron-up').removeClass('fa-chevron-down');
                        }else{
                            $(a_tag).addClass('fa-chevron-down').removeClass('fa-chevron-up');
                        }
                    }
                    new_triggerClick = triggerClicked;
                });
                function triggerClick(no)
                {
                    new_triggerClick(no);
                }
            </script>
        @stop()