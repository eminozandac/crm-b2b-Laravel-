    <script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">

    $(function ()
    {
        $('.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: 'd-m-yyyy'
        });

        $('#assign_date').click(function ()
        {
            $('.datepicker').css({'z-index':99999});
        });

        $('#completion_date').click(function ()
        {
            $('.datepicker').css({'z-index':99999});
        });

        var events_data = [];

        var events = $('#task_events').text();
        var json_obj = jQuery.parseJSON( events );
        if(json_obj.pass == 1){
            json_obj_taskdata = json_obj.task_data;
            for (var i in json_obj_taskdata)
            {
                var obj_events_ar = {};
                obj_events_ar['task_id'] = json_obj_taskdata[i].task_id;
                obj_events_ar['title'] = json_obj_taskdata[i].title;
                /*obj_events_ar['start'] = json_obj_taskdata[i].assign_date;*/
                obj_events_ar['start'] = json_obj_taskdata[i].completion_date;
                obj_events_ar['end'] = json_obj_taskdata[i].completion_date;
                obj_events_ar['assigndata'] = json_obj_taskdata[i].assign_date;
                obj_events_ar['completiondata'] = json_obj_taskdata[i].completion_date;
                obj_events_ar['color'] = json_obj_taskdata[i].color;
                obj_events_ar['task_user'] = json_obj_taskdata[i].task_user;
                obj_events_ar['task_priority'] = json_obj_taskdata[i].task_priority;

                events_data.push(obj_events_ar);
            }
        }
        //console.log(JSON.stringify(events_data));

        $('.class-refresh').click(function (){
            window.location.reload();
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });


        /* initialize the external events
         -----------------------------------------------------------------*/

        $('#staff_task_list div.external-event').each(function() {

            // store data so the calendar knows to render an event upon drop
            $(this).data('event', {
                title: $.trim($(this).text()), // use the element's text as the event title
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 1111999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        });


        /* initialize the calendar
         -----------------------------------------------------------------*/
        var default_date = '';
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        default_date = y+'-'+m+'-'+d;

        function changeTaskDate()
        {
            var tokendata = $('#hidden_token').val();
            var task_id = $('#task_id').val();
            var assign_date = $('#assign_date').val();
            var completion_date = $('#completion_date').val();

            console.log('Assign Date'+assign_date+'Completion Date'+completion_date);
            $.ajax
            ({
                type: "POST",
                url: "{{ URL::to('staff/ajax/log/changetaks') }}",
                data: {_token: tokendata, data_taskID: task_id, data_assign_date: assign_date, data_completion_date: completion_date, },
                success: function (result)
                {
                    $('#taskassignDateModel .close').trigger('click');
                    window.location.reload();
                }
            });
        }

        $('#btn_model_task').click(function (){
            changeTaskDate();
        });

        function taskPrompt()
        {
            $('#date_changeTask').trigger('click');
        }

        $('#staff_task_calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            selectable: true,
            selectHelper: true,
            timeFormat: '',
            axisFormat: '',
            defaultDate: '<?php echo date('Y-m-d');?>',
            editable: true,
            eventLimit: true,
            views: {
                agenda: {
                    eventLimit: 6
                }
            },
            eventClick: function(calEvent, jsEvent, view) {

                /*console.log('Event: ' + calEvent.title);
                console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                console.log('View: ' + view.name);*/

                /*console.log('Event: ' + calEvent.task_id);
                console.log('Event: ' + calEvent.title);
                console.log('Event: ' + calEvent.start.format());
                console.log('Event: ' + calEvent.completiondata);*/

                var assign_date = new Date(calEvent.assigndata);
                /*assign_date = (assign_date.getMonth() + 1) + '/' + assign_date.getDate() + '/' +  assign_date.getFullYear();*/
                assign_date = assign_date.getDate() + '-' + (assign_date.getMonth() + 1) + '-' +  assign_date.getFullYear();

                var completion_date = new Date(calEvent.completiondata);
                /*completion_date = (completion_date.getMonth() + 1) + '/' + completion_date.getDate() + '/' +  completion_date.getFullYear();*/
                completion_date =  completion_date.getDate() + '-' + (completion_date.getMonth() + 1) + '-' +  completion_date.getFullYear();

                console.log('Assign Data : ' + assign_date+' Completion Data : ' + completion_date);


                $('#info_task_id').val('');
                $('#info_task_id').val(calEvent.task_id);

                $('#info_taks_title').text('');
                $('#info_taks_title').text(calEvent.title);

                $('#info_taks_start').text('');
                $('#info_taks_start').text(assign_date);

                $('#info_taks_end').text('');
                $('#info_taks_end').text(completion_date);

                $('#info_taks_staff').text('');
                $('#info_taks_staff').text(calEvent.task_user);

                $('#info_task_priority').text('');
                $('#info_task_priority').text(calEvent.task_priority);

                if(calEvent.task_priority == 'HIGH'){
                    $('#info_task_priority').css({'background-color':'#CF000F'});
                }if(calEvent.task_priority == 'MEDIUM'){
                    $('#info_task_priority').css({'background-color':'#1AB394'});
                }if(calEvent.task_priority == 'LOW'){
                    $('#info_task_priority').css({'background-color':'#F8AC59'});
                }


                $('#task_info').trigger('click');


            },
            droppable: true, // this allows things to be dropped onto the calendar
            drop: function() {
                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                }

            },
            eventDrop: function(event,revertFunc) {

                console.log(event.task_id +"/" +event.title + " was dropped on start date " + event.start.format() + " ends date" + event.end.format());

                swal({
                    title: "Are you sure?",
                    text: "Assign Date and Completion Date To Change??",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm){
                    if (isConfirm)
                    {
                        $('#taks_title').text('');
                        $('#taks_title').text(event.title);

                        $('#task_id').val('');
                        $('#task_id').val(event.task_id);

                        var assign_date = new Date(event.start.format());
                        /*var newassignDate = (assign_date.getMonth() + 1) + '/' + assign_date.getDate() + '/' +  assign_date.getFullYear();*/
                        var newassignDate = assign_date.getDate() + '-' + (assign_date.getMonth() + 1) + '-' +  assign_date.getFullYear();

                        var completion_date = new Date(event.completiondata);
                        /*var newcompletionDate = (completion_date.getMonth() + 1) + '/' + completion_date.getDate() + '/' +  completion_date.getFullYear();*/
                        var newcompletionDate = completion_date.getDate() + '-' + (completion_date.getMonth() + 1) + '-' +  completion_date.getFullYear();

                        /*var assignDate = event.start.format().split('-');
                        var newassignDate = assignDate[1] + '/' + assignDate[2] + '/' + assignDate[0];*/

                        $('#assign_date').val(newassignDate);
                        $('#assign_date').datepicker("setDate",newassignDate);
                        $('#assign_date').datepicker('update');

                        /*var completionDate = event.end.format().split('-');
                        var newcompletionDate = completionDate[1] + '/' + completionDate[2] + '/' + completionDate[0];*/

                        $('#completion_date').val(newcompletionDate);
                        $('#completion_date').datepicker("setDate",newcompletionDate);
                        $('#completion_date').datepicker('update');


                        taskPrompt();
                    }else{
                        window.location.reload();
                    }
                });
            },
            events:events_data
        });
    });
</script>