<?php
    if(!$coach_cooperation):?>

    <div id='userProfile-sparring-section-wrapper'> 

        <div id="userProfile-sparring-section">
            <a href="#" style="float: left; font-size: 12px"><?php echo Yii::t('user', 'If you want to start cooperate and train ').$user->name?> - </a>
            <?php echo CHtml::link(Yii::t('user', 'Add to your team'),'',array('id'=>'userProfile-add-sparring-button','class'=>'styled-button wide-button')); ?>
        </div>

        <div class="flash-success" style="display: none">
            <?php echo Yii::t('user','Congratulations. You are now cooperating with '). $user->name?> 
        </div>
        <div class="flash-error" style="display: none">
            <?php echo Yii::t('error', 'We are terribly sorry').'! '.Yii::t('error', 'Error occured during your request')?> 
        </div>
    </div>

    <?php Yii::app()->clientScript->registerScript('userProfile-start-cooperation','
        $(document).ready(function(){
            $("#userProfile-add-sparring-button").click(function(){
                $hide_element = $("#userProfile-sparring-section");
                $hide_element.animate({opacity:0});

                $element_success = $("#userProfile-sparring-section-wrapper").find(".flash-success");
                $element_error = $("#userProfile-sparring-section-wrapper").find(".flash-error");

                ajaxRequest(
                    "'.Yii::app()->createAbsoluteUrl("coaching/startcooperation").'",
                    "'.Yii::app()->user->id.'",
                    "'.$user->id.'",
                    $element_success,
                    $element_error,
                    $hide_element
                );                     
            });
        });',CClientScript::POS_END);?>

<?php else: ?>
    <div id='userProfile-sparring-section-wrapper'> 
        
        <div class="flash-success" style="right: -110px; top: 30px;left: 110px;">
            <?php echo Yii::t('user','You are coach of user ').$user->name?>
        </div>
        <div class="flash-notice" style="display: none">
            <?php echo $user->name. Yii::t('user',' is no longer in your team.')?>
        </div>
        <div class="flash-error" style="display: none">
           <?php echo Yii::t('error', 'We are terribly sorry').'! '.Yii::t('error', 'Error occured during your request')?> 
        </div>
        <?php echo CHtml::link(Yii::t('user','Quit cooperation'),'',array('id'=>'userProfile-unfriend-button','class'=>'styled-button wide-button')); ?>

        <?php Yii::app()->clientScript->registerScript('userProfile-quit-cooperation','
        $(document).ready(function(){
            $("#userProfile-unfriend-button").click(function(){
            
                var value = confirm("'.Yii::t('user','Are you sure want to quit cooperation with this athlete?').'");
                if(value == true){
                    $hide_element = $("#userProfile-unfriend-button");
                    $hide_element.animate({opacity:0});

                    $("#userProfile-sparring-section-wrapper").find(".flash-success").animate({opacity:0});

                    $element_success = $("#userProfile-sparring-section-wrapper").find(".flash-notice");
                    $element_error = $("#userProfile-sparring-section-wrapper").find(".flash-error");

                    ajaxRequest(
                        "'.Yii::app()->createAbsoluteUrl("coaching/quitcooperation").'",
                        "'.Yii::app()->user->id.'",
                        "'.$user->id.'",
                        $element_success,
                        $element_error,
                        $hide_element
                    );  
                }                   
            });
        });',CClientScript::POS_END);?>
    </div>
<?php endif; ?>

<?php Yii::app()->clientScript->registerScript('AJAX-request-handler','
    //SPRACOVANIE AJAX POZIADAVKY
    function ajaxRequest(url_address, user_sending, user_to_handle, $element_success, $element_error, $hide_element ){
        $.ajax({
                type: "POST",
                url: url_address,
                data: {usersending: user_sending, usertohandle: user_to_handle},
                success: function(data, status){

                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            bool_value = xmlDocumentElement.firstChild.data;

                            if(bool_value === "1"){
                                $element_success.fadeIn("slow");
                                setTimeout(function(){window.location.assign(document.URL);},2000);
                            }
                            else{
                                displayError($element_error,$hide_element);
                            }                          
                        }
                        catch(e){
                            displayError($element_error,$hide_element);
                        }
                    },
                    error: function(jqXHR,textStatus,errorThrown){
                        displayError($element_error,$hide_element);
                    }
            });
    }
    function displayError($element_error,$hide_element){
        $element_error.fadeIn("slow").delay(1200).fadeOut("slow");
        $hide_element.delay(1800).animate({opacity:1});
    }',  
CClientScript::POS_END);?>