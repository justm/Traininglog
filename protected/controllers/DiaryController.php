<?php

///Controller pre akcie v sekcii treningoveho dennika

class DiaryController extends Controller
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
                                        'index','delete','getsingleentry','addweek'
                                    ),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        public function actionIndex($id=''){
            
            $allowed = true;
            $this->translateDays();
            
            //Nastavenie datumu na pondelok aktualneho tyzdna
            $date = $id;
            if($date == ''){
                $date =  date('Y-m-d',strtotime("Monday this week"));
                if(date('w',strtotime(date('Y-m-d')))==0){ //Ak je dnesny datum nedela, mensi hack pretoze default je nedela ako prvy den
                    $date =  date('Y-m-d',strtotime("Monday last week"));
                }
            }
            if(date('w',strtotime($date))==0){ //Ak je vybrany datum nedela, znovu hack 
                $date =  date('Y-m-d',strtotime("Monday last week",strtotime($date)));
            }
            if(isset($_GET['userid'])){
                if(($coach_cooperation = CoachCooperation::model()->find('id_coach = '.Yii::app()->user->id.
                            ' and id_athlete = '.$_GET['userid'].' and status = 1'))!=null){
                    $userid = $_GET['userid'];
                    Yii::app()->user->setState('operating_id',$userid);
                }
                else{
                    $this->render('notallowed');
                    $allowed = false;
                }
            }else{
                $userid = Yii::app()->user->id;
            }
            if($allowed){
                $singleEntry = new TrainingEntry;
                $singleEntry->duration = '00:00';

                $sqlStatement = 'select h.id, h.id_user, coalesce(h.name,d.name) as `name`,
                    `min`, `max`, `id_user`, `id_default` from hr_zone h left join default_hr_zone d 
                    on h.id_default=d.id where id_user ='.$userid.' order by h.min';
                $hr_zones = $this->executeStringQuery($sqlStatement);

                for($j=0; $j<count($hr_zones); $j++){
                    $zone_time[] = new ZoneTime;
                }

                $labelToSave = new Label();
                if(isset($_POST['Label']['editcontrol'])){
                    if($_POST['Label']['id'] != ''){ //uprava existujuceho oznacenia

                        if(($labelToSave = Label::model()->findByPk($_POST['Label']['id']))!=null){
                             $labelToSave->id_default = null;
                             $labelToSave->attributes = $_POST['Label'];
                        }
                    }
                    else{
                        $labelToSave->attributes = $_POST['Label'];
                        $labelToSave->id_user = $userid;
                    }
                    //kontrola povinnych udajov a ulozenie
                    if($labelToSave->name != null){
                        $labelToSave->color==null? $labelToSave->color='EEEEEE':'';
                        if($labelToSave->save()){
                            Yii::app()->user->setFlash('label-created',Yii::t('plan','Label successfully added'));
                        }
                    }
                    else{
                        Yii::app()->user->setFlash('label-notcreated',Yii::t('plan','Sorry, every label must have a name.'));
                    }

                }
                elseif(isset($_POST['TrainingEntry'])){
                    if ($_POST['TrainingEntry']['id'] == '' || $_POST['TrainingEntry']['id'] == 'null'){
                        $singleEntry = new TrainingEntry;
                    }
                    else{
                        $singleEntry = TrainingEntry::model()->findByPk($_POST['TrainingEntry']['id']);
                    }
                    $singleEntry->attributes = $_POST['TrainingEntry'];

                    $singleEntry->id_label = $_POST['TrainingPlan']['id_label'];
                    $singleEntry->id_user = $userid;

                    $temp_hours = $_POST['TrainingEntry']['hours'] == '' ? '00' : $_POST['TrainingEntry']['hours'];
                    $temp_minutes = $_POST['TrainingEntry']['minutes'] == '' ? '00' : $_POST['TrainingEntry']['minutes'];
                    $singleEntry->duration = $temp_hours.':'.$temp_minutes.':00';


                    $singleEntry->avg_pace = '00:'.$_POST['TrainingEntry']['avg_pace'];

                    $singleEntry->date = date('Y-m-d',  strtotime($_POST['TrainingEntry']['date']));


                    $transaction = Yii::app()->db->beginTransaction();
                    try{
                        if($singleEntry->validate()){
                            if($singleEntry->save()){
                                $i = 0;
                                foreach ($_POST['ZoneTime'] as $id_zone=>$time){
                                    $zone_time[$i]->p_time = $time;
                                    $zone_time[$i]->id_hrzone = $id_zone;
                                    $zone_time[$i]->id_trainingentry = $singleEntry->id;

                                    if($zone_time[$i]->p_time != '')
                                        $zone_time[$i]->save();
                                    $i++;
                                }
                                $transaction->commit();
                                Yii::app()->user->setFlash('entry-saved',Yii::t('diary','Your entry has been succesfully saved'));
                            }
                        }
                    }catch (Exception $e){
                        $transaction->rollback();                                
                        Yii::app()->user->setFlash('entry-not-saved','Sorry, there might be some problems during database session. Please try again.');       
                    }
                     $singleEntry->avg_pace = $_POST['TrainingEntry']['avg_pace']; //Tempo ma format mm:ss, tymto odstranime hodiny na zaciatku retazca, ktore sme pridali vyssie
                }

                //Kompletene nacitanie aktivity pre nasledujuce 2 tyzdne z treningoveho diara
                $diary = TrainingEntry::model()->findAll('
                    t.id_user = '.$userid.' and t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                    AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -8, DAYOFWEEK("'.$date.'")-16) DAY 
                    order BY t.date, t.start_time');

                //Sumare pre konkretne dva tyzdne
                $summary[0] = $this->getWeeklySummary($userid, $date);
                $summary[1] = $this->getWeeklySummary($userid, Date('Y-m-d', strtotime('+7 days', strtotime($date))));
                //
                //Cely zoznam Aktivit
                if(Yii::app()->language == 'sk')
                    $activity = CHtml::listData(Activity::model()->findAll(), 'id', 'name_sk');
                else
                    $activity = CHtml::listData(Activity::model()->findAll(), 'id', 'name');
                $activity_array = Activity::model()->findAll(array('index'=>'id'));

                //Oznacenia treningov konretneho pouzivatela
                $sqlStatement = 'select l.id, l.id_user, coalesce(l.name,d.name) as `name`, 
                    coalesce(l.color,d.color) as `color`, l.id_default from label l 
                    left join default_label d on l.id_default=d.id where l.id_user='.$userid.';';
                $result = $this->executeStringQuery($sqlStatement);

                $labels = array();
                foreach ($result as $label){ //indexacia do pola podla ID-cka
                    $labels[$label->id] = $label; 
                }
                $newlabel = new Label();

                $this->render('index',
                        array(
                            'activity'=>$activity,
                            'activity_array'=>$activity_array,
                            'request_date'=>$date,
                            'singleEntry'=>$singleEntry,
                            'hr_zones'=>$hr_zones, 
                            'zone_time'=>$zone_time,
                            'diary'=>$diary,
                            'summary'=>$summary,
                            'newlabel'=>$newlabel,
                            'labels'=>$labels
                ));
            }
        }
        
        ///Metoda vymaze zaznam za tabulky TrainingEntry
        public function actionDelete(){
            $bool = false;
            if(isset($_POST['id'])){
                $model = TrainingEntry::model()->findByPk($_POST['id']);
                $zone_time = ZoneTime::model()->findAll('id_trainingentry = '.$model->id);
                foreach ($zone_time as $zoneentry){
                    $zoneentry->delete();
                }
                if($model->delete()){
                     $bool = true;
                     Yii::app()->user->setFlash('training-entry-deleted',Yii::t('diary','Entry was deleted'));
                }
            }
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';               
                echo '<response>';
                    echo $bool;
                echo '</response>';
        }
        
        ///Metoda ktora ziska z tabulky training_entry summar pre jeden tyzden podla datumu
        /**
         * @param string $userid Id pouzivatela ktoremu trenigovy plan patri
         * @param string $date Datum od ktoreho sa zacne hladat
         */
        private function getWeeklySummary($userid, $date){
                 
             $sqlStatement = 'SELECT id_activity, count(id_activity) as totworkouts, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) as tottime
                FROM training_entry t where t.id_user = '.$userid.' and 
                t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -1, DAYOFWEEK("'.$date.'")-9) DAY 
                and duration <> "00:00:00" group by id_activity';
            return $this->executeStringQuery($sqlStatement);
                       
        }
        
        ///Metoda nacita z tabulky TrainingEntry jeden zaznam pre AJAX request
        public function actionGetsingleentry(){
            if(isset($_GET["id"])){
                $single_entry = TrainingEntry::model()->findByPk($_GET["id"]);
                if($single_entry != null){
                    header('Content-Type: text/xml');
                    echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';               
                    echo '<response>';
                        echo $this->parseEntry($single_entry);
                    echo '</response>';
                }
            }
        }
        
        ///Metoda ktora vrati cast XML suboru pre jeden den v treningovom plane
        /**
         * @param CActiveRecord $single_entry jeden den z treningoveho planu
         * @return string
         */
        private function parseEntry($single_entry){
            $this->translateDays();
            
            $xml_part = '';
            $xml_part .= '<day>';
                $xml_part .= '<id>';
                    $xml_part .= $single_entry->id;
                $xml_part .= '</id>';
                $xml_part .= '<id_activity>';
                    if($single_entry->id_activity==''){
                        $xml_part .= "null";
                    }
                    else{
                        $xml_part .= $single_entry->id_activity;
                    }
                $xml_part .= '</id_activity>';
                
                $xml_part .= '<date>';
                    $xml_part .= $this->days[date('w',strtotime($single_entry->date))];
                    $xml_part .= ' '.date('d. m. Y',strtotime($single_entry->date));
                $xml_part .= '</date>';
                
                $xml_part .= '<mysqldate>';
                    $xml_part .= date('Y-m-d',strtotime($single_entry->date));
                $xml_part .= '</mysqldate>';
                
                $xml_part .= '<duration>';
                    $xml_part .= date('H:i',strtotime($single_entry->duration)).' ';
                $xml_part .= '</duration>';
                
                $xml_part .= '<feelings>';
                    $xml_part .= $single_entry->feelings;
                $xml_part .= '</feelings>';
                
                $xml_part .= '<min_hr>';
                    if($single_entry->min_hr == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->min_hr;
                $xml_part .= '</min_hr>';
                
                $xml_part .= '<avg_hr>';
                    if($single_entry->avg_hr == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->avg_hr;
                $xml_part .= '</avg_hr>';
                
                $xml_part .= '<max_hr>';
                    if($single_entry->max_hr == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->max_hr;
                $xml_part .= '</max_hr>';
                
                $xml_part .= '<distance>';
                    if($single_entry->distance == null || $single_entry->distance == 0)
                        $xml_part .= 'null';
                    else
                        $xml_part .= number_format($single_entry->distance, 1, '.', '');
                $xml_part .= '</distance>';
                
                $xml_part .= '<avg_speed>';
                    if($single_entry->avg_speed == null || $single_entry->avg_speed == 0)
                        $xml_part .= 'null';
                    else
                        $xml_part .= number_format($single_entry->avg_speed, 1, '.', '');
                $xml_part .= '</avg_speed>';
                
                $xml_part .= '<avg_pace>';
                    if($single_entry->avg_pace == null || $single_entry->avg_pace == '00:00:00')
                        $xml_part .= 'null';
                    else
                        $xml_part .= date ('i:s',strtotime($single_entry->avg_pace));
                $xml_part .= '</avg_pace>';
                
                $xml_part .= '<ascent>';
                    if($single_entry->ascent == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->ascent;
                $xml_part .= '</ascent>';
                
                $xml_part .= '<max_altitude>';
                    if($single_entry->max_altitude == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->max_altitude;
                $xml_part .= '</max_altitude>';
                
                $xml_part .= '<avg_watts>';
                    if($single_entry->avg_watts == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->avg_watts;
                $xml_part .= '</avg_watts>';
                
                $xml_part .= '<max_watts>';
                    if($single_entry->max_watts == null)
                        $xml_part .= 'null';
                    else
                        $xml_part .= $single_entry->max_watts;
                $xml_part .= '</max_watts>';
                
                $xml_part .= '<description>';
                    $xml_part .= $single_entry->description.' ';
                $xml_part .= '</description>';
                
                $xml_part .= '<id_visibility>';
                    $xml_part .= $single_entry->id_visibility;
                $xml_part .= '</id_visibility>';
                $xml_part .= '<link>';
                    $xml_part .= Yii::app()->params->homePath.'/'.Yii::app()->language.'/workout/view/'.$single_entry->id;
                $xml_part .= '</link>';
            $xml_part .= '</day>';
            return $xml_part;
        }
        
        ///Metoda ktora prostrednictvom AJAX requestu vrati HTML snippet s kompletnym tyzdnom podla zadaneho datumu
        public function actionAddweek(){
            
            $this->translateDays();

            if(isset($_GET['date']) && isset($_GET['offset'])){
                $date = date('Y-m-d',strtotime('+'.$_GET['offset'].' days',strtotime($_GET['date'])));
                
                if(Yii::app()->user->roles == 'coach'){
                    
                    if((CoachCooperation::model()->find('id_coach = '.Yii::app()->user->id.
                                ' and id_athlete = '.Yii::app()->user->operating_id.' and status = 1'))!=null){
                    
                        $userid = Yii::app()->user->operating_id;
                    }
                }
                else {
                    $userid = Yii::app()->user->id;
                }
                
                $diary = TrainingEntry::model()->findAll('
                t.id_user = '.$userid.' and t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -8, DAYOFWEEK("'.$date.'")-9) DAY 
                ORDER BY t.date, t.start_time');
                
                $activity_array = Activity::model()->findAll(array('index'=>'id'));
                
                //Oznacenia treningov konretneho pouzivatela
                $sqlStatement = 'select l.id, l.id_user, coalesce(l.name,d.name) as `name`, 
                    coalesce(l.color,d.color) as `color`, l.id_default from label l 
                    left join default_label d on l.id_default=d.id where l.id_user='.$userid.';';
                $result = $this->executeStringQuery($sqlStatement);

                $labels = array();
                foreach ($result as $label){ //indexacia do pola podla ID-cka
                    $labels[$label->id] = $label; 
                }
            
                $summary = $this->getWeeklySummary($userid, $date);
                
                $workouts = $this->createDailyEntries($diary, $labels, $activity_array);
                $this->createSingleWeek($workouts, $summary,$activity_array, $date);
            }
                
        }
                       
        ///Metoda pre vyskladanie diviek so zaznamami
        /**
         * @var $plan Array of TrainingEntry
         * @var $labels Array of Label joined with DefaultLabel 
         * @var $activity_array Array of all results from Activity table
         * @return $workouts Associative Array with html snippets for each day
         */
        public function createDailyEntries($diary, $labels, $activity_array){
            
            $z = 1;
            $temp_old_date = '';
            $workouts = array();
            
            foreach ($diary as $day){
                $i = 0;
                if($temp_old_date == $day->date){ //viac zaznamov pre jeden den
                    $workouts[$day->date] .= '</div><div class="single-entry" id="'.$day->id.'"
                        title="'.Yii::t('diary','Click to open this entry').'" data-id="' . $day->id . '" ';
                    if($day->id_label != null || $day->id_label != ''){
                        $workouts[$day->date] .= 'style="border-bottom: 5px solid #'.$labels[$day->id_label]->color.'">';
                    }
                    else{
                        $workouts[$day->date] .='>';
                    }
                }else {
                    if($temp_old_date != '')
                        $workouts[$temp_old_date] .= '</div>';
                    $workouts[$day->date] = '<div class="single-entry" id="'.$day->id.'"
                        title="'.Yii::t('diary','Click to open this entry').'" data-id="' . $day->id . '" ';
                    if($day->id_label != null || $day->id_label != ''){
                        $workouts[$day->date] .= 'style="border-bottom: 5px solid #'.$labels[$day->id_label]->color.'">';
                    }
                    else{
                        $workouts[$day->date] .='>';
                    }
                }

                if(isset($activity_array[$day->id_activity])){
                    $workouts[$day->date] .= '<div class="single-day-activity" id="activity-'.$day->id_activity.'">'.
                            Yii::t('activity',$activity_array[$day->id_activity]->name).'</div>';
                }
                $workouts[$day->date].= '<div class="TrainingEntry-sm-feelings" id="sm-feelings'.$day->feelings.'"></div>';

                $workouts[$day->date] .= '<div class="single-day-duration">'.date ('H:i',strtotime($day->duration)).'</div>';

                $workouts[$day->date] .= '<a class="single-day-delete" title="'.Yii::t('diary','Click to delete this entry').'"></a>';

                $workouts[$day->date] .= '<div style="clear:both"></div>';

                if($day->avg_hr != null || $day->max_hr != null){
                    $workouts[$day->date] .= '<div class="single-entry-data-wr"><span class="data-label">'.Yii::t('diary','Heart rate').'</span><br/>'.'<span class="single-entry-data">';
                    $workouts[$day->date] .= $day->avg_hr? $day->avg_hr : '--';
                    $workouts[$day->date] .= '/';
                    $workouts[$day->date] .= $day->max_hr? $day->max_hr : '--';
                    $workouts[$day->date] .= '</span> '.Yii::t('diary','bpm').'</div>';
                    $i++;
                }
                if($day->distance != 0){
                    $workouts[$day->date] .= '<div class="single-entry-data-wr"><span class="data-label">'.$day->getAttributeLabel('distance').'</span><br/>'.
                            '<span class="single-entry-data">'.number_format($day->distance, 1, '.', '').'</span> km</div>';
                    $i++;
                }
                if($day->avg_speed != 0){
                    $workouts[$day->date] .= '<div class="single-entry-data-wr"><span class="data-label">'.$day->getAttributeLabel('avg_speed').'</span><br/>'.
                            '<span class="single-entry-data">'.number_format($day->avg_speed, 1, '.', '').'</span> km/h</div>';
                    $i++;
                }
                elseif ($day->avg_pace != '00:00:00' && $day->avg_pace != null){
                    $workouts[$day->date] .='<div class="single-entry-data-wr"><span class="data-label">'.$day->getAttributeLabel('avg_pace').'</span><br/>'.
                            '<span class="single-entry-data">'.date ('i:s',strtotime($day->avg_pace)).'</span> min/km</div>';
                    $i++;
                }

                if($i<3 && $day->avg_watts){
                    $workouts[$day->date] .= '<div class="single-entry-data-wr"><span class="data-label">'.Yii::t('diary','Avg Watts').'</span><br/>'.
                            '<span class="single-entry-data">'.$day->avg_watts.'</span> W</div>';
                    $i++;
                }
                if($i<3 && $day->ascent){
                    $workouts[$day->date] .= '<div class="single-entry-data-wr"><span class="data-label">'.$day->getAttributeLabel('ascent').'</span><br/>'.
                            '<span class="single-entry-data">'.$day->ascent.'</span> m</div>';
                    $i++;
                }

                $workouts[$day->date] .= '<div class="diary-single-description">'.$this->substr_unicode($day->description, 0, 42);
                if(strlen($day->description) > 42){
                   $workouts[$day->date] .= '...';
                }
                $workouts[$day->date] .= '</div>';

                $temp_old_date = $day->date;

                if(count($diary) == $z){ //ukoncenie posledneho zaznamu
                    $workouts[$day->date] .= '</div>';
                }
                $z++;
            }
            
            return $workouts;
        }
        
        ///Metoda pre vyskladanie tyzdna s treningovym dennikom
        /**
         * @var $workouts Array of TrainingEntry
         * @var $summary Object with summary data  
         * @var $activity_array Array of all results from Activity table
         * @var $monday String beginning of the week
         */
        public function createSingleWeek($workouts,$summary,$activity_array, $monday){
            
            $this->translateDays();
                echo '<div class="single-week">';

                    for($j=1; $j<=7; $j++): 

                        $add_days = '+'.($j-1).' days';
                        $date = Date('Y-m-d', strtotime($add_days, strtotime($monday)));
                        $single_day_date = '<div class="single-day-date" id="'.date('Y-m-d',strtotime($date)).'">'.
                                    '<div class="day-part">'.$this->days[date('w',strtotime($date))].'</div>'.
                                    '<div class="date-part">'.date('d. m. Y',strtotime($date)).'</div></div>';

                        echo '<div class="single-day-full" id="'.$date.'" ondrop="drop(event)" ondragover="allowDrop(event)">';
                        echo $single_day_date;
                        echo '<div style="clear:both"></div>';
                        if(isset($workouts[$date])){
                            echo $workouts[$date];
                        }

                        echo '</div>';

                    endfor; 

                echo '<div class="classic-box week-summary">';
                    echo '<h3>'.Yii::t('diary','Total').':</h3>';
                    echo '<div class="inner-week-summary">';
                    
                    $sumtotdays = 0;
                    $sumtothours = strtotime('1970-01-01 00:00:00');
                    $todayDate = date('Y-m-d');
                    $todayDatetime = strtotime("$todayDate 00:00:00");
                    echo '<table>';
                    foreach($summary as $value){
                        
                        echo '<tr><td>'.Yii::t('activity',$activity_array[$value->id_activity]->name).' ('.$value->totworkouts.'): </td>';
                        echo '<th>'.$value->tottime.'</th></tr>';
                        $sumtotdays += $value->totworkouts;
                        $sumtothours += strtotime("$todayDate $value->tottime") - $todayDatetime;
                    }
                    echo '</table>';
                    echo '<hr>';
                    echo '<div style="font-size:110%; font-weight: bold">';
                    echo Yii::t('diary','Total workouts completed').': <div class="float-right">'.$sumtotdays.'</div><br />';
                    echo Yii::t('diary','Hours completed').': <div class="float-right">'.date('H:i:s',$sumtothours).'</div><br />';
                    echo '</div>';
            
                echo '</div></div><div style="clear: both"></div></div> '; 
        }
}