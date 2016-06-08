<?php

///Controller pre akcie v sekcii treningoveho planu

class PlanController extends Controller
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
                                        'index','delete',
                                        'getsingleentry','addweek','draganddrop'
                                    ),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        ///Metoda zobrazuje stranku treningoveho planu
        /**
         * @param string $date Datum podla ktoreho nacitame zaznamy z databazy pre tyzden s danym datumom a nasledujuci tyzden
         */
        public function actionIndex($id=''){
            
            $this->translateDays();
            $allowed = true;
            
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
                $singleEntry = new TrainingPlan;
                $singleEntry->duration = '00:00';

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
                elseif(isset($_POST['TrainingPlan'])){
                    if ($_POST['TrainingPlan']['id'] == 'null' || $_POST['TrainingPlan']['id'] == ''){
                        $singleEntry = new TrainingPlan;
                    }
                    else{
                        $singleEntry = TrainingPlan::model()->findByPk($_POST['TrainingPlan']['id']);
                    }

                    $singleEntry->id_user = $userid;
                    $singleEntry->id_activity = $_POST['TrainingPlan']['id_activity'];
                    $singleEntry->id_label = $_POST['TrainingPlan']['id_label'];
                    $singleEntry->description = $_POST['TrainingPlan']['description'];
                    $singleEntry->duration = $_POST['TrainingPlan']['hours'].':'.$_POST['TrainingPlan']['minutes'].':00';
                    $singleEntry->date = date('Y-m-d',  strtotime($_POST['TrainingPlan']['date']));

                    if($singleEntry->save()){
                        Yii::app()->user->setFlash('plan-saved',Yii::t('plan','Your entry has been succesfully planed'));
                    }
                        $singleEntry = new TrainingPlan;
                        $singleEntry->duration = '00:00';

                }

                //Kompletene nacitanie treningoveho planu pre nasledujuce 2 tyzdne
                $plan = TrainingPlan::model()->findAll('
                    t.id_user = '.$userid.' and t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                    AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -8, DAYOFWEEK("'.$date.'")-16) DAY 
                    order BY t.date');

                //Sumar z naplanovanych aktivit v hodinach a celkovom pocte dni pre relevantne dva tyzdne
                $summary[0] = (object) $this->getWeeklySummary($userid, $date);
                $summary[1] = (object) $this->getWeeklySummary($userid, Date('Y-m-d', strtotime('+7 days', strtotime($date))));

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
                            'plan'=>$plan,
                            'summary'=>$summary,
                            'singleEntry'=>$singleEntry, 
                            'activity'=>$activity,
                            'request_date'=>$date,
                            'activity_array'=>$activity_array,
                            'labels'=>$labels,
                            'newlabel'=>$newlabel
                        )
                );
            }
        }
        
        ///Metoda vymaze zaznam za tabulky Treningoveho planu
        public function actionDelete(){
            $bool = false;
            if(isset($_POST['id'])){
                $model = TrainingPlan::model()->findByPk($_POST['id']);
                if($model->delete()){
                     $bool = true;
                     Yii::app()->user->setFlash('plan-entry-deleted',Yii::t('diary','Entry was deleted'));
                }
            }
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';               
                echo '<response>';
                    echo $bool;
                echo '</response>';
        }


        ///Metoda ktora prostrednictvom AJAX requestu vrati XML s 1 zaznamom v treningovom plane
        public function actionGetsingleentry(){
            
            if(isset($_GET["id"])){
                $single_entry = TrainingPlan::model()->findByPk($_GET["id"]);
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
                $xml_part .= '<description>';
                    $xml_part .= $single_entry->description.' ';
                $xml_part .= '</description>';
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
                else{
                    $userid = Yii::app()->user->id;
                }
            
                $plan = TrainingPlan::model()->findAll('
                t.id_user = '.$userid.' and t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -1, DAYOFWEEK("'.$date.'")-9) DAY 
                order BY t.date');
                
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
            
                $summary = (object) $this->getWeeklySummary($userid, $date);
                
                $daily_plan = $this->createDailyEntries($plan, $labels, $activity_array);
                $this->createSingleWeek($daily_plan, $summary, $date);
            }
                
        }
        
               
        ///Metoda ktora ziska z treningoveho planu summar pre jeden tyzden podla datumu
        /**
         * @param string $userid Id pouzivatela ktoremu trenigovy plan patri
         * @param string $date Datum od ktoreho sa zacne hladat
         */
        private function getWeeklySummary($userid, $date){
            
            $connection=new CDbConnection(
                    Yii::app()->db->connectionString,
                    Yii::app()->db->username,
                    Yii::app()->db->password);
            $connection->active=true;
            
            $sqlStatement = 'SELECT count(*) as totdays, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) as tottime
                FROM training_plan t where t.id_user = '.$userid.' and 
                t.date >= "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, 6, DAYOFWEEK("'.$date.'")-2) DAY
                AND t.date < "'.$date.'" - INTERVAL IF(DAYOFWEEK("'.$date.'") = 1, -1, DAYOFWEEK("'.$date.'")-9) DAY and duration <> "00:00:00"';
            $command = $connection->createCommand($sqlStatement);
            $result = $command->query();
            
            foreach ($result as $row){
                return array('sumHours'=>$row['tottime'], 'sumDays'=>$row['totdays']);
                break; //query vrati 2 rovnake vysledky, staci jeden
            }
           
        }
        
        ///Metoda, ktora pozmeni datum pre zaznam ak v treningovom plane pouzivatel urobil drag & drop zmenu
        public function actionDraganddrop(){
            if(isset($_POST['id']) && isset($_POST['date'])){
                $entry = TrainingPlan::model()->findByPk($_POST['id']);
                $entry->date = $_POST['date'];
                $entry->save();
            }
        }
        
        ///Metoda pre vyskladanie diviek so zaznamami
        /**
         * @var $plan Array of Plan
         * @var $labels Array of Label joined with DefaultLabel 
         * @var $activity_array Array of all results from Activity table
         * @return $daily_plan Associative Array with html snippets for each day
         */
        public function createDailyEntries($plan, $labels, $activity_array){
            
            $z = 1;
            $temp_old_date = '';
            $daily_plan = array();
            
            foreach ($plan as $day){

                if($temp_old_date == $day->date){ //viac zaznamov pre jeden den
                    $daily_plan[$day->date] .= '</div><div class="single-entry" id="'.$day->id.'" 
                        draggable="true" ondragstart="drag(event)" 
                        title="'.Yii::t('plan','Click to open this entry or drag to move').'" ';
                    if(isset($labels[$day->id_label]) && ($day->id_label != null || $day->id_label != '')){
                        $daily_plan[$day->date] .= 'style="border-bottom: 5px solid #'.$labels[$day->id_label]->color.'">';
                    }
                    else{
                        $daily_plan[$day->date] .='>';
                    }
                }else {
                    if($temp_old_date != '')
                        $daily_plan[$temp_old_date] .= '</div>';
                    $daily_plan[$day->date] = '<div class="single-entry" id="'.$day->id.'" 
                        draggable="true" ondragstart="drag(event)" 
                        title="'.Yii::t('plan','Click to open this entry or drag to move').'" ';
                    if(isset($labels[$day->id_label]) && ($day->id_label != null || $day->id_label != '')){
                        $daily_plan[$day->date] .= 'style="border-bottom: 5px solid #'.$labels[$day->id_label]->color.'">';
                    }
                    else{
                        $daily_plan[$day->date] .='>';
                    }
                }

                if(isset($activity_array[$day->id_activity])){
                    $daily_plan[$day->date] .= '<div class="single-day-activity" id="activity-'.$day->id_activity.'">'.
                            Yii::t('activity',$activity_array[$day->id_activity]->name).'</div>';
                }
                $daily_plan[$day->date] .= '<div class="single-day-duration">'.date ('H:i',strtotime($day->duration)).'</div>';

                $daily_plan[$day->date] .= '<a class="single-day-delete" title="'.Yii::t('plan','Click to delete this entry').'"></a>';

                $daily_plan[$day->date] .= '<div style="clear:both"></div>';

                $daily_plan[$day->date] .= '<div class="single-day-description">' . $day->description;
                $daily_plan[$day->date] .= '</div>';
                $temp_old_date = $day->date;

                if(count($plan) == $z){ //ukoncenie posledneho zaznamu
                    $daily_plan[$day->date] .= '</div>';
                }
                $z++;
            }
            
            return $daily_plan;
        }
        
        ///Metoda pre vyskladanie tyzdna s treningovym planom
        /**
         * @var $daily_plan Array from createDailyEntries method
         * @var $summary Object with summary data  
         * @var $monday String beginning of the week
         */
        public function createSingleWeek($daily_plan,$summary, $monday){
            
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
                        if(isset($daily_plan[$date])){
                            echo $daily_plan[$date];
                        }

                        echo '</div>';

                    endfor; 

                    echo '<div class="classic-box week-summary">';
                        echo '<h3>'.Yii::t('plan','Total').':</h3>';
                        echo '<div class="inner-week-summary" style="line-height: 35px; font-size: 110%">';
                            echo Yii::t('plan','Total workouts planned').': '.$summary->sumDays.'<br />';
                            echo Yii::t('plan','Hours planned').': '.$summary->sumHours.'<br />';
                        echo '</div>';
                echo '</div>';    

                echo '<div style="clear: both"></div></div>';
        }
}