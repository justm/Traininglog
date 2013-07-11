<?php

///Controller pre model Sparring, sprava vz5ahov medzi pouzivatelmi

class SparringController extends Controller
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
				'actions'=>array('addsparring','confirmsparring','unfriend','refusesparring'),
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
        public function actionAddsparring(){
            $bool = false;
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
                $sparring = new Sparring;
                $sparring->id_user = $_POST["usersending"];
                $sparring->id_partner = $_POST["usertohandle"];
                $sparring->request_date = date("Y-m-d H:i:s");  

                $sparring->status = 1;


                if($sparring->save()){
                    $bool = true;
                }
            }  
            
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
            echo '<response>';
                echo $bool;
            echo '</response>';
           
        }
        
        ///Metoda potvrdzuje ziadost o pridanie medzi sparring partnerov prostrednictvom AJAX requestu
        public function actionConfirmsparring(){
            
            $bool = false; 
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
            
                $relation = Sparring::model()->find('id_user="'.$_POST["usertohandle"].'" and id_partner="'.$_POST["usersending"].'"');
                
                if($relation != null){ //iba pre pripad ze sa nieco nepodari skontrolujeme ci sme ziskali relaciu
                    $sparring = new Sparring;
                    $sparring->id_user = $_POST["usersending"];
                    $sparring->id_partner = $_POST["usertohandle"];
                    $sparring->request_date = $relation->request_date;  

                    $sparring->status = 2;
                    $relation->status = 2;

                    if($sparring->save()){
                        if($relation->save()){
                            $bool = true;
                        }
                    }            
                }
            }        
            
            header('Content-Type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
            echo '<response>';
                echo $bool;
            echo '</response>';
           
        }
        
        ///Metoda spracuje poziadavku na zrusenie ziadosti o priatelstvo odoslanu prostrednictvom AJAX requestu
        public function actionRefusesparring(){
            $bool = false;
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
                
                $relation = Sparring::model()->find('id_user="'.$_POST["usertohandle"].'" and id_partner="'.$_POST["usersending"].'"');    
                
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
        
        ///Metoda vymazava relaciu medzi pouzivatelmi prostrednictvom AJAX poziadavky
        public function actionUnfriend(){
            
            $bool = false;
            if(isset($_POST["usersending"]) && isset($_POST["usertohandle"]) ){
                $relation = Sparring::model()->find('id_user="'.$_POST["usertohandle"].'" and id_partner="'.$_POST["usersending"].'"');
                $backRelation = Sparring::model()->find('id_user="'.$_POST["usersending"].'" and id_partner="'.$_POST["usertohandle"].'"');
                
                if($relation != null && $backRelation !=null){
                    if($relation->delete()){
                        if($backRelation->delete()){
                            $bool = true;
                        }
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
