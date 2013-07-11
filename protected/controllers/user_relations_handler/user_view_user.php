<?php

///Pomocny subor k situacii ked rola sportovca prehliada profil sportovca

    $activity = Activity::model()->findByPk($user->id_primary_activity);
    $address = Address::model()->findByPk($user->id_address);
    $privacy = Privacy::model()->findByPk($user->id_privacy);
    $isFriend = false;
    $requestSent = false;
    $requestRecieved = false;
    $sharedworkouts = null;
    $relation = new Sparring;

    if($address != null){
        $country = Country::model()->findByPk($address->id_country);
    }
    else{
        $country = null;
    }
    $fitness = UserFitness::model()->findByPk($user->id_user_fitness);

    //Definovanie vztahu medzi pouzivatelmi
    if(!Yii::app()->user->isGuest){

        // $wantedRelation definuje ziadost ktorú odoslal prihlaseny pouzivatel prezeranemu
        $wantedRelation = Sparring::model()->find('id_user="'.Yii::app()->user->id.'" and id_partner="'.$user->id.'"');

        // $relation definuje ziadost ktoru poslal prezerany pouzivatel prihlasenemu
        $relation = Sparring::model()->find('id_user="'.$user->id.'" and id_partner="'.Yii::app()->user->id.'"');

        if($relation != null && $wantedRelation!=null){ //Ak je relacia ekvivalencia - su priatelia
            if($relation->status == 2){ // pre istotu kontrola ci je spravny status
                $isFriend = true; //Na stranke bude zobrazena flash informacia ze pouzivatelia su priatelia 
                $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility !=3 order by date DESC limit 8');
            }
        }
        else if($relation != null){ //tato relacia definuje prijatu poziadavku
            if($relation->status == 1){ // pre istotu kontrola ci je spravny status
                $requestRecieved = true;
                $isFriend = false; // nasledne sa na stranke zobrazi ziadost o potvrdenie priatelstva
            }
        }
        else if($wantedRelation != null){ //tato relacia definuje odoslanu poziadavku
            $isFriend = false;
            $requestSent = true; // nasledne sa na stranke zobrazi flash informacia o tom ze poziadavka bolo odoslana
        }
    }
    else{
        $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility = 1 order by date DESC limit 8');
    }

    $summary = null;
    if($privacy->basic_tr_data == 1 || ($privacy->basic_tr_data == 2 && $isFriend)){
        $summary = $this->getSummary($user->id);
    }

    $activity_array = Activity::model()->findAll(array('index'=>'id'));
?>

                    
