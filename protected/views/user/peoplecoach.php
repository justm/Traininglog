<?php
/**
 * @var $this UserController
 * @var $users List of Users - CActiveRecords with no relations 
 * @var $friends List of Users - CActiveRecords with relation
 * @var $activity List of Activities - CActiveRecords 
 * 
 */
    $i = 0;
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/people.png" height="42" width="231"></div>
<h2 class="flying">People</h2>

    <div id="people-find">
        <input type="text" class="people-not-focused-search" id="people-find-input" value="Find other people" size="38"/>
        <div id="people-find-response">
            <h3>Searching ... </h3>
            <ul id="people-find-list">
            </ul>
        </div>
    </div>
    
    <div id="people-suggest-container">
        <a id="people-load-suggestions" class="styled-button wide-button" title="Display more suggestions">Display more</a>    
        <div style="clear: both;"></div>
        
        <div id="people-suggest">
            <div style="float:left">
            <?php foreach ($users as $user): ?>
                <?php if(++$i == 8){
                    echo '</div><div style="float:left">';
                    $i = 1;
                }?>
                
                <a title="View Profile" href="<?php echo Yii::app()->params->homePath.'/'.Yii::app()->language.'/user/view/'.$user->username;?>" style="width: 240px; margin: 0 10px">
                    <?php if($user->profile_picture != '' && ($pic = $this->getPicture($user->profile_picture, 'profile-picture'))!= null):?>
                        <img class="response_picture" src="<?php echo $pic; ?>" height="45"/>
                    <?php else:?>
                        <img class="response_picture" src="<?php echo Yii::app()->request->baseUrl.'/images/photo-default-'.$user->gender.'.png'?>" height="45"/>
                    <?php endif;?>

                    <span class="fullname"><?php echo $user->fullname; ?></span>
                    <span class="username">as <i><?php echo $user->username; ?></i></span>
                    <?php 
                    if($user->id_primary_activity != '' || $user->id_primary_activity != null){
                        if ((strlen($user->fullname) + strlen($user->username))<20){
                            echo '<br />';
                        }
                        echo  '<span class="people-activity">'.$activity[$user->id_primary_activity-1]->name.'</span>';
                    }
                ?>
                </a>
            <?php endforeach;
                echo '</div>';
            ?><div style="clear: both;"></div>    
            </div>  
            
    </div>
    <div style="clear: both; height: 450px"></div>    

<?php Yii::app()->clientScript->registerScript('people-find','
    $(document).ready(function(){
        
        $("#people-find-input").focus(function() {
            $(this).val("");
            $(this).removeClass("people-not-focused-search");
            $("#people-find-response").find("h3").text("Searching ... ");
        });
        
        $("#people-find-input").blur(function() {
            $(this).val("Find other people");
            $(this).addClass("people-not-focused-search");
            $("#people-find-response").slideUp("fast");
        });
        
        $("#people-find-input").keyup(function(){
            
            
            value = $("#people-find-input").val();
            
            if(value != ""){
                $("#people-find-response").slideDown("fast");
                $.ajax({
                    type: "GET",
                    url: "'.Yii::app()->createAbsoluteUrl('user/finduser').'",
                    data: {finduser: value},
                    
                    success: function(data, status){
                        $("#people-find-response").find("h3").text("People found:");
                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            names = xmlDocumentElement.getElementsByTagName("fullname");
                            usernames = xmlDocumentElement.getElementsByTagName("username");
                            pictures = xmlDocumentElement.getElementsByTagName("picture");
                            gender = xmlDocumentElement.getElementsByTagName("gender");
                            activity = xmlDocumentElement.getElementsByTagName("activity");
                            var response ="";
                            for(var i=0; i < names.length; i++){
                                
                                response += "<li><a href=\"user/view/"+usernames.item(i).firstChild.data+"\"";
                                response += "title =\"View profile\">";  
                                
                                if((pictures.item(i).firstChild.data) != "null"){
                                    response += "<img class=\"response_picture\" src=\"'. Yii::app()->params->homeUrl.'uploads/profile-picture/"
                                    +pictures.item(i).firstChild.data+"\" height=\"45\"/>";    
                                }
                                else{
                                    response += "<img class=\"response_picture\" src=\"'. Yii::app()->params->homeUrl.'images/photo-default-"
                                    +gender.item(i).firstChild.data+".png\" height=\"45\"/>";
                                }
                                
                                
                                response += "<span class=\"fullname\">"+names.item(i).firstChild.data+"</span>";
                                response += "<span class=\"username\"> as <i>"+usernames.item(i).firstChild.data+"</i></span>";
                                
                                if((activity.item(i).firstChild.data) != "null"){
                                    if((names.item(i).firstChild.data.length + usernames.item(i).firstChild.data.length)<20){
                                        response += "<br />";
                                    }
                                    response += "<span class=\"people-activity\"> "+activity.item(i).firstChild.data+"</span>";  
                                }
                                response += "</a></li>";
                            }

                            $("#people-find-list").html(response);
                        } 
                        catch(e){

                        }

                    }
                });
            }
            else{
                $("#people-find-response").slideUp("fast");
                $("#people-find-list").html();
            }
        });
    });', CClientScript::POS_END);
