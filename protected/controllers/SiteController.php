<?php

/// Trieda typu controller, ktora obsluhuje základné stránky aplikácie: Index, Contact, Login, Logout
class SiteController extends Controller
{
	/// Metóda ktorá deklaruje základné akcie aplikácie
        /**
         * Ide napríklad o využitie Captcha kódu v kontaktnom formulári a volanie statických stránok
         * @return array Deklarované akcie
         */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
        
        /// Metóda vráti stránku views/site/index.php
	public function actionIndex()
	{
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){ //bez tejto podmienky redirect padne v IE 6-8
                if ($_SERVER['REQUEST_URI'] == Yii::app()->params->homePath.'/')
                {
                        if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sk')
                            $this->redirect(Yii::app()->params->homePath.'/sk');
                        else
                            $this->redirect(Yii::app()->params->homePath.'/en');
                }
            }
            $sqlStatement = 'SELECT 
				    CONCAT(hours, ":", LPAD(minutes,2,0), ":", LPAD(seconds,2,0)) as tottime, totworkouts FROM 
			    (
				    SELECT 
				    FLOOR(SUM(total_timer_time) / 3600) as hours,
				    FLOOR(MOD(SUM(total_timer_time), 3600) / 60) as minutes,
				    FLOOR(MOD(SUM(total_timer_time),60)) as seconds,
				    COUNT(id) as totworkouts
				    FROM training_entry
			    ) te;';
            $result = $this->executeStringQuery($sqlStatement);
            $this->render('index',array('result'=>$result));
	}

        ///FORWARDUJE chybové hlásenia na subdoménu
	public function actionError(){
            
            $url = 'http://ride.' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; 
            header("Location: {$url}");
	}

	/// Metóda zobrazuje a obsluhuje stránku s kontaktným formulárom
        /**
         * views/site/contact.php
         */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/// Metóda odhlási aktuálne prihláseného užívateľa a vráti domovskú stránku
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl.'/');
	}  
}