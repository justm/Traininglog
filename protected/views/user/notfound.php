<?php
/**
 * @var $this UserController
 * @var $username Pouzivatelske meno ktore nebolo najdene.
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/error.png" height="42" width="350"></div>


<?php 
    echo '<h2>';
        echo Yii::t('error','We are terribly sorry');
    echo '</h2>';
    echo Yii::t('global','Athlete').'<b><i> '.$username.' </i></b>'.Yii::t('error','was not found in our database'); 
?>