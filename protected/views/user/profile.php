<?php
/** @var $this UserController */
/** @var $user User */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/profile.png" height="42" width="231"></div>
<?php echo CHtml::link('Edit Profile',Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/update',array('id'=>'update-profile-button','class'=>'styled-button','title'=>'Update profile')); ?>

<div id="userProfile-shared-workouts">
    <h3><?php echo Yii::t('dashboard','Shared workouts')?></h3>
    <?php if($sharedworkouts == null):?>
    <h4 class="note">Currently you have no shared workouts</h4>
    <div class="noworkouts" id="<?php echo $user->gender?>"></div>
    <?php else:
        foreach ($sharedworkouts as $entry){
            $this->createSharedEntryDiv($entry,$activity_array);
        }
    endif;?>
</div><!--END OF SHARED WORKTOUS-->


<div id="userProfile">
    <div id="userProfile-top-wrapper">
            <div id="userProfile-mainInfo">
                <div class="float-left">
                    <div id="userProfile-about"><?php echo $user->about?></div>
                </div>
                <div style="clear:both"></div>
                <div class="float-left">
                    <h2 class="userProfile-display-name"><?php echo $user->name . '</h2><h2 class="userProfile-display-name">' . $user->lastname?></h2>
                    
                    <?php if($user->gender == 'M')
                            echo '<div class="userProfile-gender-icon" id="male"></div>';
                        else 
                            echo '<div class="userProfile-gender-icon" id="female"></div>';
                        ?>
                </div>
                <div style="clear:both"></div>
                <?php if ($activity != null || $activity != ''):?>
                    <div class="float-right" id="userProfile-displayed-sport"><?php echo Yii::t('user', 'Interested in ')?><span class="red"><?php echo Yii::t('activity',$activity->name)?></span></div>
                <?php endif;?>
            </div>
        
            <div id="userProfile-photo-wrapper">
                
                <?php //Kontrola ci ma pouzivatel profilovu fotku, ak nie zobrazuje sa default
                    if($user->profile_picture != '' && ($pic = $this->getPicture($user->profile_picture, 'profile-picture'))!=null):?>
                        <img src="<?php echo $pic ?>" width="200" height="200"/>
                <?php else: ?>
                        <img src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$user->gender.'.png'?>" width="200" height="200"/>
                <?php endif; ?>
                <a class="pencil"  href="<?php echo Yii::app()->params->homePath?>/user/update" title="Edit your profile and photo"></a>
                <a id="edit-photo" href="<?php echo Yii::app()->params->homePath?>/user/update" title="Edit your profile and photo">Edit picture</a>               
            </div>
        </div><!--END OF PROFILE TOP WRAPPER-->
        <div style="clear:both"></div>
            <?php if(Yii::app()->user->roles == 'athlete'):?>
                <div id="userProfile-basic-tr-data">
                    <h3><?php echo Yii::t('user', 'Recent training summary')?></h3>
                    <table class="plain-style-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php echo Yii::t('user', 'This week')?></th>
                                    <th><?php echo Yii::t('user', 'Last week')?></th>
                                    <th><?php echo Yii::t('user', 'This year')?></th>
                                    <th><?php echo Yii::t('user', 'Total')?></th>
                                </tr>
                            </thead>
                                <tbody>
                                <tr>
                                    <th class="red"><?php echo Yii::t('user', 'Total time')?></th>
                                    <?php 
                                        foreach ($summary as $data){
                                            $time = explode(':',$data->sumHours);
                                            echo '<td><span class="number">'.$time[0].'</span>:'.$time[1].'</td>';
                                        }
                                    ?>
                                </tr>
                                <tr>
                                    <th class="red"><?php echo Yii::t('user', 'Total workouts')?></th>
                                    <?php 
                                        foreach ($summary as $data){
                                            echo '<td><span class="number">'.$data->sumDays.'</span></td>';
                                        }
                                    ?>
                                </tr>
                            </tbody>
                    </table>
            </div><!--END OF TABLE WITH TRAINIG DATA-->
            <?php endif; ?>
        <div class="classic-box userProfile-info">
            <h3><?php echo Yii::t('user','Basic fitness data')?></h3>
            <div id="userProfile-info-inner">
            <?php
                if($fitness != null){
                    $attrb = $fitness->getAttributes(array('weight','height','max_hr','rest_hr'));
                    $iter = 0;
                    foreach ($attrb as $value => $key){
                        if($key != null || $key != ''){
                            if($iter % 2 == 0){
                                echo '<div style="clear:both"></div>';
                                echo '<div class="float-left rows">';
                            }
                            else{
                                echo '<div class="float-right rows">';
                            }
                                echo '<div class="float-left">'.$fitness->getAttributeLabel($value).'</div>';
                                echo '<div class="float-right attrvalue">'.$key.'</div>';
                                echo '</div>';
                         $iter++;
                        }
                        else{
                            $iter-=2;
                        }
                    }
                }
                else{?>
                    <div class="flash-notice" style="clear: both; text-align: center; font-size: 12px">
                        You can add some basic informations about you <a href="<?php echo Yii::app()->params->homePath?>/user/update">here</a>
                    </div> 
                <?php }?>
            </div>   
        </div><!--END OF USER INFO-->
        <div class="classic-box userProfile-contact">
            <h3><?php echo Yii::t('user','Contact informations')?></h3>
            <div id="userProfile-contact-inner">
            
            <?php
                
                echo $user->email."<br />";
                if($user->phone !=null || $user->phone != ''){
                    echo '<span class="attrlabel">'.$user->getAttributeLabel('phone').' </span>';
                    echo $user->phone.'<br />';
                }
                echo '<br />';
                if($address != null){
                    $attrb = $address->getAttributes(array('city','street','zip'));
                    foreach ($attrb as $value => $key){
                        if($key != null || $key != ''){
                            echo '<span class="attrlabel">'.$address->getAttributeLabel($value).' </span>';
                            echo $key."<br />";
                        }
                    }
                    if($country != null){
                        echo '<span class="attrlabel">'.$address->getAttributeLabel('id_country').' </span>';
                        echo $country->name."<br />";  
                    }
                }
            ?>
            </div>
        </div><!--END OF USER CONTACT-->
</div>
<?php 
    Yii::app()->clientScript->registerScript('set-contact-info-height',
            '$(document).ready(function(){
                $_height = $(".userProfile-contact").height();
                if($(".userProfile-info").height() > $_height){
                    $_height = $(".userProfile-info").height();
                }
                $(".userProfile-contact").css("height", $_height+"px");
                $(".userProfile-info").css("height", $_height+"px");
            });',CClientScript::POS_END);
?>