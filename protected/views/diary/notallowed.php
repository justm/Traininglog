<?php
/**
 * @var $this PlanController
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/error.png" height="42" width="350"></div>


<?php 
    echo '<h2>';
        echo Yii::t('error','We are terribly sorry');
    echo '</h2>';
    echo Yii::t('error','You are not allowed to perform this operation'); 
?>