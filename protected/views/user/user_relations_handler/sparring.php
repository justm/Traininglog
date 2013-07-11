<?php //ADDING USERS TO SPARRING LIST
    if(!Yii::app()->user->isGuest && !$isFriend && !$requestRecieved && !$requestSent && Yii::app()->user->roles != 'admin'){?>
        <div id='userProfile-sparring-section-wrapper'> 

            <div id="userProfile-sparring-section">
                <a href="#" style="float: left; font-size: 12px">If you want to share interests and train with <?php echo $user->name?> - </a>
                <?php echo CHtml::link('Ask for sparring','',array('id'=>'userProfile-add-sparring-button','class'=>'styled-button wide-button','title'=>'Add as friend')); ?>
            </div>

            <div class="flash-success" style="display: none">
                Sparring request was successfuly sent to <?php echo $user->name?> 
            </div>
            <div class="flash-error" style="display: none">
                We are terribly sorry! Something went wrong during your request 
            </div>
        </div>

        <?php Yii::app()->clientScript->registerScript('userProfile-add-sparring','
            $(document).ready(function(){
                $("#userProfile-add-sparring-button").click(function(){
                    $hide_element = $("#userProfile-sparring-section");
                    $hide_element.animate({opacity:0});

                    $element_success = $("#userProfile-sparring-section-wrapper").find(".flash-success");
                    $element_error = $("#userProfile-sparring-section-wrapper").find(".flash-error");

                    ajaxRequest(
                        "'.Yii::app()->createAbsoluteUrl("sparring/addsparring").'",
                        "'.Yii::app()->user->id.'",
                        "'.$user->id.'",
                        $element_success,
                        $element_error,
                        $hide_element
                    );                     
                });
            });',CClientScript::POS_END);?>
<?php
    } ///Viewed user is a sparring partner
    else if($isFriend && Yii::app()->user->roles != 'admin'){?>
        <div id='userProfile-sparring-section-wrapper'> 

            <div class="flash-success" style="right: -110px; top: 30px;left: 110px;">
                <?php $date = new DateTime($sparringRelation->request_date);?>
                You and <?php echo $user->name?> are sparring partners since <b><?php echo $date->format('d. m. Y');?></b>
            </div>
            <div class="flash-notice" style="display: none">
                You and <?php echo $user->name?> are no longer sparring partners
            </div>
            <div class="flash-error" style="display: none">
                We are terribly sorry! Something went wrong during your request 
            </div>
            <?php echo CHtml::link('Unfriend','',array('id'=>'userProfile-unfriend-button','class'=>'styled-button','title'=>'Remove '.$user->name.' from your friends')); ?>
            
            <?php Yii::app()->clientScript->registerScript('userProfile-unfriend','
            $(document).ready(function(){
                $("#userProfile-unfriend-button").click(function(){
                    $hide_element = $("#userProfile-unfriend-button");
                    $hide_element.animate({opacity:0});

                    $("#userProfile-sparring-section-wrapper").find(".flash-success").animate({opacity:0});
                    
                    $element_success = $("#userProfile-sparring-section-wrapper").find(".flash-notice");
                    $element_error = $("#userProfile-sparring-section-wrapper").find(".flash-error");

                    ajaxRequest(
                        "'.Yii::app()->createAbsoluteUrl("sparring/unfriend").'",
                        "'.Yii::app()->user->id.'",
                        "'.$user->id.'",
                        $element_success,
                        $element_error,
                        $hide_element
                    );                     
                });
            });',CClientScript::POS_END);?>
        </div>
<?php 
    } //Viewed user sent a sparring request
    elseif ($requestRecieved && Yii::app()->user->roles != 'admin') { ?>
        <div id='userProfile-sparring-section-wrapper'> 
            
            <div class="flash-notice" id="request-notice" style="top: 30px">
                <?php echo $user->name?> wants to be your sparring partner.
            </div>
            
            <div id="userProfile-sparring-section" style="position: relative; top: -18px;right: -40px;">
                <?php echo CHtml::link('Accept request','',array('id'=>'userProfile-accept-sparring','class'=>'styled-button wide-button','title'=>'Accept friend request')); ?>
                <?php echo CHtml::link('Refuse request','',array('id'=>'userProfile-refuse-sparring','class'=>'styled-button wide-button','title'=>'Refuse this request')); ?>
                <div style="clear: both"></div>
            </div>
            
            <div class="flash-success" style="display: none">
                Request confirmed. You and <?php echo $user->name?> are now friends
            </div>
            <div class="flash-error" style="display: none">
                We are terribly sorry! Something went wrong during your request 
            </div>
            
            
            <?php Yii::app()->clientScript->registerScript('userProfile-confirm-sparring','
            $(document).ready(function(){
                $("#userProfile-accept-sparring").click(function(){
                    $hide_element = $("#userProfile-sparring-section");
                    $hide_element.animate({opacity:0});

                    $("#request-notice").animate({opacity:0});

                    $element_success = $("#userProfile-sparring-section-wrapper").find(".flash-success");
                    $element_error = $("#userProfile-sparring-section-wrapper").find(".flash-error");

                    ajaxRequest(
                        "'.Yii::app()->createAbsoluteUrl("sparring/confirmsparring").'",
                        "'.Yii::app()->user->id.'",
                        "'.$user->id.'",
                        $element_success,
                        $element_error,
                        $hide_element
                    );                     
                });
            });',CClientScript::POS_END);?>
            
            <div class="flash-notice" id="refused" style="display: none">
                You have refused request from <?php echo $user->name?>
            </div>
            <div class="flash-error" id="refusing-error" style="display: none">
                We are terribly sorry! Something went wrong during your request 
            </div>
            
            <?php Yii::app()->clientScript->registerScript('userProfile-refuse-sparring','
            $(document).ready(function(){
                $("#userProfile-refuse-sparring").click(function(){
                    $hide_element = $("#userProfile-sparring-section");
                    $hide_element.animate({opacity:0});

                    $("#request-notice").animate({opacity:0});

                    $element_success = $("#refused");
                    $element_error = $("#refusing-error");

                    ajaxRequest(
                        "'.Yii::app()->createAbsoluteUrl("sparring/refusesparring").'",
                        "'.Yii::app()->user->id.'",
                        "'.$user->id.'",
                        $element_success,
                        $element_error,
                        $hide_element
                    );                     
                });
            });',CClientScript::POS_END);?>
            
        </div>
<?php } //Sparring request was already sent to viewed user
    elseif ($requestSent && Yii::app()->user->roles != 'admin') { ?>
        <div id='userProfile-sparring-section-wrapper'> 

            <div class="flash-success">
                Sparring request was sent. <?php echo $user->name?> must confirm it
            </div>
            
        </div>
<?php }?>


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

<!--END OF ADD SPARRING-->