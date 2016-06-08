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
<div id="EditProfile-privacy-form" class="classic-box">
    <h3><?php echo Yii::t('user','Privacy settings')?></h3>
    <!--ERROR AND SUCESS FLASHES-->
    <div class="flash-success">
        <?php echo Yii::t('user','Privacy settings has been succesfully saved')?>
    </div>
    <div class="flash-error">
        <?php echo Yii::t('error','We are terribly sorry').'. '.Yii::t('error', 'Error occured during your request')?>
    </div>
    <!--END OF FLASHES-->
        <?php
            $keys = $privacy->getAttributes();
            unset($keys['id']);
            $checked = 'checked="checked"';
        ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo Yii::t('user','public')?></th>
                    <th><?php echo Yii::t('user','friends')?></th>
                    <th><?php echo Yii::t('user','secret')?></th>
                </tr>
            </thead>
            <tbody>
        <?php
            foreach($keys as $key=>$value){
                echo '<tr id="privacy-row-'.$key.'">';
                echo '<th>'.$privacy->getAttributeLabel($key).'</th>';
                for($i=1; $i<=3; $i++){
                   $cell = '<td><input type="radio" name="'.$key.'" value="'.$i.'" ';
                   if($i == $value)
                       $cell .= 'checked="checked"';
                   $cell .='></td>';
                   echo $cell;
                }
                echo '</tr>';
            }
        ?>
             </tbody>
        </table>
        <?php echo CHtml::link(Yii::t('user', 'Save privacy'),'',array('id'=>'save-privacy-button','class'=>'styled-button wide-button','title'=>Yii::t('user', 'Save privacy settings'))); ?>
   
</div><!--End of editing privacy form-->
<div class="plain-style-form form" id="EditProfile-form">
    
    <?php echo CHtml::beginForm('','post'); ?>
        <?php echo Chtml::errorSummary(array($model,$fitness,$address)); ?>
        <?php require Yii::app()->basePath.'/views/file-upload-flash-handler.php';?>
    
        <?php //Flash o neuspesnom ulozeni
            if(Yii::app()->user->hasFlash('notsaved')):?>
                <div class="flash-error">
                        <?php echo Yii::app()->user->getFlash('notsaved'); ?>
                </div>
            <?php endif;?>
    
        <p class="note"><?php echo Yii::t('global','Fields with ');?><span class="required">*</span><?php echo Yii::t('global',' are required');?></p>
        
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
        
        <div class="EditProfile-info">
            <div class="classic-box">
                <h3><?php echo Yii::t('user','Basic fitness data')?></h3>
                <div class="row">
                    <?php echo Chtml::activeLabel($model,'id_primary_activity', array('class'=>'small')); ?>
                    <?php 
                        if($model->id_primary_activity==null){
                            echo Chtml::activeDropDownList($model,'id_primary_activity', $activity,array('prompt'=>'')); 
                        }
                        else{
                            echo Chtml::activeDropDownList($model,'id_primary_activity', $activity); 
                        }
                    ?>
                </div>

                <div style="clear:both"></div>

                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'gender',array('class'=>'requiredField small'))?>
                    <?php echo CHtml::activeDropDownList($model,'gender',array('M'=>'Male','F'=>'Female'))?>       
                </div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'birthday',array('class'=>'small'))?>
                    <?php echo CHtml::activeTextField($model,'birthday',array('size'=>'10'))?>  
                </div>

                <div style="clear:both"></div>

                <div class="float-left">
                    <?php echo CHtml::activeLabel($fitness,'weight',array('class'=>'small'))?>
                    <?php echo CHtml::activeTextField($fitness,'weight',array('size'=>'10'))?>
                </div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($fitness,'height',array('class'=>'small'))?>
                    <?php echo CHtml::activeTextField($fitness,'height',array('size'=>'10'))?>
                </div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($fitness,'max_hr',array('class'=>'small'))?>
                    <?php echo CHtml::activeTextField($fitness,'max_hr',array('size'=>'10'))?>
                </div>
                <div class="float-left">
                    <?php echo CHtml::activeLabel($fitness,'rest_hr',array('class'=>'small'))?>
                    <?php echo CHtml::activeTextField($fitness,'rest_hr',array('size'=>'10'))?>
                </div>

                <div style="clear:both"></div>
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
            
            <div style="clear:both"></div>
            <div class="row">
                <?php echo CHtml::activeLabel($address,'street',array('class'=>'small'))?>
                <?php echo CHtml::activeTextField($address,'street',array('size'=>'20','maxlength'=>'40'))?>
            </div>

            <div style="clear:both"></div>

            <div class="row">
                <?php echo CHtml::activeLabel($address,'city',array('class'=>'small'))?>
                <?php echo CHtml::activeTextField($address,'city',array('size'=>'20','maxlength'=>'40'))?>
            </div>

            <div style="clear:both"></div>

            <div class="row">
                <?php echo CHtml::activeLabel($address,'zip',array('class'=>'small'))?>
                <?php echo CHtml::activeTextField($address,'zip',array('size'=>'20','maxlength'=>'15'))?>
            </div>

            <div style="clear:both"></div>

            <div class="row">
                <?php echo CHtml::activeLabel($address,'id_country',array('class'=>'small'))?>  
                <?php 
                    if($address->id_country==null){
                        echo Chtml::activeDropDownList($address,'id_country', $country,array('prompt'=>'')); 
                    }
                    else{
                        echo Chtml::activeDropDownList($address,'id_country', $country);
                    }
                ?>
            </div>
        </div>
