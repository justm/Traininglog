<?php
/* @var $this UserController */
/* @var $model User */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/signup.png" height="42" width="389"></div>

<h2>Creating new user</h2>
<?php if(Yii::app()->user->hasFlash('registered')):?>
        <div class="flash-success">
                <?php echo Yii::app()->user->getFlash('registered'); ?>
        </div>
<?php endif;?>

<div class="form" id="SignUp-form">
    <?php echo CHtml::beginForm('','post'); ?>
    
	<?php echo Chtml::errorSummary($model); ?>
    
        <?php //Flash o neuspesnom ulozeni
            if(Yii::app()->user->hasFlash('notsaved')):?>
                <div class="flash-error">
                        <?php echo Yii::app()->user->getFlash('notsaved'); ?>
                </div>
            <?php endif;?>
    
        <div class="float-left" style="width: 205px; text-align: center">
            <?php echo CHtml::activeLabel($model,'id_role',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeDropDownList($model,'id_role',array(''=>'','1'=>'Admin','3'=>'Coach'))?>
            <?php echo CHtml::error($model,'id_role')?>       
        </div>
    
        <div class="float-left" style="width: 205px; text-align: center">
            <?php echo CHtml::activeLabel($model,'gender',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeDropDownList($model,'gender',array(''=>'','M'=>'Male','F'=>'Female'))?>
            <?php echo CHtml::error($model,'gender')?>       
        </div>
    
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'name',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'name',array('size'=>'20','maxlength'=>'30'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'name')?></div>
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'lastname')?>
            <?php echo CHtml::activeTextField($model,'lastname',array('size'=>'20','maxlength'=>'30'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'lastname')?></div>
        </div>
        
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'username',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'username',array('size'=>'20','maxlength'=>'20'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'username')?></div> 
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'email',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'email',array('size'=>'20','maxlength'=>'40'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'email')?></div>
        </div>
        
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'password',array('class'=>'requiredField'))?>
            <?php echo CHtml::activePasswordField($model,'password',array('size'=>'20','maxlength'=>'25'))?>
            <?php echo CHtml::error($model,'password')?> 
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'confirmPassword',array('class'=>'requiredField'))?>
            <?php echo CHtml::activePasswordField($model,'confirmPassword',array('size'=>'20','maxlength'=>'25'))?>
            <?php echo CHtml::error($model,'confirmPassword')?> 
        </div>

	<div class="submit-button">
            <input type="submit" value="Create" class="big-button" id="signup-button" title="Create user" style="margin-left: 150px" />
	</div>
<?php echo CHtml::endForm(); ?>

</div><!-- form -->
