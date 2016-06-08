<?php

///Controller kotry obsluhuje uvodnu stranku Dashboard s rychlym prehladom udajov

class DashboardController extends Controller
{
	/** @var string '//layouts/content' Rozlozenie stranky po volani metody render */
	public $layout='//layouts/content';

        ///Metoda ktora vrati filtre pristupu k jednolivym akciam
	/** @return array Filtre akcii */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

        ///Metoda vrati pristupove prava k jednolivym akciam ovladaca
	/** @return array Pravidla pristupu */
	public function accessRules() {
		return array(
			array('allow',
				'actions'=>array(
                                        'index','loadnotices','loadnews', 'loadstats'
                                    ),
				'roles'=>array('athlete','coach'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        ///Metoda ziska vsetky potrebne data a zobrazi stranku Dashboard
        /** Dashboard je uvodna stranka po prihlaseni uzivatela, ponuka rychly prehlad aktivit, treningov a udalosti*/
        public function actionIndex() {
            
            $activity_array = Activity::model()->findAll(array('index'=>'id'));
            
            $userid = Yii::app()->user->id;
            $date = date('Y-m-d',strtotime('+1 day', strtotime(date('Y-m-d'))));
            
            if(Yii::app()->user->roles == 'athlete') {
                $sqlStatement = 'SELECT te.*, u.fullname, u.profile_picture, u.gender FROM `training_entry` te 
				 LEFT JOIN `user` u on te.id_user = u.id 
				 WHERE 
					(te.id_visibility != 3 AND te.id_user in (select id_partner from sparring where id_user='.Yii::app()->user->id.' and status = 2))
				 OR
					id_user = '.Yii::app()->user->id.'
				 ORDER BY te.date DESC limit 8';
                $allSharedWorkouts = $this->executeStringQuery($sqlStatement);

                //Nacitanie treningoveho planu pre nasledujuce 3 dni
                $plan = TrainingPlan::model()->findAll("
                    id_user=".$userid." and `date` between '".$date."' and '".
                        date('Y-m-d',strtotime("+2 days",strtotime($date)))."' order by `date` DESC");

                $entries = TrainingEntry::model()->findAll('id_user='.$userid.' order by `date` DESC limit 3');

                $this->render('index',
                    array(
                        'allSharedWorkouts'=>$allSharedWorkouts,
                        'activity_array'=>$activity_array,
                        'plan'=>$plan,
                        'entries'=>  array_reverse($entries),
                        'first_date'=>$date,
                    ));
            }
            elseif (Yii::app()->user->roles == 'coach') {
                $sqlStatement = 'select te.*, u.fullname, u.profile_picture, u.gender from `training_entry` te 
                left join `user` u on te.id_user = u.id where 
                        te.id_user in (select id_athlete from coach_cooperation where id_coach='.Yii::app()->user->id.' and status = 1) order by te.date DESC limit 8
                 ';
                $allSharedWorkouts = $this->executeStringQuery($sqlStatement);
                
                $athletes = User::model()->findAll('id in (select id_athlete from coach_cooperation where id_coach='.Yii::app()->user->id.' and status = 1)');
                
                $this->render('indexcoach',
                        array(
                            'allSharedWorkouts'=>$allSharedWorkouts,
                            'activity_array'=>$activity_array,
                            'athletes'=>$athletes
                        ));
            }
        }
        
        ///Metoda ktora nacita upozornenia pre konkretneho pouzivatela prostrednictvom AJAX requestu
        public function actionLoadnotices(){
            
            $this->translateDays();
            $userid = Yii::app()->user->id;
            $sqlStatement1 = 
                    'select c.*, t.id as id_training, u.gender, u.fullname, u.username, u.profile_picture from comment c 
                    inner join `user` u on c.id_user = u.id
                    inner join training_entry t on c.id_trainingentry = t.id 
                    where t.id_user = '.$userid.' and c.id_user != '.$userid.' and c.seen = 0 order by c.date DESC'; 
            //zobrazenie vsetkych komentarov ktore patri k mojim treningom nepisal som ich ja a nevidel som ich
            
            $ns_comments = $this->executeStringQuery($sqlStatement1); //not seen comments
            
            $sqlStatement2 = 
                    'select u.gender, u.fullname, u.username, u.profile_picture, s.request_date as `date` from sparring s 
                    inner join `user` u on s.id_user = u.id where s.id_partner = '.$userid.' and s.status = 1 order by s.request_date DESC;'; 
            //zobrazenie ziadosti o priatelstvo
            
            $sp_request = $this->executeStringQuery($sqlStatement2); //sparring partner pending requests
            
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
            echo '<response>';
                if($ns_comments != null){
                    foreach ($ns_comments as $comment){
                        echo '<comment>'; 
                            echo '<link>';
                                echo Yii::app()->params->fullPath.Yii::app()->language.'/workout/view/'.$comment->id_training;
                            echo '</link>';
                            echo '<fullname>';
                                echo $comment->fullname;
                            echo '</fullname>';
                            echo '<picture>';
                                if($comment->profile_picture != '' && ($pic = $this->getPicture($comment->profile_picture, 'profile-picture'))!= null){
                                    echo $pic;
                                }
                                else{
                                    echo Yii::app()->request->baseUrl.'/images/photo-default-'.$comment->gender.'.png';
                                }
                            echo '</picture>';
                            echo '<date>';
                                echo $this->days[date('w',strtotime($comment->date))].', '.
                                        date('d. m.',strtotime($comment->date));
                            echo '</date>';
                        echo '</comment>'; 
                    }
                }
                else{
                    echo '<commentsstatus>';
                        echo 'notfound';
                    echo '</commentsstatus>';
                }
                if($sp_request != null){
                    foreach ($sp_request as $request){
                        echo '<request>'; 
                            echo '<link>';
                                echo Yii::app()->params->fullPath.Yii::app()->language.'/user/view/'.$request->username;
                            echo '</link>';
                            echo '<fullname>';
                                echo $request->fullname;
                            echo '</fullname>';
                            echo '<picture>';
                                if($request->profile_picture != '' && ($pic = $this->getPicture($request->profile_picture, 'profile-picture'))!= null){
                                    echo $pic;
                                }
                                else{
                                    echo Yii::app()->request->baseUrl.'/images/photo-default-'.$request->gender.'.png';
                                }
                            echo '</picture>';
                            echo '<date>';
                                echo $this->days[date('w',strtotime($request->date))].', '.
                                        date('d. m.',strtotime($request->date));
                            echo '</date>';
                        echo '</request>'; 
                    }
                }
                else{
                    echo '<requestsstatus>';
                        echo 'notfound';
                    echo '</requestsstatus>';
                }
                echo '<commentecho>';
                    echo Yii::t('dashboard', 'Posted an opinion about your training');
                echo '</commentecho>';
                echo '<requestecho>';
                    echo Yii::t('dashboard', 'Wants to be your sparring partner');
                echo '</requestecho>';
                
            echo '</response>';
        }
        
        ///Metoda ktora vytvori divko s udajmi zo zdielanych treningovych jednotiek
        /**
         * @var $allSharedWorkouts Array of TrainingEntry
         * @var $activity_array Array of Activity
         */
        public function createLargeSharedEntries($allSharedWorkouts, $activity_array){
            foreach ($allSharedWorkouts as $entry):  
                $i = 0;

                echo '<a class="shared-entry" id="'.$entry->id.'" 
                    href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/workout/view/'.$entry->id.'">';

                if($entry->profile_picture != '' && ($pic = $this->getPicture($entry->profile_picture, 'profile-picture'))!= null){
                    echo '<img class="shared-entry-apic" src="'.$pic.'" height="50"/>';
                }
                else{
                    echo '<img class="shared-entry-apic" src="'.Yii::app()->request->baseUrl.'/images/photo-default-'.$entry->gender.'.png" height="50"/>';
                }

                echo '<div class="shared-entry-author">';
                echo '<span class="bname">'.$entry->fullname.'</span> '.Yii::t('dashboard','has completed training');
                echo '</div>';

                if(isset($activity_array[$entry->id_activity])){
                    echo '<div class="single-day-activity" id="activity-'.$entry->id_activity.'">'.
                            Yii::t('activity',$activity_array[$entry->id_activity]->name).'</div>';
                }

                echo '<div class="single-day-duration">'.date ('H:i',strtotime($entry->duration)).'</div>';
                echo '<div style="clear:both; margin-bottom:5px;"></div>';

                echo '<div class=she-data-wrapper>';
                    echo '<div class="shared-entry-description">'.$this->substr_unicode($entry->description,0,70).'...</div>';
                if($entry->distance != 0){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Distance').'</span><br/>'.
                            '<span class="shared-entry-data">'.number_format($entry->distance, 1, '.', '').'</span> km</div>';
                    $i++;
                }
                if($entry->avg_speed != 0){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Average Speed').'</span><br/>'.
                            '<span class="shared-entry-data">'.number_format($entry->avg_speed, 1, '.', '').'</span> km/h</div>';
                    $i++;
                }
                elseif ($entry->avg_pace != '00:00:00' && $entry->avg_pace != null){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Average Pace').'</span><br/>'.
                            '<span class="shared-entry-data">'.date ('i:s',strtotime($entry->avg_pace)).'</span> min/km</div>';
                    $i++;
                }
                if($entry->avg_hr != null || $entry->max_hr != null){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Heart rate').'</span><br/>'.'<span class="shared-entry-data">';
                    echo $entry->avg_hr? $entry->avg_hr : '--';
                    echo '/';
                    echo $entry->max_hr? $entry->max_hr : '--';
                    echo '</span> '.Yii::t('diary','bpm').'</div>';
                    $i++;
                }

                if($entry->ascent != null){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Ascent').'</span><br/>'.
                            '<span class="shared-entry-data">'.$entry->ascent.'</span> m</div>';
                    $i++;
                }
                if($i<=3 && $entry->avg_watts != null){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Average Watts').'</span><br/>'.
                            '<span class="shared-entry-data">'.$entry->avg_watts.'</span> W</div>';
                    $i++;
                }

                echo '</div>';
                echo '</a>';
            endforeach;
        }
        
        ///Metoda nacita a vrati zdielane treningove jednotky prostrednictvom AJAX requestu
        public function actionLoadnews(){
            if(isset($_GET['offset'])){
                
                if(Yii::app()->user->roles == 'athlete'){
                $sqlStatement = "SELECT te.*, u.fullname, u.profile_picture, u.gender FROM `training_entry` te 
				 LEFT JOIN `user` u on te.id_user = u.id 
				 WHERE 
					(te.id_visibility != 3 AND te.id_user in (select id_partner from sparring where id_user=".Yii::app()->user->id." and status = 2))
				 OR
					id_user = ".Yii::app()->user->id."
				 ORDER BY te.date DESC limit {$_GET['offset']},8";
                }  elseif(Yii::app()->user->roles == 'coach') {
                    $sqlStatement = 'select te.*, u.fullname, u.profile_picture, u.gender from `training_entry` te 
                        left join `user` u on te.id_user = u.id where 
                                te.id_user in (select id_athlete from coach_cooperation where id_coach='.Yii::app()->user->id.' and status = 1) order by te.date DESC limit 8 offset '.$_GET['offset'];
                }
                $allSharedWorkouts = $this->executeStringQuery($sqlStatement);
                $activity_array = Activity::model()->findAll(array('index'=>'id'));
            
                $this->createLargeSharedEntries($allSharedWorkouts, $activity_array);
            }
            
        }
        ///Metoda nacita a vrati statistiky pre vybrane obdobie prostrednictvom AJAX requestu
        public function actionLoadstats(){
            if(isset($_GET['datefrom']) && isset($_GET['dateto'])){
                if($_GET['datefrom']=='')
                    $datefrom = date('Y-m-d');
                else
                    $datefrom = $_GET['datefrom'];
                if($_GET['dateto']=='')
                    $dateto = date('Y-m-d');
                else
                    $dateto = $_GET['dateto'];
                
                $activity_array = Activity::model()->findAll(array('index'=>'id'));
                
                $sqlStatement = 'SELECT id_activity, 
                                    SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) as tottime,
                                    count(id_activity) as totworkouts, 
                                    sum(distance) as distance,
                                    if(sum(avg_speed) = 0, 0, sum(avg_speed)/count(id_activity)) as avg_speed 
                                    FROM training_entry where id_user = '.Yii::app()->user->id.' and `date` between "'.$datefrom.'" and "'.$dateto.'"
                                    and duration <> "00:00:00" group by id_activity;';
                $result = $this->executeStringQuery($sqlStatement);
                
                $sqlStatement = 'SELECT 
                                    SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) as tottime,
                                    count(*) as totworkouts, 
                                    sum(distance) as distance 
                                    FROM training_entry where id_user = '.Yii::app()->user->id.' and `date` between "'.$datefrom.'" and "'.$dateto.'"
                                    and duration <> "00:00:00"';
                $total = $this->executeStringQuery($sqlStatement);
                
                echo '<div id="quick-stats-date-info">'.Yii::t('dashboard','Summary for selection starting from ').date('d. m. Y',  strtotime($datefrom)).
                        Yii::t('dashboard',' to ').date('d. m. Y',  strtotime($dateto)).'</div>';
                
                if($result != null){
                    echo '<table id="quick-stats" class="plain-style-table">';

                    echo '<thead>';
                        echo '<tr><th></th>';
                            echo '<th>'.Yii::t('diary','Hours').'</th>';
                            echo '<th>'.Yii::t('diary','Distance').'</th>';
                            echo '<th>'.Yii::t('diary','Average Speed').'</th>';
                            echo '<th>'.Yii::t('diary','Workouts').'</th>';
                        echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                        foreach($total as $tot){
                            echo '<tr>';
                                echo '<th style="color: #444">'.Yii::t('diary','Total').'</th>';
                                $time = explode(':',$tot->tottime);
                                echo '<td><span class="number">'.$time[0].'</span>:'.$time[1].'</td>';
                                
                                $distance = explode('.',$tot->distance);
                                echo '<td><span class="number">'.$distance[0].'</span> km</td>';
                                
                                echo '<td>--</td>';
                                
                                echo '<td><span class="number">'.$tot->totworkouts.'</span></td>';
                            echo '</tr>';
                        }

                        foreach($result as $row){
                            echo '<tr>';
                                echo '<th class="red">'.Yii::t('activity',$activity_array[$row->id_activity]->name).'</th>';
                                $time = explode(':',$row->tottime);
                                echo '<td><span class="number">'.$time[0].'</span>:'.$time[1].'</td>';
                                
                                $distance = explode('.',$row->distance);
                                echo '<td><span class="number">'.$distance[0].'</span> km</td>';
                                
                                $speed = explode('.',number_format($row->avg_speed, 2, '.', ''));
                                echo '<td><span class="number">'.$speed[0].'</span>.'.$speed[1].' km/h</td>';
                                
                                echo '<td><span class="number">'.$row->totworkouts.'</span></td>';
                            echo '</tr>';
                        }
                    echo '</tbody>';
                    echo '</table>';
                }
                else{
                    echo '<div id="quick-stats-not-found">'.Yii::t('dashboard','No stats for choosen period').'</div>';
                }
            }
        }
}