<?php


class EventPrivate extends AppModel {

    public $stages_arr = array();
    public $stages_add_arr = array();
    public $stages_add_tree_arr = array();
    public $stages_add_tree_time_arr = array();
    public $stages_remove_arr = array();
    public $schedule_data = array();

    public $schedule_startdate = null;
    public $schedule_enddate = null;

    public function getWeekOfTheMonth($dateTimestamp){

        $d = date('j',$dateTimestamp);
        $w = date('w',$dateTimestamp)+1; //add 1 because date returns value between 0 to 6
        $dt= (floor($d % 7)!=0)? floor($d % 7) : 7;
        $k = ($w-$dt);
        $W= ceil(($d+$k)/7);
        return $W ;
    }


    public function encodeStageId($group,$events_id,$datetime,$private_group_number){
        if ($group){
            $gp = 1;
        } else {
            $gp = 2;
        }

        if ($private_group_number != null){
            $private_group_number = str_pad((string)$private_group_number, 2, "0", STR_PAD_LEFT);
        } else {
            $private_group_number = "00";
        }
        $events_id = str_pad((string)$events_id, 6, "0", STR_PAD_LEFT);
        $datetime_str = date("ymdHi",strtotime($datetime));
        $stageId = $gp.$events_id.$datetime_str.$private_group_number;
        return $stageId;
    }


    public function getStages($events_id, $date_start, $date_end){
        $this->EventsSchedule           = ClassRegistry::init('EventsSchedule');
        $this->EventsStagePaxRemaining  = ClassRegistry::init('EventsStagePaxRemaining');

        $this->schedule_startdate = $date_start;
        $this->schedule_enddate = $date_end;




        if ($events_id != 0){
            $conditions_arr['EventsSchedule.events_id'] = $events_id;
        }
        if ($date_start != 0){
            $conditions_arr['EventsSchedule.date_start <= '] = $date_start;
        }
        if ($date_end != 0){
            $conditions_arr['EventsSchedule.date_end >= '] = $date_end;
        }



        $pending = $this->EventsSchedule->find(
            'all',
            array(
                'recursive' => 1,
                'conditions' => array(
                    'EventsSchedule.events_id' => $events_id,
                    'EventsSchedule.gp' => 2,
                    'EventsSchedule.active' => true,
                    'EventsSchedule.pending' => true,
                    'OR' => array(
                        'OR' => array(




                            // Total Overlap
                            array(
                                'EventsSchedule.repeat IS NOT NULL',
                                'EventsSchedule.date_start <=' => $date_start,
                                'EventsSchedule.date_end >=' => $date_end,
                            ),

                            // Total Inclusion
                            array(
                                'EventsSchedule.repeat IS NOT NULL',
                                'EventsSchedule.date_start >=' => $date_start,
                                'EventsSchedule.date_end <=' => $date_end,
                            ),

                            // Starts during and end after
                            array(
                                'EventsSchedule.repeat IS NOT NULL',
                                'EventsSchedule.date_start >=' => $date_start,
                                'EventsSchedule.date_start <=' => $date_end,
                                'EventsSchedule.date_end >=' => $date_end,
                            ),

                            // Starts and ends during
                            array(
                                'EventsSchedule.repeat IS NOT NULL',
                                'EventsSchedule.date_start <=' => $date_start,
                                'EventsSchedule.date_end >=' => $date_start,
                                'EventsSchedule.date_end <=' => $date_end,

                            ),



                        ),
                        array(
                            'EventsSchedule.repeat IS NULL',
                            'EventsSchedule.date_start > ' => $date_start,
                        ),
                    ),
                ),
                'order' => array(
                    'EventsSchedule.orderby ASC'
                )
            )
        );




        //pr($pending);


        $this->ScheduleDates($pending); // this calculates stages and stores in $this->Event->stages_arr
        //echo 'Timeslot count: '.count($this->Event->stages_arr).'<br />';
        $stage_arr_unformated = $this->stages_arr;

        $stage_arr_ordered = array();
        foreach($stage_arr_unformated as $unformated_key => $unformated_arr){
            $stage_arr_ordered[strtotime($unformated_arr['datetime'])] = array(
                'id' => $unformated_key,
                'str_id' => '' . $unformated_key,
                'events_id' => $unformated_arr['events_id'],
                'datetime' => $unformated_arr['datetime'],
            );
        }

        $stage_arr = array();
        foreach($stage_arr_ordered as $pax) {
	    if(strtotime($pax['datetime']) > strtotime('NOW + 10 days')){
            list($date, $time) = explode(' ', $pax['datetime']);

            if(!isset($stage_arr[$date])) {
                $stage_arr[$date] = array();
            }

            $stage_arr[$date][$time] = array(
                'id' => $pax['id'],
                'events_id' => $pax['events_id'],
                'date' => $date,
                'time' => $time,
                'pretty_time' => date('g:i a', strtotime($time)),
                'group' => "0",
                'pax_remaining' => "72"
            );
        }
	}
        return $stage_arr;
    }


