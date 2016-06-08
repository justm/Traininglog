<?php
/**
 * @var $this WorkoutController
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/diary.png" height="42" width="177"></div>
<h2 class="flying"><?php echo Yii::t('global','Detail view')?></h2>

<div id="Workout-detail-wr">
    <div class="float-left">
        <div id="userProfile-photo-wrapper" style="margin-bottom: 20px; margin-right: 20px;">
                <?php //Kontrola ci ma pouzivatel profilovu fotku, ak nie zobrazuje sa default
                    if($user->profile_picture != '' && ($pic = $this->getPicture($user->profile_picture, 'profile-picture'))!=null):?>
                        <img src="<?php echo $pic ?>" width="200" height="200"/>
                <?php else: ?>
                        <img src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$user->gender.'.png'?>" width="200" height="200"/>
                <?php endif; ?>
       </div>
        <div id="Workout-about-user">
            <?php if ($user->about != '' && $user->about != null)
                    echo $user->about;?>
        </div>  
    </div>
    <div id="Workout-detail-header">
        <?php
            echo '<h3 class="large-fullname">';
                echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/view/'.$user->username.'">'.$user->fullname.'</a>';
            echo '</h3>';
            echo '<div id="workout-date">';
                echo date('d. m. Y', strtotime($workout->date)).', '.Yii::t('dashboard','this is my training:');
            echo '</div>';
        ?>
    </div>
    <div id="Workout-detail-data">
        <?php 
            if(isset($activity_array[$workout->id_activity])){
                echo '<div class="single-day-activity" id="activity-'.$workout->id_activity.'">'.
                        Yii::t('activity',$activity_array[$workout->id_activity]->name).'</div>';
            }
            echo '<div class="float-right">';
                if($workout->feelings!=null || $workout->feelings != '')
                    echo '<div class="TrainingEntry-feelings" id="feelings'.$workout->feelings.'"></div>';
                echo '<div class="single-day-duration">'.
                        '<span style="font-weight:400">'.Yii::t('global', 'Time').': </span>'.date ('H:i',strtotime($workout->duration)).
                        '</div>';
            echo '</div>';
            echo '<div style="clear:both"></div>';
            echo '<table id="Workout-detail"><tbody>';
                $i = 0; $controll = 0;
                echo '<tr>';
                if($workout->distance != 0 && $workout->distance != null){
                    echo '<td>'.$workout->getAttributeLabel('distance').'</td>';
                    echo '<th>'.number_format($workout->distance, 1, '.', '').'<span class="units"> km</span></th>';
                    $i++;
                }
                if($workout->avg_speed != 0 && $workout->avg_speed != null){
                    echo '<td>'.$workout->getAttributeLabel('avg_speed').'</td>';
                    echo '<th>'.number_format($workout->avg_speed, 1, '.', '').'<span class="units"> km/h</span></th>';
                    $i++;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->avg_pace != '00:00:00' && $workout->avg_pace != null){
                    echo '<td>'.$workout->getAttributeLabel('avg_pace').'</td>';
                    echo '<th>'.date ('i:s',strtotime($workout->avg_pace)).'<span class="units"> min/km</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->avg_watts != 0 && $workout->avg_watts != null){
                    echo '<td>'.$workout->getAttributeLabel('avg_watts').'</td>';
                    echo '<th>'.$workout->avg_watts.'<span class="units"> W</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->max_watts != 0 && $workout->max_watts != null){
                    echo '<td>'.$workout->getAttributeLabel('max_watts').'</td>';
                    echo '<th>'.$workout->max_watts.'<span class="units"> W</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->ascent != 0 && $workout->ascent != null){
                    echo '<td>'.$workout->getAttributeLabel('ascent').'</td>';
                    echo '<th>'.$workout->ascent.'<span class="units"> m</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->max_altitude != 0 && $workout->max_altitude != null){
                    echo '<td>'.$workout->getAttributeLabel('max_altitude').'</td>';
                    echo '<th>'.$workout->max_altitude.'<span class="units"> m</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->min_hr != 0 && $workout->min_hr != null){
                    echo '<td>'.Yii::t('diary','Min heart rate').'</td>';
                    echo '<th>'.$workout->min_hr.'<span class="units"> bpm</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->avg_hr != 0 && $workout->avg_hr != null){
                    echo '<td>'.Yii::t('diary','Avg heart rate').'</td>';
                    echo '<th>'.$workout->avg_hr.'<span class="units"> bpm</span></th>';
                    $i++; $controll = 0;
                }
                
                if($i%2 === 0 && $controll === 0){echo '</tr><tr>'; $controll = 1;};
                
                if($workout->max_hr != 0 && $workout->max_hr != null){
                    echo '<td>'.Yii::t('diary','Max heart rate').'</td>';
                    echo '<th>'.$workout->max_hr.'<span class="units"> bpm</span></th>';
                }
                echo '</tr>';
                if($workout->description != '' && $workout->description != null){
                    echo '<tr style="height:20px"></tr>';
                    echo '<tr><th style="font-size:150%">'.$workout->getAttributeLabel('description').'</th></tr>';
                    echo '<tr><td colspan="4">'.$workout->description.'</td></tr>';
                }
               
            echo '</tbody></table>'

        ?>
        
    </div>
    
    <div style="clear:both"></div>
    <div id="Workout-comments" class="form">
        
        <div class="float-left" id="comments-container" <?php if(!Yii::app()->user->isGuest){ echo 'style="min-height:135px"';}?>>
            <div id="Workout-comment-header" <?php if($allComments == null){ echo 'style="display:none"';}?>>
                <?php 
                    echo Yii::t('dashboard', 'Comments');
                    echo CHtml::activeHiddenField($workout, 'id');
               ?>
                <div class="float-right"><a id="load-comments"><?php echo Yii::t('dashboard','View older comments')?></a></div>
            </div>
            <?php
                foreach ($allComments as $singlecomment){
                    echo '<div class="Workout-single-comment" id="'.$singlecomment->id.'">';
                    if($singlecomment->profile_picture != '' && ($pic = $this->getPicture($singlecomment->profile_picture, 'profile-picture'))!= null):
                        echo '<img class="Workout-comment-picture" src="'.$pic.'" height="35"/>';
                    else:
                        echo '<img class="Workout-comment-picture" src="'.Yii::app()->request->baseUrl.'/images/photo-default-'.$singlecomment->gender.'.png" height="35"/>';
                    endif;
                    echo '<span class="bname"><a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.
                            '/user/view/'.$singlecomment->username.'">'.$singlecomment->fullname.
                            '</a></span><span class="Workout-comment-date">'.
                            date('G:i, d. m. Y',strtotime($singlecomment->date)).'</span><br />';
                    echo $singlecomment->text;
                    echo '</div>';
                }
            ?>
        </div>
        
        <?php if(!Yii::app()->user->isGuest):?>
            <div id="new-comment">
                <?php
                    echo CHtml::activeLabel($comment, 'text');
                    echo CHtml::activeTextArea($comment, 'text',array('cols'=>40,'rows'=>3));
                    echo CHtml::activeHiddenField($comment,'id_trainingentry');
                ?> 
                <div class="float-left" style="font-size:80%"> 
                    <?php echo CHtml::activeLabel($comment,'id_visibility')?>
                    <input type="radio" name="Comment[id_visibility]" id="Comment_id_visibility" value="1" checked="checked"/><?php echo Yii::t('global','Public')?>
                    <input type="radio" name="Comment[id_visibility]" id="Comment_id_visibility" value="2"/><?php echo Yii::t('global','Friends')?>
                    <input type="radio" name="Comment[id_visibility]" id="Comment_id_visibility" value="3"/><?php echo Yii::t('global','For author')?>
                </div>
                <a id="submit-comment" class="styled-button" style="width: 110px"><?php echo Yii::t('dashboard', 'Comment')?></a>
            </div>
        <?php endif;?>
    </div>
</div>
<div id="Workout-other-workouts">
    
    <?php 
        if($sharedworkouts == null):
            if(Yii::app()->user->id == $user->id){
                echo '<h3>'.Yii::t('dashboard','Your other shared workouts').'</h3>';
                echo '<h4 class="note">'.Yii::t('dashboard','Currently you have no other shared workouts').'</h4>';
            }
            else{
                echo '<h3>'.Yii::t('dashboard','Other workouts from ').$user->name.'</h3>';
                echo '<h4 class="note">'.$user->name.' '.Yii::t('dashboard','currently doesn\'t share any workouts with you').'</h4>';
            }
    ?>
    
    <div class="noworkouts" id="<?php echo $user->gender?>"></div>
    <?php else:
            if(Yii::app()->user->id == $user->id)
                echo '<h3>'.Yii::t('dashboard','Your other shared workouts').'</h3>';
            else
                echo '<h3>'.Yii::t('dashboard','Other workouts from ').$user->name.'</h3>';
        foreach ($sharedworkouts as $entry){
            $this->createSharedEntryDiv($entry,$activity_array);
        }
    endif;?>
</div>

<?php 
Yii::app()->clientScript->registerScript('comments-handler',
    '$(document).ready(function(){
        $("#Comment_text").focus();
        
        markComments();
        
        $("a#submit-comment").click(function(){
            text = $("#Comment_text").val();
            visibility = $("#Comment_id_visibility").val();
            trainingentry = $("#Comment_id_trainingentry").val();
            $("#Comment_text").val("");
            
            if(text.length <= 300){
                $.ajax({
                    type: "POST",
                    url: "'.Yii::app()->createAbsoluteUrl('workout/addcomment').'",
                    data: {text: text, id_visibility: visibility, id_trainingentry: trainingentry},
                    success: function(data, status){
                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            validation = xmlDocumentElement.getElementsByTagName("validation");
                            
                            if(validation.item(0).firstChild.data === "ok"){
                                
                                response = parseComments(xmlDocumentElement);
                                $(response).hide().appendTo("#comments-container").fadeIn();
                                $("#Workout-comment-header").show();
                            }
                            else{
                                alert("'.Yii::t('error','Error occured during your request').'");
                            }
                        }catch(e){
                            alert("'.Yii::t('error','Error occured during your request').'");
                        }
                    }
                });
            }
            else{
                alert("'.Yii::t('error','Your text is too long. Maximum message length is 300 characters').'");            
            }
        });
        
        $("a#load-comments").click(function(){
            comments = $("#comments-container").find(".Workout-single-comment");
            id_entry = $("#TrainingEntry_id").val();
            $.ajax({
                type: "POST",
                url: "'.Yii::app()->createAbsoluteUrl('workout/loadcomments').'",
                data: {offset: comments.length, id_entry: id_entry},
                success: function(data, status){
                    try{
                        xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                        
                        response = parseComments(xmlDocumentElement);
                        $(response).hide().insertBefore($(".Workout-single-comment").first()).slideDown();

                    }catch(e){
                        alert("'.Yii::t('error','Error occured during your request').'");
                    }
                    markComments();
                }
            });
        });
    });',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('parse-comments',
    'function parseComments(xmlRoot){
        var response = "";
        
        id_a = xmlRoot.getElementsByTagName("id");
        fullname_a = xmlRoot.getElementsByTagName("fullname");
        link_a = xmlRoot.getElementsByTagName("link");
        picture_a = xmlRoot.getElementsByTagName("picture");
        text_a = xmlRoot.getElementsByTagName("text");
        date_a = xmlRoot.getElementsByTagName("date");
            
        for(i=0; i<fullname_a.length; i++){
            id = id_a.item(i).firstChild.data;
            fullname = fullname_a.item(i).firstChild.data;
            link = link_a.item(i).firstChild.data;
            picture = picture_a.item(i).firstChild.data;;
            text = text_a.item(i).firstChild.data;
            date = date_a.item(i).firstChild.data;

            response += "<div class=\"Workout-single-comment\" id=\""+id+"\">";
            response += "<img class=\"Workout-comment-picture\" src=\""+picture+"\" height=\"35\"/>";
            response += "<span class=\"bname\"><a href=\""+link+"\">"+fullname+"</a></span>";
            response += "<span class=\"Workout-comment-date\">"+date+"</span><br />";
            response += text + "</div>";
        }
        return response;
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('mark-comments',
    'function markComments(){
        id_training = document.URL;
        id_training = id_training.split("/",30);

        var array_id = [];
        $(".Workout-single-comment").each(function(){ array_id.push(this.id); });

        $.ajax({
            type: "POST",
            url: "'.Yii::app()->createAbsoluteUrl('workout/markcomments').'",
            data: {array_id: array_id, id_training: id_training[id_training.length-1]},
            success: function(data, status){
                loadNotices();
            }
        });
        
    }',
CClientScript::POS_END);
?>
    