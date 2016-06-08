<?php
///Pomocny subor k situacii ked rola sportovca prehliada profil trenera

    $activity = Activity::model()->findByPk($user->id_primary_activity);
    $address = Address::model()->findByPk($user->id_address);
    $privacy = Privacy::model()->findByPk($user->id_privacy);

    if($address != null){
        $country = Country::model()->findByPk($address->id_country);
    }
    else{
        $country = null;
    }
    $fitness = UserFitness::model()->findByPk($user->id_user_fitness);
?>