    public function ScheduleDates($pending){

        //return false;

        foreach ($pending as $pending_loop){

            $query_end = strtotime($this->schedule_enddate);
            $query_start = strtotime($this->schedule_startdate);
            $end_date = strtotime($pending_loop['EventsSchedule']['date_end']);
            $date_start = strtotime($pending_loop['EventsSchedule']['date_start']);
            $horizon_date = strtotime("+".$pending_loop['Event']['horizon']." day", time());
            $now_date = time();





            if ($query_end != 0){
                if ($query_end < $end_date){
                    $new_horizon = date("Y-m-d",$query_end);
                } else {
                    $new_horizon = date("Y-m-d",$end_date);
                }
            } else {

                // this is for full build; check to see if it's past horizon or not and select the small datetime
                if ($end_date < $horizon_date){
                    if ($pending_loop['EventsSchedule']['events_id'] == 7){
                        //echo '<br /><span style="color:Pink;">Set End to Query End</span><br />';
                    }
                    $new_horizon = date("Y-m-d",$end_date);
                } else {
                    if ($pending_loop['EventsSchedule']['events_id'] == 7){
                        //echo '<br /><span style="color:Red;">Set End to Horizon Date</span><br />';
                    }
                    $new_horizon = date("Y-m-d",$horizon_date);
                }
            }
            $new_horizon = date("Y-m-d",strtotime("+ 1 day", strtotime($new_horizon)));
            $pending_loop['EventsSchedule']['horizon_date'] = $new_horizon;


            if ($query_start != 0){
                // queried
                if ($query_start > $date_start){
                    $new_start = date("Y-m-d",$query_start);
                } else {
                    $new_start = date("Y-m-d",$date_start);
                }
            } else {
                // cron
                if ($date_start > $now_date){
                    $new_start = date("Y-m-d",$date_start);
                } else {
                    $new_start = date("Y-m-d",$now_date);
                }
            }
            $pending_loop['EventsSchedule']['date_start'] = $new_start;


            if ($pending_loop['EventsSchedule']['events_id'] == 7){
                //echo '<span style="color:green;">'.$pending_loop['EventsSchedule']['date_start'].' - '.$pending_loop['EventsSchedule']['horizon_date'].'</span><br />';
            }


            $this->schedule_data = $pending_loop['EventsSchedule'];
            $this->event_data = $pending_loop['Event'];

            //
            if ($pending_loop['EventsSchedule']['events_id'] == 7){
                //echo 'Schedule: '.$pending_loop['EventsSchedule']['id'].' -- Start:'.$new_start.' End:'.$new_horizon.'<br />';
            }

            if ($pending_loop['EventsSchedule']['repeat'] != null ){

                // REPEATING * REPEATING * REPEATING * REPEATING * REPEATING *

                // repeat daily * repeat daily * repeat daily * repeat daily * repeat daily *
                if ($pending_loop['EventsSchedule']['repeat'] == 1 && $pending_loop['EventsSchedule']['repeat']){

                    for (
                        $looping_date = strtotime($pending_loop['EventsSchedule']['date_start'].' '.$pending_loop['EventsSchedule']['time']);
                        $looping_date < strtotime($pending_loop['EventsSchedule']['horizon_date']);
                        $looping_date = strtotime("+".$pending_loop['EventsSchedule']['repeat_every']." day", $looping_date)
                    ) {

                        //echo '<span style="color:purple;">loop date: '.date("Y-m-d H:i",$looping_date).'</span><br />';
                        // --XX--
                        $this->schedule_pending_stage(
                            date("Y-m-d",$looping_date).' '.$pending_loop['EventsSchedule']['time']
                        );
                        // --XX--
                    }
                }
                // repeat weekly * repeat weekly * repeat weekly * repeat weekly *
                if ($pending_loop['EventsSchedule']['repeat'] == 2 && $pending_loop['EventsSchedule']['repeat']){

                    for (
                        $looping_date = strtotime("-".date("w",strtotime($pending_loop['EventsSchedule']['date_start']))." day", strtotime($pending_loop['EventsSchedule']['date_start']));
                        $looping_date < strtotime($pending_loop['EventsSchedule']['horizon_date']);
                        $looping_date = strtotime("+".$pending_loop['EventsSchedule']['repeat_every']." week", $looping_date)
                    ) {
                        for ($weekly_day = 0; $weekly_day <= 6; $weekly_day++) {

                            $day_date = strtotime("+".$weekly_day." day", $looping_date);
                            $day_loop = strtolower(date("w",$day_date));
                            $day_day = strtolower(date("l",$day_date));

                            if ($pending_loop['EventsSchedule'][$day_day]){
                                // --XX--
                                if ($day_date >= strtotime($pending_loop['EventsSchedule']['date_start'])){

                                    if ($day_date < strtotime($pending_loop['EventsSchedule']['horizon_date'])){

                                        $this->schedule_pending_stage(
                                            date("Y-m-d",$day_date).' '.$pending_loop['EventsSchedule']['time']
                                        );
                                    }
                                }
                                // --XX--
                            }
                        }
                    }
                }


                // repeat monthly * repeat monthly * repeat monthly * repeat monthly *
                if ($pending_loop['EventsSchedule']['repeat'] == 3 && $pending_loop['EventsSchedule']['repeat']){

                    $weekly_date = strtotime($pending_loop['EventsSchedule']['date_start']);
                    $weekly_repeat_day_name = date("l",$weekly_date);
                    $weekly_repeat_week_number = $this->getWeekOfTheMonth($weekly_date);

                    $weekname_arr = array(
                        1 => 'first',
                        2 => 'second',
                        3 => 'third',
                        4 => 'fourth',
                        5 => 'fifth',
                    );

                    for (
                        $looping_date = strtotime($pending_loop['EventsSchedule']['date_start']);
                        $looping_date < strtotime($pending_loop['EventsSchedule']['horizon_date']);
                        $looping_date = strtotime("+".$pending_loop['EventsSchedule']['repeat_every']." month", $looping_date)
                    ) {
                        if ($pending_loop['EventsSchedule']['monthly_repeat'] == 1){
                            // 1- day or the month;

                            $this->schedule_pending_stage(
                                date("Y-m-d",$looping_date).' '.$pending_loop['EventsSchedule']['time']
                            );
                        } else if ($pending_loop['EventsSchedule']['monthly_repeat'] == 2){
                            // 2-day of the week;
                            $weekly_date_selected = strtotime($weekname_arr[$weekly_repeat_week_number]." ".$weekly_repeat_day_name, strtotime(date("Y-m-01",$looping_date)));

                            $this->schedule_pending_stage(
                                date("Y-m-d",$weekly_date_selected).' '.$pending_loop['EventsSchedule']['time']
                            );
                        }
                    }
                }
            } else {


                // NO REPEAT * NO REPEAT * NO REPEAT * NO REPEAT * NO REPEAT *
                $this->schedule_pending_stage(
                    $pending_loop['EventsSchedule']['date_start'].' '.$pending_loop['EventsSchedule']['time']
                );
            }
        }
        //echo 'ScheduleDates count: '.count($this->stages_arr).'<br />';
    }


