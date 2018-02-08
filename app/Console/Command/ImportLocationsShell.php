<?php

class ImportLocationsShell extends AppShell{

    public function main(){
        $this->out('running');
        $fileLocation = $this->args[0];
        $idColumn = $this->args[1];
        $addressColumn = $this->args[2];

        $this->Event = ClassRegistry::init('Event');

        //open the csv file and go through the rows

        if (($handle = fopen($fileLocation, "r")) !== FALSE) {
            //skip the first row
            $row = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $eventId = $row[$idColumn];

                //check that event exists
                if(!$this->Event->hasAny(array('id' => $eventId))){
                    continue;
                }

                $address = $row[$addressColumn];
                $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false';
                $this->out("url: ($eventId)  " . $url);
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $json = json_decode(curl_exec($ch), true);

                //convert to a normal format
                $location = array();
                $results = isset($json['results'][0]) ? $json['results'][0] : $json['results'];
                if(isset($results['address_components'])){


                    foreach($results['address_components'] as $item){
                        $locationData = array(
                            'longName' => isset($item['long_name'])? $item['long_name'] : '',
                            'shortName' => isset($item['short_name'])? $item['short_name'] : ''
                        );
                        foreach($item['types'] as $type){
                            $location[$type] = $locationData;
                        }
                    }

                    //$this->out($address);
                    //$this->out(print_r($location,1));


                    //format data

                    //$this->out($url);
                    //$this->out(print_r($json,1));
                    $save = array(
                        'id' => $eventId,
                        'street_number' => isset($location['street_number']['longName'])? $location['street_number']['longName'] : null,
                        'street' => isset($location['route']['longName']) ? $location['route']['longName'] : null,
                        'city' => isset($location['administrative_area_level_3']['longName']) ? $location['administrative_area_level_3']['longName'] : null,
                        'province_code' => isset($location['administrative_area_level_2']['shortName']) ? $location['administrative_area_level_2']['shortName'] : null,
                        'country' => isset($location['political']['longName']) ? $location['political']['longName'] : null,
                        'zip_code' => isset($location['postal_code']['longName']) ? $location['postal_code']['longName'] . '' : null,
                        'latitude' => isset($results['geometry']['location']['lat']) ? $results['geometry']['location']['lat'] : null,
                        'longitude' => isset($results['geometry']['location']['lng']) ? $results['geometry']['location']['lng'] : null,
                    );

                    $saved = $this->Event->save($save);

//                $this->out(print_r($save,1));
                    //$this->out(print_r($saved,1));
                    //break;
                }else{
                    $this->out('Could not get address for event id: ' . $eventId);
                }
                sleep(1);


            }
            fclose($handle);
        }


    }

}