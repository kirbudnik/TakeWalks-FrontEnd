<?php

class BookingsDetailsSubmit extends AppModel {

    //   -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- 

    public function encodeStageId($group, $events_id, $datetime, $private_group_number) {
        if ($group) {
            $gp = 1;
        } else {
            $gp = 2;
        }

        if ($private_group_number != null) {
            $private_group_number = str_pad((string) $private_group_number, 2, "0", STR_PAD_LEFT);
        } else {
            $private_group_number = "00";
        }
        $events_id = str_pad((string) $events_id, 6, "0", STR_PAD_LEFT);
        $datetime_str = date("ymdHi", strtotime($datetime));
        $stageId = $gp . $events_id . $datetime_str . $private_group_number;
        return $stageId;
    }

    //   -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- 

    public function decodeStageId($id) {
        $id = strval($id);
        if ($id[0] == '1') {
            $gp = 'G';
            $gp_long = 'Group';
        } else {
            $gp = 'P';
            $gp_long = 'Private';
        }
        $events_id = ltrim(substr($id, 1, 6), "0");
        $y = substr($id, 7, 2);
        $m = substr($id, 9, 2);
        $d = substr($id, 11, 2);
        $h = substr($id, 13, 2);
        $i = substr($id, 15, 2);
        $datetime = '20' . $y . '-' . $m . '-' . $d . ' ' . $h . ':' . $i;
        $private_group_number = ltrim(substr($id, 17, 2), "0");
        $arr = array(
            'gp' => $gp,
            'gp_long' => $gp_long,
            'events_id' => $events_id,
            'datetime' => $datetime,
            'private_group_number' => $private_group_number,
            'stage_id' => $id
        );
        return $arr;
    }

    //   -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- 

    public function updatePax($stage_id) {
        $this->EventsStage = ClassRegistry::init('EventsStage');
        $this->BookingsDetail = ClassRegistry::init('BookingsDetail');
	$this->EventsStage->recursive = -1;
        $this->EventsStage->id = $stage_id;
        $EventsStage = $this->EventsStage->read();
        $package_ids = array();
        
	if (!$EventsStage){
		$newPrivate_stage_arr = $this->decodeStageId($stage_id);
		$this->EventsStage->create();
		$PrivateEventsStageInsert['EventsStage'] = array(
			'id' => $stage_id,
			'events_id' => $newPrivate_stage_arr['events_id'],
			'datetime' => $newPrivate_stage_arr['datetime'],
			'stall' => 0,
			'critical' => 0,
			'group' => 0,
			'adults_price' => 0,
			'seniors_price' => 0,
			'students_price' => 0,
			'children_price' => 0,
			'infants_price' => 0,
		);
		$this->EventsStage->save($PrivateEventsStageInsert);
		$this->EventsStage->id = $stage_id;
        	$EventsStage = $this->EventsStage->read();
	}

	if ($EventsStage) {
            $BookingsDetail = $this->BookingsDetail->find('all', array(
                'conditions' => array(
                    'BookingsDetail.stage_id' => $stage_id,
                    'BookingsDetail.is_paid IS TRUE',
                    'BookingsDetail.cancelled IS NOT TRUE',
                ),
                    ));

            if ($EventsStage['EventsStage']['package_id']) {
                $package_ids[$EventsStage['EventsStage']['package_id']] = 1;
            }


            $number_adults = 0;
            $number_students = 0;
            $number_children = 0;
            $number_seniors = 0;
            $number_infants = 0;

            if ($BookingsDetail) {
                foreach ($BookingsDetail as $BookingsDetail_Loop) {
                    $number_adults += $BookingsDetail_Loop['BookingsDetail']['number_adults'];
                    $number_students += $BookingsDetail_Loop['BookingsDetail']['number_students'];
                    $number_children += $BookingsDetail_Loop['BookingsDetail']['number_children'];
                    $number_seniors += $BookingsDetail_Loop['BookingsDetail']['number_seniors'];
                    $number_infants += $BookingsDetail_Loop['BookingsDetail']['number_infants'];
                }

            }
            $pax_total = $number_adults + $number_students + $number_children + $number_seniors;

            $EventsStage['EventsStage']['pax_adults'] = $number_adults;
            $EventsStage['EventsStage']['pax_students'] = $number_students;
            $EventsStage['EventsStage']['pax_children'] = $number_children;
            $EventsStage['EventsStage']['pax_seniors'] = $number_seniors;
            $EventsStage['EventsStage']['pax_infants'] = $number_infants;
            $EventsStage['EventsStage']['pax_total'] = $pax_total;
            $this->EventsStage->save($EventsStage);

            if (($EventsStage['EventsStage']['group'] == false) && ($pax_total == 0)) {

                // THIS IS GOING TO BE A PROBLEM IN THE FUTURE.
                //$this->EventsStage->delete($stage_id);
            }

            if ($package_ids) {
                //pr($package_ids);
                foreach ($package_ids as $package_ids_loop => $nothing) {

                    $this->updatePaxPackage($package_ids_loop);
                }
            }
        }
    }