    public function schedule_pending_stage($datetime) {

        if ($this->schedule_data['events_id'] == 7){
            //echo '<span style="color:yellow;">Dates: '.$datetime.' '.$this->schedule_data['name'].'</span><br />';
        }

        if (
            (
                ($this->schedule_data['repeat']) &&
                (strtotime(date("Y-m-d",strtotime($datetime))) >= strtotime(date("Y-m-d")) ) &&
                (strtotime(date("Y-m-d",strtotime($datetime))) <= strtotime($this->schedule_data['horizon_date']))
            ) || (
                (!$this->schedule_data['repeat']) &&
                (strtotime(date("Y-m-d",strtotime($datetime))) >= strtotime(date("Y-m-d")) )
            )

        ){
            //if ($this->schedule_data['events_id'] == 7){
            //    echo '<span style="color:green;">Cleared date check on schedule_pending_stage</span><br />';
            //}


            // ADD * ADD * ADD * ADD * ADD * ADD * ADD * ADD * ADD * ADD * ADD *
            //echo '<span style="color:green;">'.date("Y-m-d -- H:i -- l",strtotime($datetime)).'</span><br />';


            $add_template_arr = array(
                'events_id'     => $this->schedule_data['events_id'],
                'orderby'       => $this->schedule_data['orderby'],
                'schedule'      => $this->schedule_data['id'],
                'name_short'    => $this->event_data['name_short'],
                'duration'      => $this->event_data['duration']
            );



            if ($this->schedule_data['time_interval'] && $this->schedule_data['time_end']){


                for (
                    $looping_date = strtotime(date("Y-m-d",strtotime($datetime)).' '.$this->schedule_data['time']);
                    $looping_date <= strtotime(date("Y-m-d",strtotime($datetime)).' '.$this->schedule_data['time_end']);
                    $looping_date = strtotime("+".$this->schedule_data['time_interval']." minutes ", $looping_date)
                ) {

                    $add_arr = $add_template_arr;
                    $add_arr['datetime'] = date("Y-m-d H:i",$looping_date);

                    if ($this->schedule_data['add_remove'] == 1){
                        if ($this->schedule_data['gp'] == 1){
                            // group
                            $add_arr['group'] = 1;
                            $add_arr['pax_max'] = $this->schedule_data['pax_max'];
                            $stage_id = $this->encodeStageId(TRUE,$add_arr['events_id'],$add_arr['datetime'],NULL, '0', '0', '');
                            $this->stages_arr[$stage_id] = $add_arr;
                            $this->stages_add_arr[$stage_id] = $add_arr;

                        }
                        if ($this->schedule_data['gp'] == 2){
                            // private

                            //echo 'Loop:'.date("Y-m-d H:i",$looping_date).' | start date: '.$this->schedule_data['date_start'].' | end: '.$this->schedule_data['time_end'].' | id: '.$this->schedule_data['id'].' <br />';

                            $add_arr['group'] = 0;
                            $stage_id = $this->encodeStageId(FALSE,$add_arr['events_id'],$add_arr['datetime'],NULL, '0', '0', '');
                            $this->stages_arr[$stage_id] = $add_arr;
                            $this->stages_add_arr[$stage_id] = $add_arr;

                        }
                        $this->stages_add_tree_arr
                        [$add_arr['group']]
                        [$add_arr['events_id']]
                        [date("Y",strtotime($add_arr['datetime']))]
                        [date("n",strtotime($add_arr['datetime']))]
                        [date("j",strtotime($add_arr['datetime']))][] = $stage_id
                        ;
                        //pr($this->stages_add_tree_arr);

                        $this->stages_add_tree_time_arr
                        [$add_arr['group']]
                        [$add_arr['events_id']]
                        [date("Y",strtotime($add_arr['datetime']))]
                        [date("n",strtotime($add_arr['datetime']))]
                        [date("j",strtotime($add_arr['datetime']))]
                        [date("G",strtotime($add_arr['datetime']))]
                        [date("i",strtotime($add_arr['datetime']))] = $stage_id
                        ;
                        //pr($this->stages_add_tree_time_arr);

                    } else if ($this->schedule_data['add_remove'] == 2){
                        // REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE *
                        //echo '<span style="color:purple;">'.date("Y-m-d -- H:i -- l",strtotime($add_arr['datetime'])).' -- '.$add_arr['events_id'].' Repeat</span><br />';
                        $this->remove_pending_stage($add_arr['datetime']);
                    }
                }


            } else {


                $add_arr = $add_template_arr;
                $add_arr['datetime'] = $datetime;

                if ($this->schedule_data['add_remove'] == 1){
                    // private only
                    $add_arr['group'] = 0;
                    $stage_id = $this->encodeStageId(FALSE,$add_arr['events_id'],$datetime,NULL);
                    $this->stages_arr[$stage_id] = $add_arr;
                    $this->stages_add_arr[$stage_id] = $add_arr;

                    $this->stages_add_tree_arr
                    [$add_arr['group']]
                    [$add_arr['events_id']]
                    [date("Y",strtotime($add_arr['datetime']))]
                    [date("n",strtotime($add_arr['datetime']))]
                    [date("j",strtotime($add_arr['datetime']))][] = $stage_id
                    ;
                } else if ($this->schedule_data['add_remove'] == 2){
                    // REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE * REMOVE *
                    //echo '<span style="color:red;">'.date("Y-m-d -- H:i -- l",strtotime($datetime)).' -- '.$add_arr['events_id'].' Single</span><br />';
                    $this->remove_pending_stage($datetime);
                }
            }





        } else {
            //echo '<span style="color:cyan;">Past Now: '.$datetime.'</span><br />';
        }
    }

