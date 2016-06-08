<?php
/**
 * @var $this PlanController
 * @var $plan
 * @var $singleEntry empty object of TrainingPlan
 * @var $activity List of Activity 
 * @var $activity_array Array with all entries from DB table Activity
 */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/plan.png" height="42" width="350"></div>
<h2 class="flying"><?php echo Yii::t('global','Training plan')?></h2>

<?php
    if($singleEntry->id != null || $singleEntry->id != '' 
            || Yii::app()->user->hasFlash('label-created') || Yii::app()->user->hasFlash('label-notcreated')){
        Yii::app()->clientScript->registerScript('show-fl-input-area-on-start','
            $(document).ready(function(){
                $("#PlanInput-preloader").hide();
                $("#PlanInput-form").show();
                $(".fl-input-area-bg").show();
                $(".fl-input-area").css({top:"460px", opacity:"1"});
            });
            ',CClientScript::POS_END);
    }
?>
<?php
    if(Yii::app()->user->hasFlash('plan-entry-deleted')){
        echo '<div class="flash-error">'.Yii::app()->user->getFlash('plan-entry-deleted').'</div>';
         Yii::app()->clientScript->registerScript('hide-delete-flash','
            $(document).ready(function(){
                $(".flash-error").delay(3000).fadeOut();
            });
            ',CClientScript::POS_END);
    }
?>

<div class="fl-input-area-bg"></div>
<div class="fl-input-area">
    <div class="preloader" id="PlanInput-preloader"></div>
    <div class="form" id="PlanInput-form">
            <?php 
                if(Yii::app()->user->hasFlash('plan-saved')){
                    echo '<div class="flash-success">'.Yii::app()->user->getFlash('plan-saved').'</div>';
                }
                if(Yii::app()->user->hasFlash('label-created')){
                    echo '<div class="flash-success">'.Yii::app()->user->getFlash('label-created').'</div>';
                     Yii::app()->clientScript->registerScript('hide-crlabel-flash','
                        $(document).ready(function(){
                            $(".flash-success").delay(3000).fadeOut();
                        });
                        ',CClientScript::POS_END);
                }
                if(Yii::app()->user->hasFlash('label-notcreated')){
                    echo '<div class="flash-error">'.Yii::app()->user->getFlash('label-notcreated').'</div>';
                     Yii::app()->clientScript->registerScript('hide-notcrlabel-flash','
                        $(document).ready(function(){
                            $(".flash-error").delay(3000).fadeOut();
                        });
                        ',CClientScript::POS_END);
                }
                echo CHtml::beginForm('','post');
                echo Chtml::errorSummary(array($singleEntry)); 
            ?>
            <div class="PlanInput-header"><b>
                <?php echo Yii::t('plan','Updating training plan for ')?><i>
                <?php
                    echo $this->days[date('w',strtotime($singleEntry->date))].' ';
                    echo date('d. m. Y',  strtotime($singleEntry->date));
                ?></i></b>
            </div>
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
                <b><i>HH: </b></i><input class="input-trainig-time" type="text" size="4" id="TrainingPlan_hours" name="TrainingPlan[hours]" value="<?php echo $time[0]?>">
                <b><i>MM: </b></i><input class="input-trainig-time" type="text" size="4" id="TrainingPlan_minutes" name="TrainingPlan[minutes]" value="<?php echo $time[1]?>">
            </div>

            <div style="clear:both"></div>
            <div class="float-left"> 
                <?php echo CHtml::activeLabel($singleEntry,'description')?>
                <?php echo CHtml::activeTextArea($singleEntry,'description', array('cols'=>40,'rows'=>8))?>
            </div>
            <div class="float-left"> 
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
            <div class="submit-button" >
                <input type="submit" value="<?php echo Yii::t('global','Save')?>" class="big-button" id="PlanSave-button" title="Save this entry into training plan" style="margin: auto;" />
            </div>
        <?php echo CHtml::endForm();?>
    </div>
</div>

<?php 
    $monday = date('Y-m-d',strtotime( "Monday this week",strtotime($request_date)));
    
    $daily_plan = $this->createDailyEntries($plan, $labels, $activity_array);
?>

<a class="styled-button wide-button" id="previous-week" style="float:left; width: 185px;"><?php echo Yii::t('plan','Show previous week')?></a>
<div class="date-chooser"><?php echo Yii::t('plan','or choose a date')?>
    <?php 
        echo $this->widget('zii.widgets.jui.CJuiDatePicker', array('name'=>'chooseDate','options'=>array('dateFormat'=>'yy-mm-dd'),'language'=>Yii::app()->language), true); 
    ?>
</div>
<a class="styled-button wide-button" id="date-chooser-submit" style="float:left"><?php echo Yii::t('plan','Display this date')?></a>
<?php 
    for($i=0; $i<2; $i++):
        
        echo '<div class="single-week">';
    
            for($j=1; $j<=7; $j++): 
                
                $add_days = '+'.(($i*7+$j)-1).' days';
                $date = Date('Y-m-d', strtotime($add_days, strtotime($monday)));
                $single_day_date = '<div class="single-day-date" id="'.date('Y-m-d',strtotime($date)).'">'.
                            '<div class="day-part">'.$this->days[date('w',strtotime($date))].'</div>'.
                            '<div class="date-part">'.date('d. m. Y',strtotime($date)).'</div></div>';
                
                echo '<div class="single-day-full" id="'.$date.'" ondrop="drop(event)" ondragover="allowDrop(event)">';
                echo $single_day_date;
                echo '<div style="clear:both"></div>';
                if(isset($daily_plan[$date])){
                    echo $daily_plan[$date];
                }
                
                echo '</div>';
                
            endfor; 

            echo '<div class="classic-box week-summary">';
                echo '<h3>'.Yii::t('plan','Total').':</h3>';
                echo '<div class="inner-week-summary" style="line-height: 35px; font-size: 110%">';
                    echo Yii::t('plan','Total workouts planned').': '.$summary[$i]->sumDays.'<br />';
                    echo Yii::t('plan','Hours planned').': '.$summary[$i]->sumHours.'<br />';
                echo '</div>';
        echo '</div>';    
        
        echo '<div style="clear: both"></div></div>';
    endfor; 
?>
    
<a class="styled-button wide-button" id="next-week" style="margin-top: 10px; width: 185px;"><?php echo Yii::t('plan','Show next week')?></a>

<script type="text/javascript">
	$(document).ready(function(){
		$(".single-day-full").each(function(){
			if( $(this).find(".single-entry").length == 1 ){
				$(this).find(".single-entry").css({height: "140px"});
			}
		});
	});
</script>  

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

Yii::app()->clientScript->registerScript('addprev-week',
    'function addPrevWeek(date){
        $.ajax({
            type: "GET",
            url: "'.Yii::app()->createAbsoluteUrl('plan/addweek').'",
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
            url: "'.Yii::app()->createAbsoluteUrl('plan/addweek').'",
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
            window.location.assign("'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/plan/index/"+val);
        });
        
        $(document).on("click",".single-day-full",function(){
            $(".form").find(".flash-success").hide();
            show_fl_input_create($(this));
        });
        
        $(document).on("mouseover",".single-entry",
            function(){
                $(this).find(".single-day-delete").html("X");
        });
        $(document).on("mouseleave",".single-entry",
            function(){
                $(this).find(".single-day-delete").html("");
        });
        
        $(document).on("click",".single-entry",function(event){
            $(".form").find(".flash-success").hide();
            show_fl_input_update($(this));
            event.stopPropagation();
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
            var value = confirm("'.Yii::t('plan','Are you sure want to remove this entry from your plan?').'");
            if(value == true){
                delete_entry($(this));
            }
            event.stopPropagation();
        });

        $(".fl-input-area-bg").click(function(){
            $(".fl-input-area-bg").fadeOut("fast");
            $(".fl-input-area").animate({top:"-1000px", opacity:"0"});
            $("#PlanInput-preloader").show();
            $("#PlanInput-form").hide();
        });
    });'
    ,CClientScript::POS_END);
?>

<?php 
Yii::app()->clientScript->registerScript('show_fl_input_update',
    'function show_fl_input_update($element){
        //var lablesTable;
        $(".fl-input-area-bg").fadeIn(1300);
        $(".fl-input-area").animate({top:"460px", opacity:"1"},500);
        id = $element.attr("id");
        if(labelsTable != ""){
            $("#TrainingPlan-labels").find("tbody").html(labelsTable);
        }
        
            $.ajax({
                    type: "GET",
                    url: "'.Yii::app()->createAbsoluteUrl('plan/getsingleentry').'",
                    data: {id: id},
                    success: function(data, status){
                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            id = xmlDocumentElement.getElementsByTagName("id");
                            id_activity = xmlDocumentElement.getElementsByTagName("id_activity");
                            date = xmlDocumentElement.getElementsByTagName("date");
                            mysqldate = xmlDocumentElement.getElementsByTagName("mysqldate");
                            duration = xmlDocumentElement.getElementsByTagName("duration");
                            description = xmlDocumentElement.getElementsByTagName("description");
                            
                            response = "<h3>'.Yii::t('plan','Updating training plan for ').'";
                            response += "<i>"+date.item(0).firstChild.data+"</i></h3>";
                            $(".PlanInput-header").html(response);
                            
                            $("#TrainingPlan_date").val(mysqldate.item(0).firstChild.data);
                            $("#TrainingPlan_id").val(id.item(0).firstChild.data);
                            $("#TrainingPlan_id_activity").val(id_activity.item(0).firstChild.data);
                            $("#TrainingPlan_description").val(description.item(0).firstChild.data);
                            
                            parsedTime = duration.item(0).firstChild.data.split(":");
                            $("#TrainingPlan_hours").val(parsedTime[0]);
                            $("#TrainingPlan_minutes").val(parsedTime[1]);
                            
                            $("#PlanInput-preloader").hide();
                            $("#PlanInput-form").show();
                            
                        }catch(e){
                            
                        }
                    }
            });
    }'
    ,CClientScript::POS_END);