    //   -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- 

    public function updatePaxPackage($package_stageid) {

        //echo 'package id: '.$package_stageid;

        $this->EventsStage = ClassRegistry::init('EventsStage');
        $this->Event = ClassRegistry::init('Event');

        $package_stage_arr = $this->decodeStageId($package_stageid);

        $this->Event->id = $package_stage_arr['events_id'];
        $package_event = $this->Event->read();

        $package_date = date("Y-m-d", strtotime($package_stage_arr['datetime']));

        $event1_datetime = $package_date . ' ' . $package_event['Event']['pack_1_time'];
        $event1_stageid = $this->encodeStageId(true, $package_event['Event']['pack_1_events_id'], $event1_datetime, '00');

        $event2_datetime = $package_date . ' ' . $package_event['Event']['pack_2_time'];
        $event2_stageid = $this->encodeStageId(true, $package_event['Event']['pack_2_events_id'], $event2_datetime, '00');

        $this->EventsStage->id = $event1_stageid;
        $event1 = $this->EventsStage->read();

        $this->EventsStage->id = $event2_stageid;
        $event2 = $this->EventsStage->read();

        $PaxA = $event1['EventsStage']['pax_total'];
        $PaxB = $event2['EventsStage']['pax_total'];

        if ($PaxA > $PaxB) {
            $Pax = $PaxA;
        } else {
            $Pax = $PaxB;
        }
        if ($Pax < 0) {
            $Pax = 0;
        }

        $this->EventsStage->id = $package_stageid;
        $this->EventsStage->recursive = -1;
        $package = $this->EventsStage->read();

        $package['EventsStage']['pax_adults'] = $Pax;
        $package['EventsStage']['pax_total'] = $Pax;

        //pr($package);

        $this->EventsStage->save($package);
    }

    //   -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- -=/=- -=|=- -=(X)=- -=|=- -=\=- 

