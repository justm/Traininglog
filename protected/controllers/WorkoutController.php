<?php

///Controller pre akcie tykajuce sa zdielanych treningov

class WorkoutController extends Controller
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
                                        'view','loadcomments','markcomments'
                                    ),
				'users'=>array('*'),
			),
                        array('allow',
				'actions'=>array(
                                        'addcomment',
                                    ),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        ///Metoda zobrazuje spracuje udaje pre stranku na ktorej su zobrazene detaily treningu a zobrazi stranku
        /**
         * @var $id ID TrainingEntry
         */
        public function actionView($id){
            
            $workout = TrainingEntry::model()->findByPk($id);
            $user = User::model()->findByPk($workout->id_user); //vyhladanie autora treningu
            
            $friendship = null;
            $coach_cooperation = null;
            if($workout != null){
                if(!Yii::app()->user->isGuest){ //kontrola ci su priatelia, alebo sportovec<=>trener
                    if(($friendship = Sparring::model()->find('id_user='.Yii::app()->user->id.' and status = 2'))==null){
                        if(($coach_cooperation = CoachCooperation::model()->find('id_coach = '.Yii::app()->user->id.
                            ' and id_athlete = '.$user->id.' and status = 1'))!=null){ 
                        }
                    }
                }

                if($coach_cooperation != null){
                    $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id != '.$workout->id.' order by date DESC limit 6');
                    $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                            left join `user` u on c.id_user = u.id where c.id_trainingentry ='.$workout->id.' order by c.date DESC limit 5';
                
                    $allComments = $this->executeStringQuery($sqlStatement);
                    
                    $activity_array = Activity::model()->findAll(array('index'=>'id'));
                    $comment = new Comment(); 
                    $comment->id_trainingentry = $workout->id;

                    $this->render('view',
                            array(
                                'workout'=>$workout,
                                'sharedworkouts'=>$sharedworkouts,
                                'activity_array'=>$activity_array,
                                'user'=>$user,
                                'comment'=>$comment,
                                'allComments'=>array_reverse($allComments),
                            ));
                }
                elseif(((Yii::app()->user->isGuest || $friendship == null) && $workout->id_visibility != 1)){ //nemaju pravo trening nie je public
                    $this->render('denied');
                }
                else {
                    if((Yii::app()->user->isGuest || $friendship == null) && $workout->id_visibility == 1){ //trening je public a zobrazime aj dalsie autorove public treningy
                        $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility = 1 and id != '.$workout->id.' order by date DESC limit 6');
                        
                        $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                        left join `user` u on c.id_user = u.id where c.id_visibility = 1 and 
                                c.id_trainingentry ='.$workout->id.' order by c.date DESC limit 5';
                    }
                    else{
                        $sharedworkouts = TrainingEntry::model()->findAll('id_user='.$user->id.' and id_visibility != 3 and id != '.$workout->id.' order by date DESC limit 6');
                    
                        $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                        left join `user` u on c.id_user = u.id where (c.id_visibility !=3 and 
                                c.id_trainingentry = '.$workout->id.') or (c.id_visibility = 3 and id_user = '.Yii::app()->user->id.
                                ' and c.id_trainingentry = '.$workout->id.') order by c.date DESC limit 5';
                        if(Yii::app()->user->id == $user->id){ ///Prezeram si vlastny trening a mozem vidiet vsetky komenty
                            $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                            left join `user` u on c.id_user = u.id where c.id_trainingentry ='.$workout->id.' order by c.date DESC limit 5';
                        }
                    }
                    $allComments = $this->executeStringQuery($sqlStatement);
                    
                    $activity_array = Activity::model()->findAll(array('index'=>'id'));
                    $comment = new Comment(); 
                    $comment->id_trainingentry = $workout->id;

                    $this->render('view',
                            array(
                                'workout'=>$workout,
                                'sharedworkouts'=>$sharedworkouts,
                                'activity_array'=>$activity_array,
                                'user'=>$user,
                                'comment'=>$comment,
                                'allComments'=>array_reverse($allComments),
                            ));
                }
            }
            else{
                $this->render('denied');
            }
        }
                
        ///Metoda prida komentar k treningovemu zaznamu prostrednictvom ajax requestu
        public function actionAddcomment(){
            if(isset($_POST['text'])){
                $comment = new Comment();
                $comment->text = $_POST['text'];
                $comment->id_trainingentry = $_POST['id_trainingentry'];
                $comment->id_user = Yii::app()->user->id;
                $comment->id_visibility = $_POST['id_visibility'];
                $comment->date = date('Y-m-d G:i:s');
                $comment->seen = 0;
                
                $user = User::model()->findByPk(Yii::app()->user->id);
                
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo '<response>';
                    echo '<validation>';
                        if($comment->save()){
                            echo 'ok';
                        echo '</validation>';
                                echo $this->createXmlComment($user, $comment);
                        }else{
                            echo 'error';
                            echo '</validation>';
                        }
                    echo '</response>';
            }
        }
        
        ///Metoda nacita viacero starsich komentarov prostrednictvom AJAX requestu
        public function actionLoadcomments(){
            if(isset($_POST['offset'])){
                $offset = $_POST['offset'];
                $workout = TrainingEntry::model()->findByPk($_POST['id_entry']);
                $user = User::model()->findByPk($workout->id_user);
                $friendship = null;
                
                if(!Yii::app()->user->isGuest){ //kontrola ci su priatelia
                    $friendship = Sparring::model()->find('id_user='.Yii::app()->user->id.' and status = 2');
                }
                
                if((Yii::app()->user->isGuest || $friendship == null) && $workout->id_visibility == 1){ //trening je public a zobrazime aj dalsie autorove public treningy
                        $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                        left join `user` u on c.id_user = u.id where c.id_visibility = 1 and 
                                c.id_trainingentry ='.$workout->id.' order by c.date DESC limit 10 offset '.$offset;
                }
                else{
                    $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                    left join `user` u on c.id_user = u.id where (c.id_visibility !=3 and 
                            c.id_trainingentry = '.$workout->id.') or (c.id_visibility = 3 and id_user = '.Yii::app()->user->id.
                            ' and c.id_trainingentry = '.$workout->id.') order by c.date DESC limit 10 offset '.$offset;
                    if(Yii::app()->user->id == $user->id){ ///Prezeram si vlastny trening a mozem vidiet vsetky komenty
                        $sqlStatement = 'select c.*, u.fullname, u.profile_picture, u.gender, u.username from `comment` c 
                        left join `user` u on c.id_user = u.id where c.id_trainingentry ='.$workout->id.' order by c.date DESC limit 10 offset '.$offset;
                    }
                }
                $usercomment_obj = array_reverse($this->executeStringQuery($sqlStatement));
                
                header('Content-Type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
                echo '<response>';
                foreach ($usercomment_obj as $one){
                    echo $this->createXmlComment($one);
                }
                echo '</response>';
            }
        }
        
        
        ///Metoda vytvori cast XML suboru z udajmi potrebnymi pre zobrazenie komentaru
        /**
         * @var $usercomment_obj Merged Object of user and comment
         */
        private function createXmlComment($user, $comment = null){
            if($comment == null){
                $comment = new Comment;
                if(isset($user->text) && isset($user->date)){ //objekty user aj comment su zlucene do jedneho
                    $comment->text = $user->text;
                    $comment->date = $user->date;
                    $comment->id = $user->id;
                }
            }
            $xml_part = '<comment>';
                $xml_part .= '<id>';
                    $xml_part .= $comment->id;
                $xml_part .= '</id>';
                $xml_part .= '<fullname>';
                    $xml_part .= $user->fullname;
                $xml_part .= '</fullname>';
                $xml_part .= '<link>';
                    $xml_part .= Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/view/'.$user->username;
                $xml_part .= '</link>';

                $xml_part .= '<picture>';
                    if($user->profile_picture != '' && ($pic = $this->getPicture($user->profile_picture, 'profile-picture'))!= null){
                        $xml_part .= $pic;
                    }
                    else{
                        $xml_part .= Yii::app()->request->baseUrl.'/images/photo-default-'.$user->gender.'.png';
                    }
                $xml_part .= '</picture>';

                $xml_part .= '<text>';
                    $xml_part .= $comment->text;
                $xml_part .= '</text>';
                $xml_part .= '<date>';
                    $xml_part .= date('G:i, d. m. Y',strtotime($comment->date));
                $xml_part .= '</date>';
            $xml_part .= '</comment>';
            
            return $xml_part;
        }
        
        ///Metoda oznaci zobrazene komentare ako precitane prostrednictvom AJAX requestu
        public function actionMarkcomments(){
            if(isset($_POST['array_id']) && isset($_POST['id_training'])){
                
                $entry = TrainingEntry::model()->findByPk($_POST['id_training']);
                
                if($entry->id_user == Yii::app()->user->id){ //Iba ak prehliadam vlastny trening a komentare k nemu
                    $condition = '';
                    foreach ($_POST['array_id'] as $id){
                        $condition .= $id.', ';
                    }
                    $condition = substr($condition, 0, count($condition)-3); //odstranenie poslednej ciarky

                    $comments = Comment::model()->findAll('id in ('.$condition.')');
                    foreach ($comments as $comment){
                        if($comment->seen != 1){
                            $comment->seen = 1;
                            $comment->save();
                        }
                    }
                }
            }
        }
}