?>

<?php 
Yii::app()->clientScript->registerScript('show_fl_input_create',
    'function show_fl_input_create($element){
        if(labelsTable != ""){
            $("#TrainingPlan-labels").find("tbody").html(labelsTable);
        }
        $(".fl-input-area-bg").fadeIn(1300);
        $(".fl-input-area").animate({top:"460px", opacity:"1"},500);
        
        $("#PlanInput-preloader").hide(0);
        $("#PlanInput-form").show(0);
        date = $element.find(".single-day-date").html();
        response = "<h3><div style=\"float: left\">'.Yii::t('plan','Creating training plan for ').'</div>";
        response += "<i>"+date+"</i></h3><div style=\"clear:both\">";
        $(".PlanInput-header").html(response);

        $("#TrainingPlan_id_activity").val("");
        $("#TrainingPlan_description").val("");
        $("#TrainingPlan_id").val("null");
        $("#TrainingPlan_date").val($element.find(".single-day-date").attr("id"));
        $(".input-trainig-time").val("");
    }'
    ,CClientScript::POS_END);
?>

<?php 
Yii::app()->clientScript->registerScript('delete-entry',
    'function delete_entry($element){
        id = $element.parent().attr("id");
        $.ajax({
            type: "POST",
            url: "'.Yii::app()->createAbsoluteUrl('plan/delete').'",
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
    }'
    ,CClientScript::POS_END);
?>

<?php 
Yii::app()->clientScript->registerScript('drag-and-drop',
    'function allowDrop(ev){
        ev.preventDefault();
    }
        
    function drag(ev){
        ev.dataTransfer.setData("Text",ev.target.id);
    }

    function drop(ev){
        ev.preventDefault();
        var data=ev.dataTransfer.getData("Text");
        ev.target.appendChild(document.getElementById(data));
        handleDroppedEntry(document.getElementById(data).id,ev.target.id);
    }
    function handleDroppedEntry(id, date){
        $.ajax({
            type: "POST",
            url: "'.Yii::app()->createAbsoluteUrl('plan/draganddrop').'",
            data: {id: id, date:date},

        });
    }'
    ,CClientScript::POS_END);
?>





    