?>

<?php Yii::app()->clientScript->registerScript('adding-to-list','
    
    function createResponse(data, add_class){
        try{
            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
            names = xmlDocumentElement.getElementsByTagName("fullname");
            usernames = xmlDocumentElement.getElementsByTagName("username");
            pictures = xmlDocumentElement.getElementsByTagName("picture");
            gender = xmlDocumentElement.getElementsByTagName("gender");
            activity = xmlDocumentElement.getElementsByTagName("activity");
            var response = "<div style=\"float:left\">";
            for(var i=0; i < names.length; i++){

                response += "<a class=\""+add_class+"\" href=\"user/view/"+usernames.item(i).firstChild.data+"\"";
                response += "title =\"View profile\" style=\"float:left; margin: 0 10px; width: 240px\">";  

                if((pictures.item(i).firstChild.data) != "null"){
                    response += "<img class=\"response_picture\" src=\"'. Yii::app()->params->homeUrl.'uploads/profile-picture/"
                    +pictures.item(i).firstChild.data+"\" height=\"45\"/>";    
                }
                else{
                    response += "<img class=\"response_picture\" src=\"'. Yii::app()->params->homeUrl.'images/photo-default-"
                    +gender.item(i).firstChild.data+".png\" height=\"45\"/>";
                }


                response += "<span class=\"fullname\">"+names.item(i).firstChild.data+"</span>";
                response += "<span class=\"username\"> as <i>"+usernames.item(i).firstChild.data+"</i></span>";

                if((activity.item(i).firstChild.data) != "null"){
                    if((names.item(i).firstChild.data.length + usernames.item(i).firstChild.data.length)<20){
                        response += "<br />";
                    }
                    response += "<span class=\"people-activity\"> "+activity.item(i).firstChild.data+"</span>";  
                }

                response += "</a></div>";
            }
        } 
        catch(e){

        }
        return response;
    }', CClientScript::POS_END);
?>


<?php Yii::app()->clientScript->registerScript('strangers-adding-to-list','
    var value = 0;
    $(document).ready(function(){
        
        $("#people-load-suggestions").click(function(){
            value++;   
        
            $.ajax({
                    type: "GET",
                    url: "'. Yii::app()->createAbsoluteUrl("user/suggestusers").'",
                    data: {offset: value*21, limit:21},

                    success: function(data, status){
                        response = createResponse(data,"");
                        $("#people-suggest").css({display: "none"});
                        $("#people-suggest").html(response);
                        $("#people-suggest").fadeIn(1000);
                    }
            });
        });
    });', CClientScript::POS_END);
?>
    