<?php
/* @var $this DashboardController */
?>

<h2 class="flying"><?php echo Yii::t('global','Dashboard')?></h2>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/dashboard.png" height="42" width="350"></div>

<div id="Dashboard-news">
    <h3><?php echo Yii::t('dashboard','Latest activity of athletes')?></h3>
    <?php
    if($allSharedWorkouts != null){
        $this->createLargeSharedEntries($allSharedWorkouts,$activity_array);
    }
    else{
        echo '<div style="text-align:center"><h4 class="note">'.Yii::t('dashboard','Currently there is nothing to display.').'</h4>';
        echo '<img src="'.Yii::app()->request->baseUrl.'/images/siluets-body.png" width="235" height="250"></div>';
    }
    ?>
</div>

<div id="Dashboard-left-data" style="height: 575px">
    <h3 style="text-align: center"><?php echo Yii::t('dashboard', 'Your athletes')?></h3>
    <?php
        foreach ($athletes as $athlete):?>
            <div class="coach-DB-athlete" title="View Profile" href="<?php echo Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/view/'.$athlete->username;?>">
            <?php if($athlete->profile_picture != '' && ($pic = $this->getPicture($athlete->profile_picture, 'profile-picture'))!= null):?>
                <img class="response_picture" src="<?php echo $pic; ?>" height="75"/>
            <?php else:?>
                <img class="response_picture" src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$athlete->gender.'.png'?>" height="75"/>
            <?php endif;?>
           
            <?php echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/view/'.$athlete->username.'"><span class="fullname">'.$athlete->fullname.'</span></a>'; ?> 
            
            <?php 
                if($athlete->id_primary_activity != '' || $athlete->id_primary_activity != null){
                    echo  '<span class="people-activity"> - '.Yii::t('activity',$activity_array[$athlete->id_primary_activity]->name).'</span>';
                }
            ?>
            <br /><br />
            
            <?php echo '<a class="coach-DB-operation-links" href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/plan/?userid='.$athlete->id.'">'.Yii::t('dashboard', 'Show training plan').'</a>'?>
            <?php echo '<a class="coach-DB-operation-links" href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/diary/?userid='.$athlete->id.'">'.Yii::t('dashboard', 'Show training diary').'</a>'?>
        </div>
        <?php endforeach;?>
</div>

<?php
Yii::app()->clientScript->registerScript('load-news',
    '$(document).ready(function(){
        processing = false;
        $(window).scroll(function () { 
            
            adjustQuickPanelPosition();
            
            if ($(window).scrollTop() >= ($(document).height() - $(window).height())*0.75) {                
                offset = $(".shared-entry").length;
                if(!processing){
                    processing = true;
                    $.ajax({
                        type: "GET",
                        url: "'.Yii::app()->createAbsoluteUrl('dashboard/loadnews').'",
                        data: {offset: offset},
                        success: function(data, status){
                            try{
                                $(data).hide().insertAfter($(".shared-entry").last()).fadeIn(1200);
                                adjustQuickPanelPosition();
                                processing = false;
                            }catch(e){
                                processing = false;
                            }
                        }
                    });
                }
                else{
                    return false;
                }
            }
         });
    });
        
    function adjustQuickPanelPosition(){
        if ($(window).scrollTop() < 162){ //poloha takmer na vrchu dokumentu
            $("#Dashboard-left-data").removeClass("DB-pos-fixed DB-pos-absolute").removeAttr("style").css({position:"static"});
        }
        else if ($(window).scrollTop() >= 162 && ($(document).height()-$(document).scrollTop()>=885)){ //poloha uprostred
            left = $("#Dashboard-left-data").offset().left;
            width = $("#Dashboard-left-data").width();
            $("#Dashboard-left-data").removeClass("DB-pos-absolute").addClass("DB-pos-fixed").removeAttr("style");
            $("#Dashboard-left-data").css({width: width, left: left});
        }
        else if(($(document).height()-$(document).scrollTop()<885)){ //poloha takmer na spodku dokumentu
            width = $("#Dashboard-left-data").width();
            $("#Dashboard-left-data").removeClass("DB-pos-fixed").addClass("DB-pos-absolute").removeAttr("style").css({width: width});
        }
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('load-stats',
        '$(document).ready(function(){
            processing_stats = false;
            loadstats("'.date('Y-m-d', strtotime('-30 days',strtotime(date('Y-m-d')))).'","'.date('Y-m-d').'");
            
            $("#load-quick-stats").click(function(){
                datefrom = $("#datefrom").val();
                dateto = $("#dateto").val();
                loadstats(datefrom,dateto);
            });

            function loadstats(datefrom,dateto){
                if(!processing_stats){
                    processing_stats = true;
                    $.ajax({
                        type: "GET",
                        url: "'.Yii::app()->createAbsoluteUrl('dashboard/loadstats').'",
                        data: {datefrom: datefrom, dateto: dateto},
                        success: function(data, status){
                            try{
                                $("#Dashboard-quick-stats").find(".DB-quick-inner").find("img").hide();
                                $("#quick-stats-resp").html("");
                                $("#stats-req-resp").show();
                                $(data).hide().appendTo("#quick-stats-resp").fadeIn();
                                processing_stats = false;
                            }catch(e){
                                processing_stats = false;
                            }
                        }
                    }); 
                } 
                else{
                    return false;
                }
           }
        });',
        CClientScript::POS_END);
?>