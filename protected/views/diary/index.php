<?php
/**
 * @var $this DiaryController
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/diary.png" height="42" width="177"></div>
<h2 class="flying"><?php echo Yii::t('global','Diary')?></h2>

<?php Yii::app()->clientScript->registerCssFile(Yii::app()->params->homeUrl.'/css/print.css', 'print')?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->params->homeUrl.'/protected/extensions/colorpicker/css/colorpicker.css', 'print')?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->params->homeUrl.'/protected/extensions/colorpicker/js/colorpicker.js', CClientScript::POS_HEAD)?>

<?php
    $workout_link = '';
    if($singleEntry->id != null || $singleEntry->id != '' || $singleEntry->hasErrors() || Yii::app()->user->hasFlash('entry-not-saved')){
        Yii::app()->clientScript->registerScript('show-fl-input-area-on-start','
            $(document).ready(function(){
                $("#DiaryInput-preloader").hide();
                $("#DiaryInput-form").show();
                $(".fl-input-area-bg").show();
                $(".fl-input-area").css({top:"400px", opacity:"1"});
            });
            ',CClientScript::POS_END);
        if($singleEntry->id != null || $singleEntry->id != ''){
            $workout_link = '<a id="diary-to-workout-link" target="_blank" href="'.Yii::app()->params->homePath.
                    '/'.Yii::app()->language.'/workout/view/'.$singleEntry->id.'">'.Yii::t('diary', 'View workout in details').'</a>';
        }
    }

    if(Yii::app()->user->hasFlash('training-entry-deleted')){
        echo '<div class="flash-error">'.Yii::app()->user->getFlash('training-entry-deleted').'</div>';
         Yii::app()->clientScript->registerScript('hide-delete-flash','
            $(document).ready(function(){
                $(".flash-error").delay(3000).fadeOut();
            });
            ',CClientScript::POS_END);
    }
?>

<div class="fl-input-area-bg"></div>
<div class="fl-input-area" id="TrainigDiary-input">
    <div class="preloader" id="DiaryInput-preloader"></div>
    <div class="form" id="DiaryInput-form">
        <div class="DiaryInput-header"><h3 class="float:left">
                <?php echo Yii::t('diary','Saving training entry for ')?><i>
                <?php
                    echo $this->days[date('w',strtotime($singleEntry->date))].' ';
                    echo date('d. m. Y',  strtotime($singleEntry->date));
                ?></i>
                <?php echo $workout_link?></h3>
        </div>
        <?php 
            if(Yii::app()->user->hasFlash('entry-saved')){
                echo '<div class="flash-success">'.Yii::app()->user->getFlash('entry-saved').'</div>';
            }
            if(Yii::app()->user->hasFlash('entry-not-saved')){
                echo '<div class="flash-error">'.Yii::app()->user->getFlash('entry-not-saved').'</div>';
            }
            echo CHtml::beginForm('','post');
            echo Chtml::errorSummary(array($singleEntry)); 
        ?>
        
        <div id="DiaryInput-leftData"> 
            <div id="di-left-activity_time">
                <div class="float-left"> 
                        <?php echo CHtml::activeLabel($singleEntry,'id_activity')?>
                        <?php echo CHtml::activeDropDownList($singleEntry,'id_activity', $activity,array('prompt'=>''));?>
                        <?php echo CHtml::activeHiddenField($singleEntry,'date');?>
                        <?php echo CHtml::activeHiddenField($singleEntry,'id');?>
                </div>
                <div class="float-left"> 
                    <?php 
                        echo CHtml::activeLabel($singleEntry,'duration');
                        $time = explode(":", $singleEntry->duration);        
                    ?>
                    <b><i>HH: </b></i><input class="input-trainig-time" type="text" size="3" maxlength="2" id="TrainingEntry_hours" name="TrainingEntry[hours]" value="<?php echo $time[0]?>">
                    <b><i>MM: </b></i><input class="input-trainig-time" type="text" size="3" maxlength="2" id="TrainingEntry_minutes" name="TrainingEntry[minutes]" value="<?php echo $time[1]?>">
                </div>

                <div style="clear:both"></div>
            </div>
            
            <div id="di-left-otherdata">
                <table>
                    <tbody>
                        <tr><td colspan="3"><label for=""><?php echo Yii::t('diary','Heart rate')?></label></td></tr>
                        <tr>
                            <td><?php echo '<b><i>Min: </b></i>'.CHtml::activeTextField($singleEntry,'min_hr',array('size'=>'5', 'maxlength'=>'3'))?></td>
                            <td><?php echo '<b><i>Avg: </b></i>'.CHtml::activeTextField($singleEntry,'avg_hr',array('size'=>'5', 'maxlength'=>'3'))?></td>
                            <td><?php echo '<b><i>Max: </b></i>'.CHtml::activeTextField($singleEntry,'max_hr',array('size'=>'5', 'maxlength'=>'3'))?></td>
                        </tr>
                        <tr class="TrainingEntry-dist_speed">
                            <td><?php echo CHtml::activeLabel($singleEntry,'distance')?></td>
                            <td><?php echo CHtml::activeLabel($singleEntry,'avg_speed')?></td>
                            <td><?php echo CHtml::activeLabel($singleEntry,'avg_pace')?></td>
                        </tr>
                        <tr class="TrainingEntry-dist_speed">
                            <td><?php echo CHtml::activeTextField($singleEntry,'distance',array('size'=>'5')).'<span class="units"> km</span>'?></td>
                            <td><?php echo CHtml::activeTextField($singleEntry,'avg_speed',array('size'=>'5')).'<span class="units"> km/h</span>'?></td>
                            <td><?php echo CHtml::activeTextField($singleEntry,'avg_pace',array('size'=>'5')).'<span class="units"> min/km</span>'?></td>
                        </tr>
                        <tr class="TrainingEntry-watts">
                            <td><?php echo CHtml::activeLabel($singleEntry,'avg_watts')?></td>
                            <td><?php echo CHtml::activeLabel($singleEntry,'max_watts')?></td>
                        </tr>
                        <tr class="TrainingEntry-watts">
                            <td><?php echo CHtml::activeTextField($singleEntry,'avg_watts',array('size'=>'5')).'<span class="units"> W</span>'?></td>
                            <td><?php echo CHtml::activeTextField($singleEntry,'max_watts',array('size'=>'5')).'<span class="units"> W</span>'?></td>
                        </tr>
                        <tr class="TrainingEntry-altitude">
                            <td><?php echo CHtml::activeLabel($singleEntry,'ascent')?></td>
                            <td><?php echo CHtml::activeLabel($singleEntry,'max_altitude')?></td>
                        </tr>
                        <tr class="TrainingEntry-altitude">
                            <td><?php echo CHtml::activeTextField($singleEntry,'ascent',array('size'=>'5')).'<span class="units"> m</span>'?></td>
                            <td><?php echo CHtml::activeTextField($singleEntry,'max_altitude',array('size'=>'5')).'<span class="units"> m</span>'?></td>
                        </tr>
                        <tr>
                            <td><?php echo CHtml::activeLabel($singleEntry,'feelings')?></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="TrainingEntry-feelings feelings-selected" id="feelings1"></div><div class="TrainingEntry-feelings" id="feelings2"></div>
                                <div class="TrainingEntry-feelings" id="feelings3"></div><div class="TrainingEntry-feelings" id="feelings4"></div>
                                <?php echo CHtml::activeHiddenField($singleEntry,'feelings')?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                  
                <div style="clear:both"></div>

                <div class="row"> 
                    <?php echo CHtml::activeLabel($singleEntry,'description')?>
                    <?php echo CHtml::activeTextArea($singleEntry,'description', array('cols'=>34,'rows'=>4))?>
                </div>

                <div class="row"> 
                    <?php echo CHtml::activeLabel($singleEntry,'id_visibility')?>
                    <input type="radio" name="TrainingEntry[id_visibility]" id="TrainingEntry_id_visibility" value="1"/><?php echo Yii::t('global','Public')?>
                    <input type="radio" name="TrainingEntry[id_visibility]" id="TrainingEntry_id_visibility" value="2"/><?php echo Yii::t('global','Friends')?>
                    <input type="radio" name="TrainingEntry[id_visibility]" id="TrainingEntry_id_visibility" value="3" checked="checked"/><?php echo Yii::t('global','Secret')?>
                </div>
            </div>
        </div>
        
        <div id="DiaryInput-rightData">
            <div id="di-right-zones">
                <h4 class="hr_zones-input"><?php echo Yii::t('diary', 'HR Zone Time')?></h4>
                <table class="hr_zones-input">
                    <thead>
                        <tr>
                            <th></th>
                            <th>%</th>
                            <th><?php echo Yii::t('global', 'Time');?> (HH:MM)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 0;
                        foreach ($hr_zones as $zone){
                            echo '<tr>';
                              echo '<th>'.$zone->name.'</th>';
                              echo '<td>'.CHtml::activeTextField($zone_time[$i], 'p_time',array('class'=>'zone_time-percentage','size'=>'4', 'maxlength'=>'3','name'=>'ZoneTime['.$zone->id.']')).'</td>';
                              echo '<td><input class="zone_time-time" type="text" size="6" maxlength="5"></td>';
                              echo '<td>'.$zone->min.' - '.$zone->max.'</td>';
                            echo '</tr>';
                            $i++;
                        }
                    ?>
                    </tbody>    
                </table> 
                <div class="flash-notice" id="training_time_notice">
                    <?php echo Yii::t('diary','Assigning values to <b>Duration</b> helps you calculate zones time.')?>
                </div>
            </div>
            <div id="di-right-labels">
                <?php echo CHtml::activeLabel($singleEntry,'id_label')?>
                <table id="TrainingPlan-labels">
                    <tbody>
                        <tr id="cr-nw-label-input">
                            <td colspan="2">
                                <?php echo CHtml::activeLabel($newlabel,'name')?>
                                <?php echo CHtml::activeTextField($newlabel,'name', array('size'=>'10'))?>
                            </td>
                            <td>
                                <?php echo CHtml::activeLabel($newlabel,'color')?>
                                <?php echo CHtml::activeTextField($newlabel,'color', array('class'=>'label-color-prev'))?>
                                <?php echo CHtml::activeHiddenField($newlabel,'id');?>
                                <input type="checkbox" name="Label[editcontrol]" id="edit-control" style="display: none" value="1"/>
                            </td>
                            <td>
                                <input type="submit" value="<?php echo Yii::t('global','Create')?>" class="small-button" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <a class="styled-button" id="create-new-label"><?php echo Yii::t('global','Create new')?></a>
                            </td>
                        </tr>
                        <?php
                            foreach ($labels as $label){
                                echo '<tr id="'.$label->id.'">';
                                    echo '<th>';
                                        echo '<input type="radio" name="TrainingPlan[id_label]" id="TrainingEntry_id_label" value="'.$label->id.'"/>';
                                    echo '</th>';
                                    echo '<td>';
                                        echo Yii::t('plan', $label->name);
                                    echo '</td>';
                                    echo '<td class="label-color">';
                                        echo '<div class="label-color-prev" style="background-color: #'.$label->color.'"></div>';
                                    echo '</td>';
                                    echo '<td>';
                                        echo '<a class="update-label styled-button small-button">'.Yii::t('global','Update').'</a>';
                                    echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                        <tr>
                            <th><input type="radio" name="TrainingPlan[id_label]" id="TrainingEntry_id_label" value="" checked="checked"/></th>
                            <td><?php echo Yii::t('plan', 'Without label')?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="submit-button" >
            <input type="submit" value="<?php echo Yii::t('global','Save')?>" class="big-button" id="DiarySave-button" title="Save this entry into training diary" style="margin: auto;" />
        </div>
        <?php echo CHtml::endForm();?>
            
    </div>
</div>

<?php
    $monday = date('Y-m-d',strtotime( "Monday this week",strtotime($request_date)));
    
    $workouts = $this->createDailyEntries($diary, $labels, $activity_array);
    
?>

<a class="styled-button wide-button" id="previous-week" style="float:left; width: 185px;"><?php echo Yii::t('diary','Show previous week')?></a>
<div class="date-chooser"><?php echo Yii::t('plan','or choose a date')?>
    <?php 
        echo $this->widget('zii.widgets.jui.CJuiDatePicker', array('name'=>'chooseDate','options'=>array('dateFormat'=>'yy-mm-dd'),'language'=>Yii::app()->language), true); 
    ?>
</div>
<a class="styled-button wide-button" id="date-chooser-submit" style="float:left"><?php echo Yii::t('diary','Display this date')?></a>

<?php 
    for($i=0; $i<2; $i++):
        
        echo '<div class="single-week">';
    
            for($j=1; $j<=7; $j++): 
                
                $add_days = '+'.(($i*7+$j)-1).' days';
                $date = Date('Y-m-d', strtotime($add_days, strtotime($monday)));
                $single_day_date = '<div class="single-day-date" id="'.date('Y-m-d',strtotime($date)).'">'.
                            '<div class="day-part">'.$this->days[date('w',strtotime($date))].'</div>'.
                            '<div class="date-part">'.date('d. m. Y',strtotime($date)).'</div></div>';
                
                echo '<div class="single-day-full" id='.$date.'>';
                echo $single_day_date;
                echo '<div style="clear:both"></div>';
                
                if(isset($workouts[$date])){
                    echo $workouts[$date];
                }
                
                echo '</div>';
                
                endfor; 

                echo '<div class="classic-box week-summary">';
                echo '<h3>'.Yii::t('diary','Total').':</h3>';
                    echo '<div class="inner-week-summary">';
                    $sumtotdays = 0;
                    $sumtothours = strtotime('00:00:00');
                    echo '<table>';
                    foreach($summary[$i] as $value){
                        
                        echo '<tr><td>'.Yii::t('activity',$activity_array[$value->id_activity]->name).' ('.$value->totworkouts.'): </td>';
                        echo '<th>'.$value->tottime.'</th></tr>';
                        $sumtotdays += $value->totworkouts;
                        $sumtothours += strtotime($value->tottime);
                    }
                    echo '</table>';
                    echo '<hr>';
                    echo '<div style="font-size:110%; font-weight: bold">';
                    echo Yii::t('diary','Total workouts completed').': <div class="float-right">'.$sumtotdays.'</div><br />';
                    echo Yii::t('diary','Hours completed').': <div class="float-right">'.date('H:i:s',$sumtothours).'</div><br />';
                    echo '</div>';
            
                echo '</div></div><div style="clear: both"></div></div> ';  

    endfor; 
?>
    
<a class="styled-button wide-button" id="next-week" style="margin-top: 10px; width: 185px;"><?php echo Yii::t('diary','Show next week')?></a>

<?php    
Yii::app()->clientScript->registerScript('labels-handler',
    'var labelsTable = $("#TrainingPlan-labels").find("tbody").html();
     $(document).ready(function(){
        $(document).on("click","#create-new-label",function(){
            if(labelsTable != ""){
                $("#TrainingPlan-labels").find("tbody").html(labelsTable);
            }
            $("#create-new-label").hide();
            $("#cr-nw-label-input").show();
            $("#edit-control").attr({checked: "checked"});
        });
        $(document).on("click",".update-label",function(){
            input_html = $("#cr-nw-label-input").html();
            $("#create-new-label").show();
            $("#cr-nw-label-input").hide();
            $target_row = $(this).parent().parent();
            
            label_name = $target_row.children().get(1).firstChild.data;
            label_color = $target_row.find(".label-color").find("div").attr("style");
            hex = label_color.split("#",2);
            label_color = hex[1];
            label_id = $target_row.attr("id");
            
            $target_row.html("").append(input_html);
            
            $target_row.find("#Label_name").val(label_name);
            $target_row.find("#Label_color").val(label_color);
            $target_row.find("#Label_color").css({"color":"#"+label_color, "background":"#"+label_color});
            $target_row.find("#Label_id").val(label_id);
            $("#edit-control").attr({checked: "checked"});
        });
    });',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('handle-input-visibility',
    '$(document).ready(function(){
        handle_input_visibility($("#TrainingEntry_id_activity").val());
        $("#TrainingEntry_id_activity").change(function (){
            handle_input_visibility($(this).val());
        });
    });
    
    function handle_input_visibility(selected){
        $(".TrainingEntry-altitude").show();
        $(".TrainingEntry-dist_speed").show();
        $(".TrainingEntry-watts").hide();
        if(selected === "1"){
                $(".TrainingEntry-watts").show();
        }
        else if(selected === "4" || selected === "5"){
            $(".TrainingEntry-watts").hide();
            $(".TrainingEntry-altitude").hide();
        }else if(selected === "7"){
            $(".TrainingEntry-watts").hide();
            $(".TrainingEntry-altitude").hide();
            $(".TrainingEntry-dist_speed").hide();
        }
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('addprev-week',
    'function addPrevWeek(date){
        $.ajax({
            type: "GET",
            url: "'.Yii::app()->createAbsoluteUrl('diary/addweek').'",
            data: {date: date, offset: "-7"},
            success: function(data, status){
                try{
                    $(data).hide().insertBefore($(".single-week").first()).slideDown();
                }catch(e){
                }
            }
        });
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('addnext-week',
    'function addNextWeek(date){
        $.ajax({
            type: "GET",
            url: "'.Yii::app()->createAbsoluteUrl('diary/addweek').'",
            data: {date: date, offset: "1"},
            success: function(data, status){
                try{
                    $(data).hide().insertAfter($(".single-week").last()).slideDown();
                }catch(e){
                }
            }
        });
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('calendar-handler',
    '$(document).ready(function(){
        
        $("#date-chooser-submit").click(function(){
            val = $("#chooseDate").val();
            window.location.assign("'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/diary/index/"+val);
        });
        
        $(document).on("click",".single-day-full",function(){
            show_fl_input_create($(this));
        });
        
        $(".fl-input-area-bg").click(function(){
            $(".fl-input-area-bg").fadeOut("fast");
            $(".fl-input-area").animate({top:"-1000px", opacity:"0"});
            $("#DiaryInput-preloader").show();
            $("#DiaryInput-form").hide();
        });
        
        $(document).on("click",".single-entry",function(event){
            $(".form").find(".flash-success").hide();
            show_fl_input_update($(this));
            event.stopPropagation();
        });

        $(document).on("mouseover",".single-entry",
            function(){
                $(this).find(".single-day-delete").html("X");
        });
        $(document).on("mouseleave",".single-entry",
            function(){
                $(this).find(".single-day-delete").html("");
        });
        
        $("#previous-week").click(function(event){
            date = $(".single-week").first().find(".single-day-full").attr("id");
            addPrevWeek(date);
        });
        
        $("#next-week").click(function(event){
            date = $(".single-week").last().find(".single-day-full").last().attr("id");
            addNextWeek(date);
        });
        
        $(document).on("click",".single-day-delete",function(event){
            var value = confirm("'.Yii::t('diary','Are you sure want to remove this entry from your calendar?').'");
            if(value == true){
                delete_entry($(this));
            }
            event.stopPropagation();
        });
        
    });',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('create-training-entry',
    'function show_fl_input_create($element){
    
        $("#TrainingEntry_id_activity").val("");
        handle_input_visibility($("#TrainingEntry_id_activity").val());
        
        $(".fl-input-area").find(".flash-success").hide();
        $(".fl-input-area").find(".errorSummary").remove();
        $(".fl-input-area").find("label").removeClass("error");
        $(".fl-input-area").find("input").removeClass("error");
        $(".fl-input-area").find("select").removeClass("error");
        $("#training_time_notice").hide();
        
        $(".fl-input-area-bg").fadeIn(1300);
        $(".fl-input-area").animate({top:"400px", opacity:"1"},500);
        
        $("#DiaryInput-preloader").hide(0);
        $("#DiaryInput-form").show(0);
        date = $element.find(".single-day-date").html();
        response = "<h3><div style=\"float: left\">'.Yii::t('diary','Saving training entry for ').'</div>";
        response += "<i>"+date+"</i></h3><div style=\"clear:both\">";
        $(".DiaryInput-header").html(response);
        
        $("#TrainingEntry_description").val("");
        $("#TrainingEntry_id").val("null");
        $("#TrainingEntry_date").val($element.find(".single-day-date").attr("id"));
        
        $("#di-left-otherdata").find("table").find("input").val("");
        $(".zone_time-percentage").val("");
        $(".zone_time-time").val("");
        $("#TrainingEntry_feelings").val("1");
        $(".input-trainig-time").val("");
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('show_fl_input_update',
    'function show_fl_input_update($element){
        $(".fl-input-area-bg").fadeIn(1300);
        $(".fl-input-area").animate({top:"400px", opacity:"1"},500);
        id = $element.attr("id");
        
            $.ajax({
                    type: "GET",
                    url: "'.Yii::app()->createAbsoluteUrl('diary/getsingleentry').'",
                    data: {id: id},
                    success: function(data, status){
                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            id = xmlDocumentElement.getElementsByTagName("id").item(0).firstChild.data;
                            id_activity = xmlDocumentElement.getElementsByTagName("id_activity").item(0).firstChild.data;
                            id_visibility = xmlDocumentElement.getElementsByTagName("id_visibility").item(0).firstChild.data;
                            
                            date = xmlDocumentElement.getElementsByTagName("date").item(0).firstChild.data;
                            mysqldate = xmlDocumentElement.getElementsByTagName("mysqldate").item(0).firstChild.data;
                            duration = xmlDocumentElement.getElementsByTagName("duration").item(0).firstChild.data;
                            feelings = xmlDocumentElement.getElementsByTagName("feelings").item(0).firstChild.data;
                            
                            description = xmlDocumentElement.getElementsByTagName("description").item(0).firstChild.data;
                            min_hr = xmlDocumentElement.getElementsByTagName("min_hr").item(0).firstChild.data;
                            avg_hr = xmlDocumentElement.getElementsByTagName("avg_hr").item(0).firstChild.data;
                            max_hr = xmlDocumentElement.getElementsByTagName("max_hr").item(0).firstChild.data;
                            
                            distance = xmlDocumentElement.getElementsByTagName("distance").item(0).firstChild.data;
                            avg_speed = xmlDocumentElement.getElementsByTagName("avg_speed").item(0).firstChild.data;
                            avg_pace = xmlDocumentElement.getElementsByTagName("avg_pace").item(0).firstChild.data;
                            ascent = xmlDocumentElement.getElementsByTagName("ascent").item(0).firstChild.data;
                            
                            max_altitude = xmlDocumentElement.getElementsByTagName("max_altitude").item(0).firstChild.data;
                            avg_watts = xmlDocumentElement.getElementsByTagName("avg_watts").item(0).firstChild.data;
                            max_watts = xmlDocumentElement.getElementsByTagName("max_watts").item(0).firstChild.data;
                            link = xmlDocumentElement.getElementsByTagName("link").item(0).firstChild.data;

                            response = "<h3 class=\"float:left\">'.Yii::t('diary','Updating training entry for ').'";
                            response += "<i>"+date+"</i>";
                            response += "<a id=\"diary-to-workout-link\" href=\""+link+"\">'.Yii::t('diary', 'View workout in details').'</a></h3>";
        
                            $(".DiaryInput-header").html(response);
                            
                            $("#TrainingEntry_date").val(mysqldate);
                            $("#TrainingEntry_id").val(id);
                            $("#TrainingEntry_id_activity").val(id_activity);
                            $("#TrainingEntry_id_visibility[value=\""+id_visibility+"\"]").prop("checked", true);
                            $("#TrainingEntry_description").val(description);
                            $("#TrainingEntry_min_hr").val((min_hr=="null")? "":min_hr);
                            $("#TrainingEntry_avg_hr").val((avg_hr=="null")? "":avg_hr);
                            $("#TrainingEntry_max_hr").val((max_hr=="null")? "":max_hr);
                            $("#TrainingEntry_distance").val((distance=="null")? "":distance);
                            $("#TrainingEntry_avg_speed").val((avg_speed=="null")? "":avg_speed);                            
                            $("#TrainingEntry_avg_pace").val((avg_pace=="null")? "":avg_pace);    
                            $("#TrainingEntry_ascent").val((ascent=="null")? "":ascent);
                            $("#TrainingEntry_max_altitude").val((max_altitude=="null")? "":max_altitude);
                            $("#TrainingEntry_avg_watts").val((avg_watts=="null")? "":avg_watts);
                            $("#TrainingEntry_max_watts").val((max_watts=="null")? "":max_watts);

                            parsedTime = duration.split(":");
                            $("#TrainingEntry_hours").val(parsedTime[0]);
                            $("#TrainingEntry_minutes").val(parsedTime[1]);
                            
                            $("#TrainingEntry_feelings").val(feelings);
                            $(".TrainingEntry-feelings").removeClass("feelings-selected");
                            $("#feelings"+feelings).addClass("feelings-selected");

                            handle_input_visibility($("#TrainingEntry_id_activity").val());

                            $("#DiaryInput-preloader").hide();
                            $("#DiaryInput-form").show();
                            
                        }catch(e){
                            
                        }
                    }
            });
    }'
    ,CClientScript::POS_END);

Yii::app()->clientScript->registerScript('delete-entry',
    'function delete_entry($element){
        id = $element.parent().attr("id");
        $.ajax({
            type: "POST",
            url: "'.Yii::app()->createAbsoluteUrl('diary/delete').'",
            data: {id: id},
            success: function(data, status){
                try{
                    xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                    response = xmlDocumentElement.firstChild.data;
                    if(response === "1"){
                        window.location.assign(document.URL);  
                    }
                    else{

                    }
                }catch(e){
                }
            }
        });
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('zone-time-calculator',
    '$(document).ready(function(){ 
        var control = 0;
        $(".zone_time-percentage, .zone_time-time").focus(function(){
            completeTrainingTime(control);
        });
        
        $(".zone_time-percentage").blur(function(){
            if(($("#TrainingEntry_hours").val() === "") || ($("#TrainingEntry_minutes").val() === "")){
                completeTrainingTime();
            }else if($(this).val() !== ""){ //percentage to time conversion
                
                per = $(this).val()/100;
                t_min = $("#TrainingEntry_hours").val()*60 + $("#TrainingEntry_minutes").val()*1;
                min = (per*t_min);
                hours = parseInt(min/60);
                min = parseInt(min % 60);
                $(this).parent().parent().find(".zone_time-time").val(pad(hours.toString(),2)+":"+pad(min.toString(),2));
            }
        });
        $(".zone_time-time").blur(function(){
            if(($("#TrainingEntry_hours").val() === "") || ($("#TrainingEntry_minutes").val() === "")){
               completeTrainingTime();
            }else if($(this).val() !== ""){ //time to percentage conversion
             
                var reg = new RegExp("[0-9]{2}:[0-9]{2}");
                if(reg.test($(this).val())){
                    $(this).removeClass("error");
                    t_splited = $(this).val().split(":");
                    t_min = $("#TrainingEntry_hours").val()*60 + $("#TrainingEntry_minutes").val()*1;
                    time = 60*t_splited[0]+t_splited[1]*1;
                    per = parseInt(time*100/t_min);
                    $(this).parent().parent().find(".zone_time-percentage").val(per);
                }
                else{
                    $(this).addClass("error");
                }
            }
        });
    });
    
    function completeTrainingTime(){
        if(($("#TrainingEntry_hours").val() === "") && ($("#TrainingEntry_minutes").val() === "")){
                $("#training_time_notice").fadeIn();
            }
            else{
                if($("#TrainingEntry_hours").val() === ""){
                    $("#TrainingEntry_hours").val("00");
                    $("#training_time_notice").fadeOut();
                }
                else if($("#TrainingEntry_minutes").val() === ""){
                    $("#TrainingEntry_minutes").val("00");
                    $("#training_time_notice").fadeOut();
                }
                else{
                    $("#training_time_notice").fadeOut();
                }
        }
    }
    //Padding string with leading zeroes
    function pad(n, pos){
        while(n.length < pos){
            n = "0"+n;
        }
        return n;
    }',
CClientScript::POS_END);

Yii::app()->clientScript->registerScript('feelings-selector',
    '$(document).ready(function(){
        $(".TrainingEntry-feelings").click(function(){
            id = $(this).attr("id");
            $(".TrainingEntry-feelings").removeClass("feelings-selected");
            $(this).addClass("feelings-selected");
            $("#TrainingEntry_feelings").val(id.substr(id.length-1));
        });
    });',
CClientScript::POS_END);

?>