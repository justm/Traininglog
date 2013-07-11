<?php
    class I18nUrlManager extends CUrlManager
    {
            public function createUrl ($route, $params=array(), $ampersand='&')
            {
                    if (!isset($params['language'])) $params['language'] = Yii::app()->getLanguage();
                    return parent::createUrl($route, $params, $ampersand);
            }

            public function parsePathInfo($pathInfo)
            {   
                    parent::parsePathInfo($pathInfo);

                    $lang = Yii::app()->getRequest()->getParam('language');

                    if ( in_array($lang, array('en', 'sk')) ) Yii::app()->setLanguage($lang);
            }
    }
?>