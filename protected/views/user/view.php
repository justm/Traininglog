<?php
/** 
 * @var $this UserController
 * @var $user User 
 * @var $activity Activity - NULL if not found
 * @var $address Address - NULL if not found
 * @var $country Country - NULL if not found
 * @var $fitness UserFitness - NULL if not found
 * @var $privacy Privacy
 * @var $isFriend bool - Is friend or not 
 */
?>
<?php ///Google indexing and Sharing
    $this->pageTitle = $user->fullname .' - profile | MôjTréning.sk';
    $this->pageDesc = $user->fullname.' používa MôjTréning.sk, inteligentný tréningový denník. Zlepšite svoj tréning a cvičte zdravo a s prehľadom už dnes.'
?>

<div id="pagelogo">
    <img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/profile.png" height="42" width="231">
</div><!--END OF PAGE LOGO-->


<?php 
    if(isset(Yii::app()->user->roles))
        $role = Yii::app()->user->roles;
    else 
        $role = 'guest';

    if($role == 'athlete'){
        require_once 'user_relations_handler/sparring.php';
    }
    else if($role == 'coach'){
        require_once 'user_relations_handler/coach_cooperation.php';
    }
?>

<div id="userProfile-shared-workouts"><!--START OF SHARED WORKOUTS CONTAINER-->
    <h3><?php echo Yii::t('dashboard','Shared workouts')?></h3>
    <?php if($sharedworkouts != null){
            $count_visible = 0;
            foreach ($sharedworkouts as $entry){
                    if((Yii::app()->user->isGuest && $entry->id_visibility == 1) || !Yii::app()->user->isGuest){
                        $this->createSharedEntryDiv($entry,$activity_array);
                        $count_visible++;
                    }
            }
        }
        if($sharedworkouts == null || $count_visible == 0):?>
            <h4 class="note"><?php echo $user->name.' '.Yii::t('dashboard','currently doesn\'t share any workouts with you')?></h4>
            <div class="noworkouts" id="<?php echo $user->gender?>"></div>
        <?php endif;?>
</div><!--END OF SHARED WORKTOUS-->


<div id="userProfile"><!--START OF USER PROFILE CONTAINER-->
    
        <div id="userProfile-top-wrapper">
        
            <div id="userProfile-mainInfo">
                
                <div class="float-left">
                        <div id="userProfile-about"><?php echo $user->about?></div>
                </div>
                <div style="clear:both"></div>
                
                <div class="float-left">
                    <h2 class="userProfile-display-name">
                        <?php 
                                echo $user->name;
                                if($privacy->lastname==1 || ($privacy->lastname==2 && $isFriend)){
                                    echo'</h2><h2 class="userProfile-display-name">' . $user->lastname;
                                }
                                else{
                                    echo '<span id="userProfile-alias-name"> alias '.$user->username.'</span>';
                                }
                        ?>
                    </h2>
                    
                    <?php if($user->gender == 'M')
                            echo '<div class="userProfile-gender-icon" id="male"></div>';
                        else 
                            echo '<div class="userProfile-gender-icon" id="female"></div>';
                    ?>
                </div>
                <div style="clear:both"></div>
                
                <?php if ($activity != null || $activity != ''):?>
                <div class="float-right" id="userProfile-displayed-sport"><?php echo Yii::t('user', 'Interested in ')?><span class="red"><?php echo $activity->name?></span></div>
                <?php endif;?>
            </div><!--END OF USER MAIN INFO-->
        
            <div id="userProfile-photo-wrapper">
                
                <?php //Kontrola ci ma pouzivatel profilovu fotku, ak nie zobrazuje sa default
                    if($user->profile_picture != '' && ($pic = $this->getPicture($user->profile_picture, 'profile-picture'))!=null):?>
                        <img src="<?php echo $pic ?>" width="200" height="200"/>
                <?php else: ?>
                        <img src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$user->gender.'.png'?>" width="200" height="200"/>
                <?php endif; ?>
            </div><!--END OF PROFILE PHOTO WRAPPER-->
            
        </div><!--END OF PROFILE TOP WRAPPER-->
        <div style="clear:both"></div>
        
        <?php if($summary!=null && ($privacy->basic_tr_data==1 || ($privacy->basic_tr_data==2 && $isFriend))):?>
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
        </div>
        <?php endif;?><!--END OF TABLE WITH TRAINIG DATA-->
    
        <div class="classic-box userProfile-info">
            <h3><?php echo Yii::t('user','Basic fitness data')?></h3>
            <div id="userProfile-info-inner">
            <?php
                if($fitness != null){
                    $attrb = $fitness->getAttributes(array('weight','height','max_hr','rest_hr'));
                    $iter = 0;
                    foreach ($attrb as $value => $key){
                        if(($key != null || $key != '') && 
                                ($privacy->getAttribute($value)==1 || ($privacy->getAttribute($value)==2 && $isFriend))){
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
            ?>
            </div>
        </div><!--END OF USER INFO-->
        
        <div class="classic-box userProfile-contact">
            <h3><?php echo Yii::t('user','Contact informations')?></h3>
            <div id="userProfile-contact-inner">
            <?php
                
                if($privacy->email==1 || ($privacy->email==2 && $isFriend)){
                   echo $user->email."<br />"; 
                   $break = true;
                }
                if(($user->phone !=null || $user->phone != '') && 
                                ($privacy->phone==1 || ($privacy->phone==2 && $isFriend))){
                    echo '<span class="attrlabel">'.$user->getAttributeLabel('phone').' </span>';
                    echo $user->phone.'<br />';
                    $break = true;
                }
                if(isset($break))
                    echo '<br />';
                
                if($address != null){
                    $attrb = $address->getAttributes(array('city','street','zip'));
                    foreach ($attrb as $value => $key){
                        if(($key != null || $key != '') && 
                                ($privacy->getAttribute($value)==1 || ($privacy->getAttribute($value)==2 && $isFriend))){
                            echo '<span class="attrlabel">'.$address->getAttributeLabel($value).' </span>';
                            echo $key."<br />";
                        }
                    }
                    if(($address->zip !=null || $address->zip != '') && 
                                ($privacy->city==1 || ($privacy->city==2 && $isFriend))){
                        echo '<span class="attrlabel">'.$address->getAttributeLabel('zip').' </span>';
                        echo $address->zip.'<br />';
                    }
                    if(($country !=null || $country != '') && 
                                ($privacy->country==1 || ($privacy->country==2 && $isFriend))){
                        echo '<span class="attrlabel">'.$address->getAttributeLabel('id_country').' </span>';
                        echo $country->name."<br />";  
                    }
                }
            ?>
            </div>
        </div><!--END OF USER CONTACT-->
        
</div><!--END OF USER PROFILE CONTAINER-->

<?php //ZAROVNANIE CONTAINEROV CONTACT A USER-INFO
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