    public function saveBookingDetails($item) {

        $this->Event = ClassRegistry::init('Event');
        $this->BookingsDetail = ClassRegistry::init('BookingsDetail');
        $this->Comment = ClassRegistry::init('Comment');
        $this->EventsStage = ClassRegistry::init('EventsStage');

        $this->Event->id = $item['event_id'];
        $Event = $this->Event->read();

        if ($item['adults'] == 0 && $item['students'] == 0 && $item['seniors'] == 0 && $item['children'] == 0 && $item['infants'] == 0) {
            return array(
                'result' => 'error',
                'message' => 'No Pax'
            );
        }


        $insertBookingsDetails['BookingsDetail'] = array(
            'bookings_id' => $item['bookings_id'],
			'events_id' => $item['event_id'],
			'events_datetimes' => $item['datetime'],
			'amount_local' => $item['subtotal_local'],
            'amount_converted' => $item['subtotal_converted'],
			'number_adults' => $item['adults'],
			'number_children' => $item['children'],
			'number_students' => $item['students'],
			'number_seniors' => $item['seniors'],
			'number_infants' => $item['infants'],
			'adults_price_charged' => $item['adults_price_charged'],
			'students_price_charged' => $item['students_price_charged'],
			'children_price_charged' => $item['children_price_charged'],
			'seniors_price_charged' => $item['seniors_price_charged'],
			'infants_price_charged' => $item['infants_price_charged'],
			'is_paid' => $item['is_paid'],
			'transaction_id' => $item['transaction_id'],
			'stage_id' => $item['stage_id'],
			'booking_date' => date("Y-m-d H:i:s"),
			'private_group' => ucfirst(strtolower($item['type'])),
            'exchange_rate' => $item['exchange_rate'],
            'charged_local_amount' => $item['charged_local_amount'],
            'charged_usd_amount' => $item['charged_usd_amount'],
            'charged_converted_amount' => $item['charged_amount'],
            'payment_transaction_number' => $item['transaction_id'],


        );


        if ($Event['Event']['is_package']) {
            // PACKAGES !! PACKAGES !! PACKAGES !! PACKAGES !! PACKAGES !! PACKAGES !! PACKAGES

            $package_date = date("Y-m-d", strtotime($item['datetime']));
            $insertBookingsDetails['BookingsDetail']['package_id'] = $item['event_id'];
            $insertBookingsDetails['BookingsDetail']['package_stage'] = $item['stage_id'];

            // Part 1 * Part 1 * Part 1 * Part 1 * 
            $event1_datetime = $package_date . ' ' . $Event['Event']['pack_1_time'];
            $event1_stageid = $this->encodeStageId(true, $Event['Event']['pack_1_events_id'], $event1_datetime, '00');
            $insertBookingsDetails['BookingsDetail']['events_id'] = $Event['Event']['pack_1_events_id'];
            $insertBookingsDetails['BookingsDetail']['events_datetimes'] = $event1_datetime;
            $insertBookingsDetails['BookingsDetail']['stage_id'] = $event1_stageid;
            //split the prices
            $insertBookingsDetails['BookingsDetail']['amount_local'] = ($item['amount_local'] * $Event['Event']['pack_1_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_usd_amount'] = ($item['charged_usd_amount'] * $Event['Event']['pack_1_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_local_amount'] = ($item['charged_local_amount'] * $Event['Event']['pack_1_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_converted_amount'] = ($item['charged_amount'] * $Event['Event']['pack_1_price_percent']) / 100;
            $this->BookingsDetail->create();
            $this->BookingsDetail->save($insertBookingsDetails);
            $lastId = $this->BookingsDetail->getLastInsertId();
            //add extra info for details transaction table
            $insertBookingsDetails['BookingsDetail']['subtotal_local'] = ($item['subtotal_local'] * $Event['Event']['pack_1_price_percent']) / 100;
            $item['bookings_details_id'] = $lastId;
            $this->_createBookingDetailsTransaction($insertBookingsDetails, $item);

            // Part 2 * Part 2 * Part 2 * Part 2 * 
            $event2_datetime = $package_date . ' ' . $Event['Event']['pack_2_time'];
            $event2_stageid = $this->encodeStageId(true, $Event['Event']['pack_2_events_id'], $event2_datetime, '00');
            $insertBookingsDetails['BookingsDetail']['events_id'] = $Event['Event']['pack_2_events_id'];
            $insertBookingsDetails['BookingsDetail']['events_datetimes'] = $event2_datetime;
            $insertBookingsDetails['BookingsDetail']['stage_id'] = $event2_stageid;
            //split the prices
            $insertBookingsDetails['BookingsDetail']['amount_local'] = ($item['amount_local'] * $Event['Event']['pack_2_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_usd_amount'] = ($item['charged_usd_amount'] * $Event['Event']['pack_2_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_local_amount'] = ($item['charged_local_amount'] * $Event['Event']['pack_2_price_percent']) / 100;
            $insertBookingsDetails['BookingsDetail']['charged_converted_amount'] = ($item['charged_amount'] * $Event['Event']['pack_2_price_percent']) / 100;
            $this->BookingsDetail->create();
            $this->BookingsDetail->save($insertBookingsDetails);

            $lastId = $this->BookingsDetail->getLastInsertId();
            //add extra info for details transaction table
            $insertBookingsDetails['BookingsDetail']['subtotal_local'] = ($item['subtotal_local'] * $Event['Event']['pack_2_price_percent']) / 100;
            $item['bookings_details_id'] = $lastId;
            $this->_createBookingDetailsTransaction($insertBookingsDetails, $item);

            $this->updatePax($event1_stageid);
            $this->updatePax($event2_stageid);
        } else {

            // NOT A PACKAGE 
	        $this->BookingsDetail->create();
            $this->BookingsDetail->save($insertBookingsDetails);
            $lastId = $this->BookingsDetail->getLastInsertId();
            $item['bookings_details_id'] = $lastId;
            $this->_createBookingDetailsTransaction($insertBookingsDetails, $item);
            $this->updatePax($item['stage_id']);
        }

        if ($item['comment'] <> '') {
            $commentInsert['Comment'] = array(
                'comment_detail' => $item['comment'],
                'comment_time' => 'NOW()',
                'booking_id' => $item['bookings_id'],
                'bookings_details_id' => $lastId,
                'notes_type' => 'Event Specific Note',
            );
            $this->Comment->save($commentInsert);
        }
        if (!$item['type']) {
            $this->EventsStage->id = $item['stage_id'];
            $events_stage_id = $this->EventsStage->read();
            if (!$events_stage_id) {
                $insert_events_stage['EventsStage'] = array(
                    'id' => $item['stage_id'],
                    'events_id' => $item['event_id'],
                    'datetime' => $item['datetime'],
                    'pax_max' => 0,
                    'group' => false,
                    'schedule' => null,
                    'private_booked_flag' => false
                );
                $this->EventsStage->save($insert_events_stage);
            }
        }

        return array('result' => 'success');
    }

    private function _createBookingDetailsTransaction($details, $item){
        $details = $details['BookingsDetail'];
        $details['subtotal_local'] = isset($details['subtotal_local']) ? $details['subtotal_local'] : $item['subtotal_local'];

//        debug($item);
//        debug($details);
        //log everything in the Bookings_details_transactions table
        $this->BookingDetailsTransaction = ClassRegistry::init('BookingsDetailsTransaction');

        $this->BookingDetailsTransaction->create();
        $this->BookingDetailsTransaction->save(array(
            'bookings_details_id' => $item['bookings_details_id'],
            'trans_number' => $item['transaction_id'],
            'trans_datetime' => date('Y-m-d H:i:s'),
            'local_amount' => $details['charged_local_amount'],
            'local_discount' => $details['subtotal_local'] - $details['charged_local_amount'],
            'local_usd_rate' => $item['local_usd_rate'],
            'usd_amount' => $details['charged_usd_amount'],
            'exchange_from' => $item['exchange_from'],
            'exchange_to' => $item['exchange_to'],
            'exchange_rate' => $item['exchange_rate'],
            'exchange_amount' => $details['charged_converted_amount']
        ));



    }

}

