<?php

///Controller pre model CoachCooperation, sprava vz5ahov medzi trenerom a sportovcom,

class CoachingController extends Controller
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
				'actions'=>array(),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('startcooperation',),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
         ///Metoda ulozi udaje o nastaveni sukromia prostrednictvom AJAX Requestu
        /*
         * status 1-request sent, status 2 - request confirmed, status 3 - request deleted 
         */
        public function actionStartcooperation(){
            $bool = false;
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
                $coaching = new CoachCooperation;
                $coaching->id_coach = $_POST["usersending"];
                $coaching->id_athlete = $_POST["usertohandle"];
                $coaching->cooperatin_since = date("Y-m-d H:i:s");  
                $coaching->status = 1;

                if($coaching->save()){
                    $bool = true;
                }
            }  
            
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
            echo '<response>';
                echo $bool;
            echo '</response>';
           
        }
        
                
        ///Metoda vymazava relaciu medzi trenerom a sportovcom prostrednictvom AJAX poziadavky
        public function actionQuitcooperation(){
            
            $bool = false;
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
                $relation = CoachCooperation::model()->find('id_athlete="'.$_POST["usertohandle"].'" and id_coach="'.$_POST["usersending"].'"');
                
                if($relation != null){
                    if($relation->delete()){
                        $bool = true;
                    }
                }
            }
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
            echo '<response>';
                echo $bool;
            echo '</response>';
        }
}
