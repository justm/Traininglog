<?php

///Controller pre operacie administratora

class AdminController extends Controller
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
                                        'users',
                                    ),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        public function actionUsers() {
            $roles = Role::model()->findAll(array('index'=>'id'));
            $model=new User('search');
            $model->unsetAttributes();  // clear any default values
            
            if(isset($_GET['User']))
		$model->attributes=$_GET['User'];
            
            $this->render('users',array('model'=>$model,'roles'=>$roles));
        }
}