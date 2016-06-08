<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/mainlogo.png" height="42" width="457"></div>

<?php //Flash o uspesnej registracii
    if(Yii::app()->user->hasFlash('registered')):?>
        <div class="flash-success">
                <?php echo Yii::app()->user->getFlash('registered'); ?>
        </div>
    <?php endif;?>

<div class="float-left" style="width: 55%">
    <div class="main-pushed-left">
        <h2 class="main-header"><?php echo Yii::t('global','All you need')?></h2> 
        <h3 class="main-header"><?php echo Yii::t('global','simple and handful training diary')?><h3>
    </div>    
    <div class="main-pushed-left">
        <h4 class="main-header"><?php echo Yii::t('global','Total workouts saved')?></h4>
        <?php echo '<span class="main-values">'.$result[0]->totworkouts.'</span>' ?>
        <div style="clear: both"></div>
    </div>  
    <div class="main-pushed-left">
        <h4 class="main-header"><?php echo Yii::t('global','Total hours saved')?></h4>
        <?php echo '<span class="main-values">'.$result[0]->tottime.'</span>'?>
        <div style="clear: both"></div>
    </div> 
</div>

