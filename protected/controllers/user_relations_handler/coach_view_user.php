<?php
///Pomocny subor k situacii ked rola trenera prehliada profil sportovca

    $activity = Activity::model()->findByPk($user->id_primary_activity);
    $address = Address::model()->findByPk($user->id_address); 
    $privacy = Privacy::model()->findByPk($user->id_privacy);
    
    if($address != null){
        $country = Country::model()->findByPk($address->id_country);
    }
    else{
        $country = null;
    }
    
    $summary = null;
    
    if((CoachCooperation::model()->find('id_coach = '.Yii::app()->user->id.
            ' and id_athlete = '.$user->id.' and status = 1')) == null){ //Spolupraca trenera a sportovca 
        
        $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility = 1 order by date DESC limit 8');
        $privacy = Privacy::model()->findByPk($user->id_privacy);
        $coach_cooperation = FALSE;
        
        $isFriend = FALSE;
        
        if($privacy->basic_tr_data == 1)
            $summary = $this->getSummary($user->id);
    }
    else{ //Trener a sportovec nie su vo vztahu
        $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' order by date DESC limit 8');    
        $summary = $this->getSummary($user->id);
        $coach_cooperation = TRUE;
        
        $isFriend = TRUE;
        
        $privacy = new Privacy;
        foreach ($privacy as $value){
            $value = 1;
        }
    }
        
    $fitness = UserFitness::model()->findByPk($user->id_user_fitness);
    $activity_array = Activity::model()->findAll(array('index'=>'id'));
?>
