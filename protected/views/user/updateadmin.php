<?php
/**
 * @var $this UserController 
 * @var $model User 
 * @var $address Address
 * @var $fitness UserFitness
 * @var $activity array List of Activity models
 * @var $country array List of Country models  
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/profile.png" height="42" width="231"></div>

<h2 class="flying">Edit profile</h2>

<div class="plain-style-form form" id="EditProfile-form">
    
    <?php echo CHtml::beginForm('','post'); ?>
        <?php echo Chtml::errorSummary(array($model)); ?>
        <?php require Yii::app()->basePath.'/views/file-upload-flash-handler.php';?>
    
        <?php //Flash o neuspesnom ulozeni
            if(Yii::app()->user->hasFlash('notsaved')):?>
                <div class="flash-error">
                        <?php echo Yii::app()->user->getFlash('notsaved'); ?>
                </div>
            <?php endif;?>
    
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        
        <!--TLACIDLO NA ODOSLANIE FORMULARU-->
        <div class="submit-button" >
            <input type="submit" value="Update" id="EditProfile-update-button" class="big-button" title="Save" style="margin: auto;" />
        </div>
        
        <div id="userProfile-top-wrapper">
            <div id="userProfile-mainInfo">
                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'about')?>
                    <?php echo CHtml::activeTextArea($model,'about',array('cols'=>55,'rows'=>4,'maxlength'=>'250'))?>
                    <?php echo CHtml::error($model,'about')?>
                </div>
                <div style="clear:both"></div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'name',array('class'=>'requiredField'))?>
                    <?php echo CHtml::activeTextField($model,'name',array('size'=>'15','maxlength'=>'30'))?>
                    <?php echo CHtml::error($model,'name')?>
                </div>
                <div class="float-right">
                    <?php echo CHtml::activeLabel($model,'lastname')?>
                    <?php echo CHtml::activeTextField($model,'lastname',array('size'=>'15','maxlength'=>'30'))?>
                    <?php echo CHtml::error($model,'lastname')?>
                </div>
                <div style="clear:both"></div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'password')?>
                    <?php echo CHtml::activePasswordField($model,'password',array('size'=>'15','maxlength'=>'25'))?>
                    <?php echo CHtml::error($model,'password')?> 
                </div>

                <div class="float-right">
                    <?php echo CHtml::activeLabel($model,'confirmPassword')?>
                    <?php echo CHtml::activePasswordField($model,'confirmPassword',array('size'=>'15','maxlength'=>'25'))?>
                    <?php echo CHtml::error($model,'confirmPassword')?> 
                </div>
            </div>
            <div id="userProfile-photo-wrapper">
                
                <?php //Kontrola ci ma pouzivatel profilovu fotku, ak nie zobrazuje sa default
                     if($model->profile_picture != '' && ($pic = $this->getPicture($model->profile_picture, 'profile-picture'))!=null):?>
                        <img src="<?php echo $pic ?>" width="200" height="200"/>
                <?php else: ?>
                        <img src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$model->gender.'.png'?>" width="200" height="200"/>
                <?php endif; ?>
                
                <a class="pencil edit-photo-start" title="Edit your photo"></a>
                <a id="edit-photo" class="edit-photo-start" title="Edit your photo">Edit picture</a>
                
                <?php Yii::app()->clientScript->registerScript('edit-photo-script',
                        '$(document).ready(function(){
                            $(".flash-success").delay(2000).slideUp();
                            $(".edit-photo-start").click(function(){
                                $("input[name=uploaded]").trigger("click");
                                $("#uploading-file").change(function(){
                                    $("#EditPhoto-form").submit();
                                });
                            });
                        });',
                        CClientScript::POS_END);?>
            </div>
        </div>
        
        <div class="EditProfile-contact">
            <div class="row">
                <?php echo CHtml::activeLabel($model,'username',array('class'=>'requiredField small'))?>
                <?php echo CHtml::activeTextField($model,'username',array('size'=>'20','maxlength'=>'20'))?>
            </div>
            
            <div style="clear:both"></div>
            
            <div class="row">
                <?php echo CHtml::activeLabel($model,'email',array('class'=>'requiredField small'))?>
                <?php echo CHtml::activeTextField($model,'email',array('size'=>'20','maxlength'=>'40'))?>
                <?php echo CHtml::error($model,'email')?> 
            </div>
            
            <div style="clear:both"></div>
            
            <div class="row">
                <?php echo CHtml::activeLabel($model,'phone',array('class'=>'small'))?>
                <?php echo CHtml::activeTextField($model,'phone',array('size'=>'20','maxlength'=>'40'))?>
            </div>
        </div>
<?php echo CHtml::endForm(); ?>
</div><!-- END OF EDIT PROFILE FORM -->

<?php echo CHtml::beginForm('','post',array('id'=>"EditPhoto-form",'enctype'=>"multipart/form-data", 'style'=>'display:none')); /*ENCTYPE je dolezity pri nacitavani suborov*/?>   
    <input type="file" name="uploaded" id="uploading-file" />
<?php echo CHtml::endForm(); ?>

    