    // 0oOo0 (o) 0oOo0 (o) 0oOo0 (o) 0oOo0 (o) 0oOo0 (o) 0oOo0 (o) 0oOo0 (o) 0oOo0

    public function remove_pending_stage($datetime) {
        //echo '<span style="color:orange;">'.$datetime.' - events_id: '.$this->schedule_data['events_id'].' - id: '.$this->schedule_data['id'].'</span><br />';
        //pr($this->schedule_data);schedule_pending_stage


        if ($this->schedule_data['events_id'] == 7){
            //echo '<span style="color:Fuchsia;">DELETE -- '.$datetime.' '.$this->schedule_data['name'].'</span><br />';
        }


        //echo 'Before Removal count: '.count($this->stages_arr).'<br />';


        if ($this->schedule_data['remove_all_day']){
            $arr_date = date("Y-m-d",strtotime($datetime));
            $allDay = true;
            //echo '<span style="color:pink;">all day</span><br />';
        } else {
            $arr_date = $datetime;
            $allDay = false;
            //echo '<span style="color:pink;">single time slot</span><br />';
        }

        $remove_template_arr = array(
            'datetime' => $arr_date,
            'events_id' => $this->schedule_data['events_id'],
            'schedule' => $this->schedule_data['id'],
            'orderby' => $this->schedule_data['orderby'],
            'name_short'    => $this->event_data['name_short'],
            'allDay' => $allDay
        );

        if ($this->schedule_data['gp'] == 1){
            $gp_remove = 1;
        }else{
            $gp_remove = 0;
        }

        if ($this->schedule_data['remove_all_day']){
            if (isset($this->stages_add_tree_arr[$gp_remove][$this->schedule_data['events_id']][date("Y",strtotime($datetime))][date("n",strtotime($datetime))][date("j",strtotime($datetime))])){
	    

	    $tree_arr = $this->stages_add_tree_arr
            [$gp_remove]
            [$this->schedule_data['events_id']]
            [date("Y",strtotime($datetime))]
            [date("n",strtotime($datetime))]
            [date("j",strtotime($datetime))];
            //pr($tree_arr);
            //pr($this->stages_arr);
            if ($tree_arr){
                foreach ($tree_arr as $stage_id){
                    unset($this->stages_arr[$stage_id]);
                }
            }
	    }
        } else {
	    if (isset($this->stages_add_tree_time_arr[$gp_remove][$this->schedule_data['events_id']][date("Y",strtotime($datetime))][date("n",strtotime($datetime))][date("j",strtotime($datetime))][date("G",strtotime($datetime))][date("i",strtotime($datetime))])){
            $stage_id = $this->stages_add_tree_time_arr
            [$gp_remove]
            [$this->schedule_data['events_id']]
            [date("Y",strtotime($datetime))]
            [date("n",strtotime($datetime))]
            [date("j",strtotime($datetime))]
            [date("G",strtotime($datetime))]
            [date("i",strtotime($datetime))]
            ;
            unset($this->stages_arr[$stage_id]);
	    }
        }
    }

} 
