<?php
/* @var $this DashboardController */
?>

<h2 class="flying"><?php echo Yii::t('global','Dashboard')?></h2>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/dashboard.png" height="42" width="350"></div>

<div id="Dashboard-news">
    <h3><?php echo Yii::t('dashboard','Latest activity of your friends')?></h3>
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
<div id="Dashboard-left-data">
    <div id="Dashboard-quick-plan" class="classic-box">
        <?php
            echo '<h3>'.Yii::t('dashboard','Nearest training plan').'</h3>';

                $z = 1;
                $temp_old_date = '';

                //Vyskladanie diviek pre jednotlive dni podla zaznamov z databazy
                foreach ($plan as $day){

                    if($temp_old_date == $day->date){ //viac zaznamov pre jeden den
                        $daily_plan[$day->date] .= '</a><a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/plan"
                            class="single-entry" id="'.$day->id.'">';
                    }else {
                        if($temp_old_date != '')
                            $daily_plan[$temp_old_date] .= '</a>';
                        $daily_plan[$day->date] = '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/plan" 
                            class="single-entry" id="'.$day->id.'">';
                    }

                    if(isset($activity_array[$day->id_activity])){
                        $daily_plan[$day->date] .= '<div class="single-day-activity" id="activity-'.$day->id_activity.'">'.
                                Yii::t('activity',$activity_array[$day->id_activity]->name).'</div>';
                    }
                    $daily_plan[$day->date] .= '<div class="single-day-duration">'.date ('H:i',strtotime($day->duration)).'</div>';

                    $daily_plan[$day->date] .= '<div style="clear:both"></div>';

                    $daily_plan[$day->date] .= '<div class="single-day-description">'.substr($day->description, 0, 50);
                    if(strlen($day->description) > 50){
                        $daily_plan[$day->date] .= '...';
                    }
                    $daily_plan[$day->date] .= '</div>';
                    $temp_old_date = $day->date;

                    if(count($plan) == $z){ //ukoncenie posledneho zaznamu
                        $daily_plan[$day->date] .= '</a>';
                    }
                    $z++;
                }
                
                //Zobrazenie 3 dni
                for($j=0; $j<3; $j++): 

                    $add_days = '+'.$j.' days';
                    if($j == 0)
                        $date = Yii::t ('calendar', 'Tomorrow');
                    else
                        $date = date('d. m. Y', strtotime($add_days, strtotime($first_date)));
                    $date_to_compare = date('Y-m-d', strtotime($add_days, strtotime($first_date)));

                    echo '<div class="DB-quick-day">';
                    echo '<div class="DB-quick-date">'.$date.'</div>';
                    echo '<div style="clear:both"></div>';
                    if(isset($daily_plan[$date_to_compare])){
                        echo $daily_plan[$date_to_compare];
                    }
                    else{
                        echo '<div class="flash-notice">'.Yii::t('plan','Nothing planed for this day.').'</div>';
                    }

                    echo '</div>';
                endfor; 
            ?>
    </div><!--END OF Quick Plan-->
    <?php /*

    <div id="Dashboard-quick-trainings" class="classic-box">
        <h3><?php echo Yii::t('dashboard','Recent trainings')?></h3>
        <div class="DB-quick-inner">
            <?php
                if($entries != null){
                    foreach ($entries as $entry){
                        $i = 0;
                        echo '<div class="DB-quick-day">';
                        echo '<div class="DB-quick-date">'.date('d. m. Y',  strtotime($entry->date)).'</div>';
                            echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/diary" 
                            class="single-entry" id="'.$entry->id.'">';
                            if(isset($activity_array[$entry->id_activity])){
                                echo '<div class="single-day-activity" id="activity-'.$entry->id_activity.'">'.
                                        Yii::t('activity',$activity_array[$entry->id_activity]->name).'</div>';
                            }
                            echo '<div class="single-day-duration">'.date ('H:i',strtotime($entry->duration)).'</div>';
                            echo '<div style="clear:both; margin:2px;"></div>';    
                            if($entry->distance != 0){
                                echo '<div class="entry-data-wr"><span class="data-label">'.Yii::t('diary','Distance').'</span><br/>'.
                                        '<span class="entry-data">'.number_format($entry->distance, 1, '.', '').'</span> km</div>';
                                $i++;
                            }
                            if($entry->avg_hr != null || $entry->max_hr != null){
                                echo '<div class="entry-data-wr"><span class="data-label">'.Yii::t('diary','Heart rate').'</span><br/>'.'<span class="entry-data">';
                                echo $entry->avg_hr? $entry->avg_hr : '--';
                                echo '/';
                                echo $entry->max_hr? $entry->max_hr : '--';
                                echo '</span> '.Yii::t('diary','bpm').'</div>';
                                $i++;
                            }
                            if($i<=1 && $entry->avg_speed != 0){
                                echo '<div class="entry-data-wr"><span class="data-label">'.Yii::t('diary','Average Speed').'</span><br/>'.
                                        '<span class="entry-data">'.number_format($entry->avg_speed, 1, '.', '').'</span> km/h</div>';
                                $i++;
                            }
                            elseif ($i<=1 && $entry->avg_pace != '00:00:00' && $entry->avg_pace != null){
                                echo '<div class="entry-data-wr"><span class="data-label">'.Yii::t('diary','Average Pace').'</span><br/>'.
                                        '<span class="entry-data">'.date ('i:s',strtotime($entry->avg_pace)).'</span> min/km</div>';
                                $i++;
                            }
                            
                        echo '</a></div>';
                    }
                }
                else {
                    echo '<div class="flash-notice">'.Yii::t('dashboard','You haven\'t  completed any workouts yet. Start to train and share your improvement.').'</div>';
                }
            ?>
            
        </div>
    </div><!--END OF Quick Entries-->*/?>
    <div id="Dashboard-quick-stats" class="classic-box">
        <h3><?php echo Yii::t('dashboard','Simple stats')?></h3>
        <div class="DB-quick-inner" style="text-align: center;overflow: auto">
            <img src="<?php echo Yii::app()->request->baseUrl?>/images/css/loader.gif"/>
            
            <div id="stats-req-resp" style="display: none">
                <div style="font-size: 120%; float: left; margin: 5px 5px 5px 30px">
                <?php echo '<i>'.Yii::t('dashboard','Date between ').'</i>';
                    echo $this->widget('zii.widgets.jui.CJuiDatePicker', array('name'=>'datefrom','id'=>'datefrom','options'=>array('dateFormat'=>'yy-mm-dd'),'language'=>Yii::app()->language, 'htmlOptions'=>array('size'=>'10')), true); ?>
                <?php echo '<i>'.Yii::t('dashboard',' and ').'</i>';
                    echo $this->widget('zii.widgets.jui.CJuiDatePicker', array('name'=>'datefrom','id'=>'dateto','options'=>array('dateFormat'=>'yy-mm-dd'),'language'=>Yii::app()->language,'htmlOptions'=>array('size'=>'10')), true); ?>
                </div><a id="load-quick-stats" class="styled-button" style="float: right; margin-right: 30px"><?php echo Yii::t('dashboard', 'Display')?></a>
                <div style="clear: both"></div>
                <div id="quick-stats-resp">
                </div>
            </div>
        </div>
    </div>
    
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