<?php echo CHtml::endForm(); ?>
</div><!-- END OF EDIT PROFILE FORM -->

<?php echo CHtml::beginForm('','post',array('id'=>"EditPhoto-form",'enctype'=>"multipart/form-data", 'style'=>'display:none')); /*ENCTYPE je dolezity pri nacitavani suborov*/?>   
    <input type="file" name="uploaded" id="uploading-file" />
<?php echo CHtml::endForm(); ?>

<?php if (Yii::app()->user->roles == 'athlete') :?>
<div id="EditProfile-hr-zones-wrapper">
    <div id="EditProfile-hr-zones" class="classic-box">
        <h3><?php echo Yii::t('user','Heart rate zones')?></h3>
            <table>
            <?php
                $i = 0;
                foreach ($hr_zones as $zone){
                    echo '<tr>';
                    echo '<td>'.++$i.'.</td>';
                    if($zone->name == null && $zone->id_default != null)
                        echo '<td>'.$default_zones[$zone->id_default]->name.'</td>';
                    else
                        echo '<td>'.$zone->name.'</td>';
                    echo '<td>'.$zone->min.'</td>';
                    echo '<td class="sep">to</td>';
                    echo '<td>'.$zone->max.'</td>';
                    echo '</tr>';
                }
            ?>
            </table>
    </div>
        <a id="ep-add-z-button" class="styled-button wide-button"><?php echo Yii::t('user','Click to add zone')?></a>
        <a id="ep-save-z-button" class="styled-button wide-button"><?php echo Yii::t('user','Save zones')?></a>
</div>

<?php endif; ?>
<script type="text/javascript"> //ULOZENIE NASTAVENI SUKROMIA
    $(document).ready(function(){
        $("#save-privacy-button").click(function(){
            
            $("#save-privacy-button").attr("disabled", true).animate({opacity: 0},"fast");
            
            $element_success = $("#EditProfile-privacy-form").find(".flash-success");
            $element_error = $("#EditProfile-privacy-form").find(".flash-error");
            
            $listRadio = $("#EditProfile-privacy-form").find("input[type='radio']:checked");
            
            var arrayData = new Object();
            for(i=0; i<($listRadio.length); i++){
                  arrayData[$listRadio.eq(i).attr("name")] = $listRadio.eq(i).val();
            }
            
            $.ajax({
                type: "POST",
                url: "<?php echo Yii::app()->createAbsoluteUrl('user/saveprivacy')?>",
                data: {privacy: arrayData, user: "<?php echo $model->id?>"},
                success: function(data, status){
                    $("#save-privacy-button").delay(2300).animate({opacity: 1},"slow").attr("disabled", false);
                    
                    try{
                        xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                        bool_value = xmlDocumentElement.firstChild.data;

                        if(bool_value === "1"){
                            if ( $element_error.is(':visible') ){ //Ak je viditelny neuspesny flash tak ho shovame
                                $element_error.hide();
                            }
                            $element_success.fadeIn("slow").delay(1600).fadeOut("slow");
                        }
                        else{
                            $element_error.fadeIn("slow").delay(1600).fadeOut("slow");
                        }                          
                    }
                    catch(e){
                        $element_error.fadeIn("slow").delay(1600).fadeOut("slow");
                    }
                },
                error: function(jqXHR,textStatus,errorThrown){ 
                    $("#save-privacy-button").delay(2300).animate({opacity: 1},"slow").attr("disabled", false);
                    
                    $element_error.fadeIn("slow").delay(1600).fadeOut("slow");
                }
            });
        });
    });
</script>

<?php 
    Yii::app()->clientScript->registerScript('set-zones-goals-height',
            '$(document).ready(function(){
                $_height = $("#EditProfile-hr-zones").height();
                if($("#EditProfile-goals").height() > $_height){
                    $_height = $("#EditProfile-goals").height();
                }
                $("#EditProfile-goals").css("height", $_height+"px");
                $("#EditProfile-hr-zones").css("height", $_height+"px");
            });',CClientScript::POS_END);
?>
