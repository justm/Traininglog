<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Môj tréning',

        'language'=>'en',
    
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),
//        'aliases' => array(
//            'xupload' => 'ext.xupload'
//        ),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'matusGIIgenerator',
                        'ipFilters'=>array('*'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
                        //definovanie presmerovacej url pre prihlasenie 
                        //ak sa neregitrovany pouzivatel pokusa dostat na stranku pre prihlasenych
                        'loginUrl'=>array('user/signup'), 
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
                        'class' => 'WebUser',
		),
//		//URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
                                '<view:(gii)>'=>'gii',
                                '<language:[a-z]{2}>'=>'site/index',
                                '<language:[a-z]{2}>/<view:(contact)>'=>'site/contact',
                                '<language:[a-z]{2}>/<view:(signup)>'=>'user/signup',
                                '<language:[a-z]{2}>/<view:(people)>'=>'user/people',
                                '<language:[a-z]{2}>/<controller:(plan)>'=>'plan/index',
                                '<language:[a-z]{2}>/<controller:(diary)>'=>'diary/index',
                                '<language:[a-z]{2}>/<controller:(dashboard)>'=>'dashboard/index',
                            
                                '<language:[a-z]{2}>/<view:\w+>' => 'site/page/view/<view>',
                                
				'<language:[a-z]{2}>/<controller:\w+>/<action:\w+>/<id:.*>'=>'<controller>/<action>',
				'<language:[a-z]{2}>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                                
                                //Pri pouzivani GII zakomentuj urlManager a pristupuj cez adresu index.php?r=gii/default/login
                                
			),
                        'class' => 'I18nUrlManager'
		),
            
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=mojtrening',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
                    'class'=>'CLogRouter',
                    'routes'=>array(
                            array(
                            'class'=>'CWebLogRoute',
                            //
                            // I include *trace* for the sake of the example, you can include more levels separated by commas
                            'levels'=>'trace',
                            //
                            // I include *vardump* but you can include more separated by commas
                            'categories'=>'vardump',
                            //
                            // This is self-explanatory right?
                            'showInFireBug'=>true
                        ),
                    ),
                 ),
                    
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'homePath'=>  "/TrainingLog/" . "index.php", //na remote serveri odobrat index.php a pouzivat clean url
                'homeUrl'=>'http://localhost/TrainingLog/',
                'fullPath'=>'http://localhost/TrainingLog/index.php/',
                'uploadDirectory'=>dirname(__FILE__).'/../../uploads/',
        ),
);