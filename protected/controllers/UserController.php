<?php

///Controller pre model User, najdolezitejsia trieda ktora obsluhuje vsetky operacie na pouzivatelskym uctom

class UserController extends Controller
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
				'actions'=>array('signup','validateusername','validateemail','view'),
				'users'=>array('*'),
			),
                        array('allow',
				'actions'=>array(
                                        'update', 'profile', 'saveprivacy'
                                    ),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array(
                                        'people','finduser','loadfriends','suggestusers'
                                    ),
				'roles'=>array('athlete','coach'),
			),
                        array('allow',
				'actions'=>array('create','bann','enable'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        /// Metoda nacita vsetky data pouzivatelskeho profilu aktualne prihlaseneho pouzivatela a presmeruje na stranku /profile
	public function actionProfile() {
                $user = User::model()->findByPk(Yii::app()->user->id);
                $activity = Activity::model()->findByPk($user->id_primary_activity);
                $address = Address::model()->findByPk($user->id_address);
                if($address != null){
                    $country = Country::model()->findByPk($address->id_country);
                }
                else{
                    $country = null;
                }
                $fitness = UserFitness::model()->findByPk($user->id_user_fitness);
                    
                $summary = $this->getSummary($user->id);
                
                $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility != 3 order by date DESC limit 8');
                $activity_array = Activity::model()->findAll(array('index'=>'id'));
                
		$this->render('profile',array(
			'user'=>$user,
                        'activity'=>$activity,
                        'address'=>$address,
                        'country'=>$country,
                        'fitness'=>$fitness,
                        'summary'=>$summary,
                        'sharedworkouts'=>$sharedworkouts, 
                        'activity_array'=>$activity_array,
		));
	}
        
        ///Metoda zobrazuje profil ineho pouzivatela
        public function actionView($id){
            if(isset(Yii::app()->user->roles)){
                $role = Yii::app()->user->roles;
            }
            else {
                $role = 'guest';
            }
                
            if(($user = User::model()->find('username="'.$id.'"'))!=null){
                
                if($user->id == Yii::app()->user->id){ //vlastny profil
                    $this->redirect(Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/profile');
                }
                
                if(($viewed_user_role = Role::model()->findByPk($user->id_role)->name) == 'athlete' 
                        && ($role == 'athlete' || $role == 'guest')){
                    
                    require_once 'user_relations_handler/user_view_user.php';
                    
                    $this->render('view',array(
                            'user'=>$user,
                            'activity'=>$activity,
                            'address'=>$address,
                            'country'=>$country,
                            'fitness'=>$fitness,
                            'privacy'=>$privacy,
                            'isFriend'=>$isFriend,
                            'requestRecieved'=>$requestRecieved,
                            'requestSent'=>$requestSent,
                            'sparringRelation'=>$relation,
                            'summary'=>$summary,
                            'sharedworkouts'=>$sharedworkouts, 
                            'activity_array'=>$activity_array,
                    ));  
                }
                
                elseif($role != 'admin' && $viewed_user_role == 'admin'){
                    $this->render('notfound',array('username'=>$id));
                }
                
                elseif ($viewed_user_role == 'coach' && $role == 'athlete') {
                    //TODO skontroluje ci si zverencom trenera, ak ano mozes ho zobrazit
                    if((CoachCooperation::model()->find('id_coach = '.$user->id.
                            ' and id_athlete = '.Yii::app()->user->id.' and status = 1')) != null){
                        
                        require_once 'user_relations_handler/user_view_coach.php';

                        $this->render('viewcoach',array(
                                'user'=>$user,
                                'activity'=>$activity,
                                'address'=>$address,
                                'country'=>$country,
                                'fitness'=>$fitness,
                                'privacy'=>$privacy,
                        ));  
                    }
                    else{
                        $this->render('notfound',array('username'=>$id));
                    }
                }
                elseif ($viewed_user_role == 'athlete' && $role == 'coach') {
                    
                    require_once 'user_relations_handler/coach_view_user.php';
                    
                    $this->render('view',array(
                                'user'=>$user,
                                'activity'=>$activity,
                                'address'=>$address,
                                'country'=>$country,
                                'fitness'=>$fitness,
                                'privacy'=>$privacy,
                                'isFriend'=>$isFriend,
                                'sharedworkouts'=>$sharedworkouts,
                                'activity_array'=>$activity_array,
                                'summary'=>$summary,
                                'coach_cooperation'=>$coach_cooperation
                    ));  
                }
                
            }
            else{
                $this->render('notfound',array('username'=>$id));
            }
        }
        
        ///Metoda ktora ziska data pre vyplnenie tabulku Recent training summary
        /**
         * @param string $userid Id pouzivatela ktoremu trenigove zaznamy patria
         */
        private function getSummary($userid){
            
            $monday =  date('Y-m-d',strtotime("Monday this week"));
            if(date('w',strtotime(date('Y-m-d')))==0) //Ak je dnesny datum nedela, mensi hack pretoze default je nedela ako prvy den
            $monday =  date('Y-m-d',strtotime("Monday last week"));

            $summary['thisweek'] = (object) $this->summaryQuery($userid, $monday,date('Y-m-d'));
            $summary['lastweek'] = (object) $this->summaryQuery($userid, 
                date('Y-m-d H:i:s', strtotime('last Monday '.$monday)),
                date('Y-m-d H:i:s', strtotime('-1 day',  strtotime($monday))));
            $summary['year'] = (object) $this->summaryQuery($userid, date('Y-m-d H:i:s', strtotime('1/1 this year')),date('Y-m-d'));
            $summary['total'] = (object) $this->summaryQuery($userid, '1970-01-01', date('Y-m-d'));

            return $summary;
        }
        
        ///Metoda ktora ziska z treningoveho dennika summar v rozmedzi urciteho datumu
        /**
         * @param string $userid Id pouzivatela ktoremu trenigove zaznamy patria
         * @param string $datefrom Datum od ktoreho sa zacne hladat
         * @param string $dateto Datum po ktory sa vyhladava
         */
        private function summaryQuery($userid, $datefrom, $dateto){
            
            $connection = Yii::app()->db;
            $connection->active = true;
            
            $sqlStatement = 'select count(*) as totdays, coalesce(SEC_TO_TIME(SUM(TIME_TO_SEC(duration))),\'00:00:00\') as tottime
                from training_entry where id_user = '.$userid.' and duration <> "00:00:00"
                and `date` >= STR_TO_DATE(\''.$datefrom.' 00:00:00\', \'%Y-%m-%d %H:%i:%s\')
                and `date` <= STR_TO_DATE(\''.$dateto.' 23:59:59\', \'%Y-%m-%d %H:%i:%s\')';
            $command = $connection->createCommand($sqlStatement);
            $result = $command->query();
            
            foreach ($result as $row){
                return array('sumHours'=>$row['tottime'], 'sumDays'=>$row['totdays']);
                break; //query vrati 2 rovnake vysledky, staci jeden
            }
        }

        ///Metoda obsluhuje vytvranie noveho uzivatelskeho konta sportovca
	/** Vytvori model User a zavola stranku s registracnym formularom, nasledne obsluhuje ukadanie novaho uzivatela do DB */
	public function actionSignup() {
		$user=new User;
                $privacy = new Privacy;
                
		if(isset($_POST['User']))
		{
                    $user->attributes=$_POST['User'];
                    $user->id_role = 2;
                    $user->fullname = $user->name ." ".$user->lastname;
                    
                    $transaction = Yii::app()->db->beginTransaction();
                    
                    try{    
                        if($privacy->save()){
                            $user->id_privacy = $privacy->id;
                            if($user->save()){
                                $this->createDefaultHrZones($user->id);
                                $this->createDefaultLabels($user->id);
                                $transaction->commit();
                                Yii::app ()->user->setFlash('registered',Yii::t('global','Congratulations, You have successfully created your account. Let\'s start with signing in.'));
                                $this->redirect('site/index');  
                            }
                        }
                    }
                    catch (Exception $e){
                            $transaction->rollback();
                            Yii::app()->user->setFlash('notsaved','Sorry, there might be some problems during database session, 
                                            try to resend the form. If problems persist please contact our <a href="contact">administrator</a>.');
                            $this->redirect('signup'); 
                    }
		}
                
                $this->render('signup',array(
			'model'=>$user,
		));
	}

        /// Metoda obsluhuje upravu pouzivatelskeho profilu
	/** Administrator systemu moze upravovat profil ktorehokolvek uzivatela, bezny uzivatel iba svoj */
	public function actionUpdate($id='') {
                
                $id_check = 1;
                if($id == ''){
                    $id_check = 0;
                }
                if($id=='' || (Yii::app()->user->roles != 'admin' && $id != '')){
                    $id = Yii::app()->user->id;
                }
                //NACITANIE MODELOV
		$user = User::model()->findByPk($id);
                $activity = CHtml::listData(Activity::model()->findAll(), 'id', 'name');
                $country = CHtml::listData(Country::model()->findAll(), 'id', 'name');
                
                $privacy = Privacy::model()->findByPk($user->id_privacy);
                
                if (($address = Address::model()->findByPk($user->id_address)) == null){
                    $address = new Address;
                }
                if (($fitness = UserFitness::model()->findByPk($user->id_user_fitness)) == null){
                    $fitness = new UserFitness;
                }
                if(($hr_zones = HrZone::model()->findAll('id_user='.$id.' order by `min`'))==null){//Ak pouzivatel este nema defaultne zony vytvoria sa
                    $this->createDefaultHrZones($id);
                }
                $default_zones = DefaultHrZone::model()->findAll(array('index'=>'id'));
                
                
                //Spracovanie formularu s uploadom profilovej fotky
                if(isset($_FILES['uploaded'])){
                    if($_FILES['uploaded']['name']!='' || $_FILES['uploaded']['name']!= null){
                            $user->setAttributes( array('password'=>$user->getOldPassword())); //nahodime stare heslo
                            $user->setAttributes( array('confirmPassword'=>$user->getOldPassword()));
                            $old_picture_name = $user->profile_picture;
                            
                            if(($user->profile_picture = $this->uploadProfilePricture())!=null){ //ak je upload obrázku uspesny stary zmazeme
                                
                                @unlink(Yii::app()->params->uploadDirectory.'/profile-picture/'.$old_picture_name);
                                
                                $user->save();
                            }
                            
                            $user->setAttributes( array('password'=>'')); //po ulozenie heslo zase schovame
                            $user->setAttributes( array('confirmPassword'=>''));
                    }
                }
                
                //Spracovanie odoslaneho formularu
		if(isset($_POST['User']))
		{
			$user->attributes=$_POST['User'];
                        $user->fullname = $user->name . ' ' . $user->lastname;
                        $fitness->attributes = $_POST['UserFitness'];
                        $address->attributes = $_POST['Address'];
                        
                        $check = $user->getAttributes(array('password')); //Kontrola ci pouzivatel menil aj heslo
                        if($check['password']==""){
                                    $user->setAttributes( array('password'=>$user->getOldPassword())); //Ak nenastala hodi sa do fomulara stare heslo
                                    $user->setAttributes( array('confirmPassword'=>$user->getOldPassword()));
                        }
                        
                        $transaction = Yii::app()->db->beginTransaction();
                         
                        try{
                            //validacia vstupov fitness a adress
                            if($fitness->validate() && $address->validate()){
                                
                                if(!$this->checkEmpty($fitness)){ //Ak pouzivatel nevyplnil udaje fitness, nevytvara sa novy zaznam, inak ano
                                    $fitness->save();
                                    $user->id_user_fitness = $fitness->id;
                                } 
                                if(!$this->checkEmpty($address)){//Ak pouzivatel nevyplnil udaje address, nevytvara sa novy zaznam, inak ano
                                    $address->save();
                                    $user->id_address = $address->id;
                                }
                                if( $user->save()){
                                    $transaction->commit();
                                    $this->redirect('profile');   
                                }
                            }
                            $user->afterFind(); //Opatovne schovanie hesla ak sa nepodari ulozenie
                        }
                        catch(Exception $e){
                                $transaction->rollback();
                                Yii::app()->user->setFlash('notsaved','Sorry, there might be some problems during database session, 
                                            try to resend the form. If problems persist please contact our <a href="contact">administrator</a>. <br/> Message:'.$e->getMessage()."<br>".$user->id_primary_activity);
                                $this->redirect('update');
                        }	
		}
                
                if(Yii::app()->user->roles == 'admin' && $id_check == 0){
                    $this->render('updateadmin',array(
			'model'=>$user,
                    ));
                }else{
                    $this->render('update',array(
                            'model'=>$user,
                            'address'=>$address,
                            'fitness'=>$fitness,
                            'activity'=>$activity,
                            'country'=>$country,
                            'privacy'=>$privacy,
                            'hr_zones'=>$hr_zones,
                            'default_zones'=>$default_zones,
                    ));
                }
	}
        
        ///Pomocna funkcia, ktora vytvori defaultne pasma tepovej frekvencie
        /**
         * @var $id Id pouzivatela ktoremu patria zony
         */
        private function createDefaultHrZones($id){
            $zone = new HrZone();
            $zone->id_default = 1;
            $zone->min = 100;
            $zone->max = 120;
            $zone->id_user = $id;
            $zone->save();
            
            $zone = new HrZone();
            $zone->id_default = 2;
            $zone->min = 120;
            $zone->max = 150;
            $zone->id_user = $id;
            $zone->save();
            
            $zone = new HrZone();
            $zone->id_default = 3;
            $zone->min = 165;
            $zone->max = 170;
            $zone->id_user = $id;
            $zone->save();
            
            $zone = new HrZone();
            $zone->id_default = 4;
            $zone->min = 170;
            $zone->max = 185;
            $zone->id_user = $id;
            $zone->save();
        }
        ///Pomocna funkcia, ktora vytvori defaultne pasma tepovej frekvencie
        /**
         * @var $id Id pouzivatela ktoremu patria oznacenia
         */
        private function createDefaultLabels($id){
            $label = new Label();
            $label->id_default = 1;
            $label->id_user = $id;
            $label->save();
            
            $label = new Label();
            $label->id_default = 2;
            $label->id_user = $id;
            $label->save();
            
            $label = new Label();
            $label->id_default = 3;
            $label->id_user = $id;
            $label->save();
        }
                
        ///Metoda spracuje udaje a zobrazí stránku People
        public function actionPeople(){
            $activity = Activity::model()->findAll();
            $user = User::model()->findByPk(Yii::app()->user->id);
            $users = array();
            
            if(Yii::app()->user->roles == 'athlete'){
                $friends = User::model()->findAll('id in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2) order by fullname limit 16'); 
                
                if($user->id_primary_activity != null){ //pouzivatel ma nastavenu primarnu aktivitu
                    if(TrainingEntry::model()->findAll('id_user = '.Yii::app()->user->id) != null){
                        //absolvoval aspon jeden trening => navrhnutie ludi podla priemerneho objemu
                        
                        $sqlStatement = 'select SUM(TIME_TO_SEC(duration))/count(*) as avg from training_entry where id_user = 1 and duration != "00:00:00"';
                        $result = $this->executeStringQuery($sqlStatement);
                        $avg = $result[0]->avg;
                        $users = User::model()->findAll(
                                'id_primary_activity = '.$user->id_primary_activity.' and 
                                id not in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2) and
                                id in (select id_user from training_entry where duration != "00:00:00" group by id_user having SUM(TIME_TO_SEC(duration))/count(*) 
                                between '.$avg * 0.8.' and '.$avg * 1.2.')    
                                and id != '.Yii::app()->user->id.' and id_role = 2 limit 7');
                       
                    }if(($addlimit = 7 - count($users)) != 0){ //doplnenie zoznamu odporucanych ludi podla uz len podla primarnej aktivity
                        $id_string = $users[0]->id;
                        for($i = 1; $i< count($users); $i++){
                            $id_string .= ', '.$users[$i]->id;
                        }
                        ($id_string == '') ? $id_string = 0 : $id_string;
                        $temp = User::model()->findAll(
                                'id not in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2) 
                                and id not in('.$id_string.') 
                                and id != '.Yii::app()->user->id.' and id_primary_activity = '.$user->id_primary_activity.' and id_role = 2 limit 7');
                    
                        $users = array_merge($users,$temp);
                    }
                }
                if(($addlimit = 7 - count($users)) != 0){ //doplnenie zoznamu ludi uz len podla tohoci su v priateloch alebo nie
                    $temp = User::model()->findAll('id not in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2)
                        and id != '.Yii::app()->user->id.' and id_role = 2 limit '.$addlimit);
                    $users = array_merge($users,$temp);
                }
                
                $this->render('people',array('friends'=>$friends,'users'=>$users, 'activity'=>$activity));
            }
            elseif (Yii::app()->user->roles == 'coach') {
                $users = User::model()->findAll('id not in(select id_athlete from coach_cooperation where id_coach = '.Yii::app()->user->id.' and status = 1) and id != '.Yii::app()->user->id.' and id_role = 2 limit 21');
           
                $this->render('peoplecoach',array('users'=>$users, 'activity'=>$activity));
            }
            
        }
        
        ///Metoda vrati zoznam priatelov v zavislosti od potrebneho offsetu
        /**Offset znamená kolko pouzivatelov uz je na stranke zobrazenych a kolko teda odosialat nemusime*/
        public function actionLoadfriends(){
            if(isset($_GET['offset'])){
                $offset = $_GET['offset'];
                $friends = User::model()->findAll('id in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2) order by fullname limit 16 offset '.$offset); 
                $activity = Activity::model()->findAll();
                
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo $this->createXml($friends, $activity);
            }
        }
        
        ///Metoda vrati zoznam pouzivatelov z ktorymi prihlaseny pouzivatel nie je v ziadnom vztahu
        /**Offset znamená kolko pouzivatelov uz je na stranke zobrazenych a kolko teda odosialat nemusime*/
        public function actionSuggestusers(){
            if(isset($_GET['offset'])){
                $offset = $_GET['offset'];
                $limit = $_GET['limit'];
                
                if(Yii::app()->user->roles == 'athlete')
                    $users = User::model()->findAll('id not in(select id_partner from sparring where id_user = '.Yii::app()->user->id.' and status = 2)  and id_role = 2 and id != '.Yii::app()->user->id.' limit '.$limit.' offset '.$offset);
                elseif (Yii::app()->user->roles == 'coach') 
                    $users = User::model()->findAll('id not in(select id_athlete from coach_cooperation where id_coach = '.Yii::app()->user->id.' and status = 1) and id != '.Yii::app()->user->id.' and id_role = 2 limit '.$limit.' offset '.$offset);
           
                $activity = Activity::model()->findAll();
                
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo $this->createXml($users, $activity);
            }
        }
        
        ///Metoda vrati zoznam pouzivatelov na zaklade podobneho stringu prijateho prostrednictvom AJAX requestu
        public function actionFinduser(){

            if(isset($_GET['finduser'])) {
                $match = $_GET['finduser'];
                $activity = Activity::model()->findAll();

                $users = User::model()->findAll(
                    'id_role = 2 and fullname LIKE :match',
                    array(':match' => "%$match%")
                );
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo $this->createXml($users, $activity);
            }

        }
        
        ///Metoda ulozi udaje o nastaveni sukromia prostrednictvom AJAX Requestu
        public function actionSaveprivacy(){
           if(isset($_POST["privacy"])){
                $user = User::model()->findByPk($_POST["user"]);
                $privacy = Privacy::model()->findByPk($user->id_privacy);
                $privacy->attributes = $_POST["privacy"];
                if($privacy->save()){
                    $bool = true;
                }
                else{
                    $bool = false;
                }
                    
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo '<response>';
                    echo $bool;
                echo '</response>';
           }
        }
         
        ///Metoda nacita pozadovany model
        /**
         * @param int $id ID pouzivatela ktoreho treba nacitat
         * @return User model
         * @throws CHttpException 404 nenajdeny model
         */
	public function loadModel($id){
            
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
        
        ///Metoda kontroluje ci su vsetky polia z prijateho formulara prazdne
        /**
         * @param CActiveRecord $model
         * @return boolean True ak su vsetky prazdne
         */
        protected function checkEmpty($model){
            $counter = 0;
            foreach ($model->getAttributes() as $key){
                if(!empty($key)){
                    $counter++;
                }
            }
            if ($counter == 0)
                return true;
            else 
                return false;
            
        }
        
        ///Metoda validuje jedinecnost pouzivatelskeho mena v databaze prostrednictov AJAX
        public function actionValidateusername(){
            
            if(isset($_GET["username"])){
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo '<response>';
                $name = $_GET['username'];
                $user = User::model()->find('username="'.$name.'"');
                if($user){
                   echo 'true'; 
                }
                if(!$user){
                    echo 'false';
                }
                echo '</response>';
            }
        }
        
        ///Metoda validuje jedinecnost emailu v databaze prostrednictvom AJAX
        public function actionValidateemail(){
            if(isset($_GET["email"])){
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo '<response>';
                $email = $_GET['email'];
                $user = User::model()->find('email="'.$email.'"');
                if($user){
                   echo 'true'; 
                }
                if(!$user){
                    echo 'false';
                }
                echo '</response>';
            }
        }
        
        ///Pomocna metoda, ktora generuje xml vystup 
        /**
         * @var $models User 
         * @var $activity Activity
         * @return $xml String ktory bude vrateny AJAX poziadavke
         */
            private function createXml($models, $activity){
            $xml = '';

            $xml .= '<response>';
                    foreach ($models as $model){
                        $xml .= '<user>';

                            $xml .= '<fullname>';
                                $xml .= $model->fullname;
                            $xml .= '</fullname>';

                            $xml .= '<username>';
                                $xml .= $model->username;
                            $xml .= '</username>';
                            
                            $xml .= '<gender>';
                                $xml .= $model->gender;
                            $xml .= '</gender>';
                            
                            $xml .= '<activity>';
                                if($model->id_primary_activity == ''){
                                    $xml .= 'null';
                                }
                                else{
                                    $xml .= $activity[$model->id_primary_activity-1]->name;
                                }
                            $xml .= '</activity>';
                            
                            $xml .= '<picture>';
                                if($model->profile_picture == ''){
                                    $xml .= 'null';
                                }
                                else{
                                    $xml .= $model->profile_picture;
                                }
                            $xml .= '</picture>';
                        $xml .= '</user>';
                    }
                $xml .= '</response>';
                
            return $xml;
        }
        
        ///Metoda ktora umoznuje administratorovi ulozit na pouzivatelsky ucet bann/zakazat ho
	public function actionBann($id) {
            if(Yii::app()->user->roles == 'admin'){
                $user = User::model()->findByPk($id);
                $user->account_status = 9;
                $user->save(false);
            }  
	}
        
        ///Metoda ktora umoznuje administratorovi opatovne obnovit pouzivatelsky ucet
	public function actionEnable() {
            if(Yii::app()->user->roles == 'admin' && isset($_POST['id'])){
                
                $user = User::model()->findByPk($_POST['id']);
                $user->account_status = 0;
                $user->save(false);
            }  
	}
        
        ///Metoda ktora umoznuje administratorovi vytovrenie akehokolvek pouzivatelskeho uctu
        public function actionCreate(){
            if(Yii::app()->user->roles == 'admin'){
                $user = new User;
                $privacy = new Privacy;
                if(isset($_POST['User'])){
                    
                    $user->attributes = $_POST['User'];
                    $user->fullname = $user->name ." ".$user->lastname;
                                        
                    $transaction = Yii::app()->db->beginTransaction();
                    
                    try{    
                        if($privacy->save()){
                            $user->id_privacy = $privacy->id;
                            if($user->save()){
                                $transaction->commit();
                                Yii::app ()->user->setFlash('registered',Yii::t('global','You have successfully created new account.'));        
                                $user=new User;  
                            }
                        }
                    }
                    catch (Exception $e){
                            $transaction->rollback();
                    }
                }
                
                $this->render('create',array(
			'model'=>$user,
		));
            }
        }
}

