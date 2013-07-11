<?php
/* @var $this UserController */
/* @var $model User */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/signup.png" height="42" width="389"></div>

<h2 class="flying">Join Us</h2>
<h3 style="text-align: center">Connect the network today and start to train like a real professional</h3>

<div class="form" id="SignUp-form">
    <?php echo CHtml::beginForm('','post'); ?>
    
	<?php echo Chtml::errorSummary($model); ?>
    
        <?php //Flash o neuspesnom ulozeni
            if(Yii::app()->user->hasFlash('notsaved')):?>
                <div class="flash-error">
                        <?php echo Yii::app()->user->getFlash('notsaved'); ?>
                </div>
            <?php endif;?>
    
        <div class="float-left" style="width: 205px">
            <p class="note">Fields with <span class="required">*</span> are required.</p>
        </div>
        <div class="float-left" style="width: 205px; text-align: center">
            <?php echo CHtml::activeLabel($model,'gender',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeDropDownList($model,'gender',array(''=>'','M'=>'Male','F'=>'Female'))?>
            <?php echo CHtml::error($model,'gender')?>       
        </div>
    
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'name',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'name',array('size'=>'20','maxlength'=>'30'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'name')?></div>
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'lastname')?>
            <?php echo CHtml::activeTextField($model,'lastname',array('size'=>'20','maxlength'=>'30'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'lastname')?></div>
        </div>
        
        
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'username',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'username',array('size'=>'20','maxlength'=>'20'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'username')?></div> 
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'email',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeTextField($model,'email',array('size'=>'20','maxlength'=>'40'))?>
            <div class="validation-info"><?php echo CHtml::error($model,'email')?></div>
        </div>
        
        <div class="clear-line"></div>
        
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'password',array('class'=>'requiredField'))?>
            <?php echo CHtml::activePasswordField($model,'password',array('size'=>'20','maxlength'=>'25'))?>
            <?php echo CHtml::error($model,'password')?> 
        </div>
        <div class="float-left">
            <?php echo CHtml::activeLabel($model,'confirmPassword',array('class'=>'requiredField'))?>
            <?php echo CHtml::activePasswordField($model,'confirmPassword',array('size'=>'20','maxlength'=>'25'))?>
            <?php echo CHtml::error($model,'confirmPassword')?> 
        </div>

	<div class="submit-button">
            <input type="submit" value="Sign Up" class="big-button" id="signup-button" title="Sign Up" style="margin-left: 150px" />
	</div>
<?php echo CHtml::endForm(); ?>

</div><!-- form -->
<div class="float-right" id="sign-up-reason">
    <div class="float-left" id="reason1"><span class="bold-red">Reach your goals</span> with sophisticated system of planning and managing your sport training. Winning has never been easier with objectives clearly listed.</div>
    <div class="float-left" id="reason2"><span class="bold-red">No matter</span> if you want to become a champion or just lose a few pounds. Training with us is professional and funny. Find your friends and share your latest workouts and improvement.</div>
    <div class="float-left" id="reason3"><span class="bold-red">Simplicity</span> is what we offer. Quick input but maximum output with all data you need. Are you missing something? So just create it and start to use.</div>
</div>
<?php
Yii::app()->clientScript->registerScript('sign-up-check-password',
        '$(document).ready(function(){
            ///Validacia spravneho zopakovania hesla
            $("#User_password, #User_confirmPassword").keyup(function() {
                var $confirm = $("#User_confirmPassword");
                var $password = $("#User_password");
                
                if($password.val() === $confirm.val()){
                    $confirm.parent().removeClass("notconfirmed-ws").addClass("confirmed-ws");
                    $password.parent().removeClass("notconfirmed").addClass("confirmed");
                }
                else if($confirm.val()){
                    $confirm.parent().removeClass("confirmed-ws").addClass("notconfirmed-ws");
                    $password.parent().removeClass("confirmed").addClass("notconfirmed");
                }
                
                if(!$("#User_password").val()){
                    $confirm.parent().removeClass("confirmed-ws").removeClass("notconfirmed-ws");
                    $password.parent().removeClass("confirmed").removeClass("notconfirmed");
                }
            });
        });',
        CClientScript::POS_END);
Yii::app()->clientScript->registerScript('sign-up-check-username-and-email',
        '$(document).ready(function(){
            
            ///Kontrola jedinecnosti uzivatelskeho mena prostrednictvom AJAX requestu
            var usernameEdited = false;
            $("#User_username").blur(function (){
                if($(this).val()){
                    usernameControl($(this).val(),$(this));
                    usernameEdited = true;
                }
                else{
                    usernameEdited = false;
                    $(this).parent().removeClass("confirmed-ws").removeClass("notconfirmed-ws");
                    $(this).parent().find(".validation-info").html("");
                }
            });
            $("#User_username").keyup(function (){
                if(usernameEdited && $(this).val()){
                    usernameControl($(this).val(),$(this));
                }
                if(!$(this).val()){
                    usernameEdited = false;
                    $(this).parent().removeClass("confirmed-ws").removeClass("notconfirmed-ws");
                    $(this).parent().find(".validation-info").html("");
                }
            });
            
            function usernameControl(usernameString, $input){
                process("'.Yii::app()->params->homeUrl.'/index.php/user/validateusername?username="+usernameString,$input);
            }
            
            ///Kontrola jedinecnosti emailu v databaze prostrednictvom AJAX requestu
            var emailEdited = false;
            $("#User_email").blur(function (){
                if($(this).val()){
                    emailControl($(this).val(),$(this));
                    emailEdited = true;
                }
                else{
                    emailEdited = false;
                    $(this).parent().removeClass("confirmed-ws").removeClass("notconfirmed-ws");
                    $(this).parent().find(".validation-info").html("");
                }
            });
            $("#User_email").keyup(function (){
                if(emailEdited && $(this).val()){
                    emailControl($(this).val(),$(this));
                }
                if(!$(this).val()){
                    emailEdited = false;
                    $(this).parent().removeClass("confirmed-ws").removeClass("notconfirmed-ws");
                    $(this).parent().find(".validation-info").html("");
                }
            });
            function emailControl(emailString, $input){
                process("'.Yii::app()->params->homeUrl.'/index.php/user/validateemail?email="+emailString,$input);
            }
            
        });',
        CClientScript::POS_END);

Yii::app()->clientScript->registerScript('sign-up-proces-ajax-request',
        '///Funkcia precita subor zo serveru
        function process(url,$input){
            if(xmlHttp){ //pokracuje len ak xmlHttp request nie je prazdny

                try{ //pokus o pripojenie na server
                    xmlHttp.open("GET",url,true); //true odosiela asynchronne poziadavky
                    xmlHttp.onreadystatechange = function(){
                        if(xmlHttp.readyState == 4){ //Ak je state 4 mozeme precitat odpoved
                            if(xmlHttp.status == 200){ //ak je status 200 odpoved je v poriadku
                                try{
                                    var xmlResponse = xmlHttp.responseXML;
                                    //Zachytenie moznych chyb vo firefoxe a IE
                                    if(!xmlResponse || !xmlResponse.documentElement)
                                        throw("Wrong xml structure.");
                                    if(xmlResponse.documentElement.nodeName == "parsererror")
                                        throw("Wrong xml structure.");

                                    xmlDocumentElement = xmlResponse.documentElement; //korenovy prvok xml dokumentu
                                    bool_value = xmlDocumentElement.firstChild.data;
                                    id = $input.attr("id").split("_");
                                    if(bool_value === "true"){
                                       $input.parent().removeClass("confirmed-ws").addClass("notconfirmed-ws");
                                       $input.parent().find(".validation-info").html("<span class=\"used\">"+id[1].charAt(0).toUpperCase() + id[1].slice(1)+" is alredy used</span>");
                                    }
                                    else{
                                        $input.removeClass("error"); 
                                       $input.parent().removeClass("notconfirmed-ws").addClass("confirmed-ws");
                                       $input.parent().find(".validation-info").html("<span class=\"free-to-use\">"+id[1].charAt(0).toUpperCase() + id[1].slice(1)+" is free to use</span>");
                                    }
                                }
                                catch(e){
                                    alert("We are terribly sorry. Application can\'t read server response. Please try again");
                                }
                            }
                            else{
                                alert("We are terribly sorry. Data were lost. Please try again");
                            }
                        }
                    }
                    xmlHttp.send(null);
                }
                catch(e){
                    alert("We are terribly sorry. Application can\'t connect server. Please try again");
                }
            }
        }',
        CClientScript::POS_END);

Yii::app()->clientScript->registerScript('createXMLHttpRequest',
                    'var xmlHttp = createXmlHttpRequestObject();

                    ///vytvorenie instancie HmlHttpRequest
                    function createXmlHttpRequestObject(){

                        var xmlHttp; //referencia na objekt xmlHttp
                        try { //prehliadače okrem IE6 a starších
                            xmlHttp = new XMLHttpRequest(); //pokus o vytvorenie objektu XMLHttpRequest
                        }
                        catch(e){ //predpoklada sa IE6 alebo starsi
                            var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
                                                            "MSXML2.XMLHTTP.5.0",
                                                            "MSXML2.XMLHTTP.4.0",
                                                            "MSXML2.XMLHTTP.3.0",
                                                            "MSXML2.XMLHTTP",
                                                            "Microsoft.XMLHTTP");
                            for(var i = 0; i < XmlHttpVersions.length && !xmlHttp; i++){//vyskusa vsetky prog ID pokial niektore nebude funkcne
                                try{
                                    xmlHttp = new ActiveObject(XmlHttpVersions[i]); //pokus o vytvorenie objektu XMLHttpRequest
                                }
                                catch(e){}
                            } 
                        }
                        if(!xmlHttp) {
                            alert("We are terribly sorry. Something wrong happend with server request. Please try again.");
                            return null;
                        }
                        else
                            return xmlHttp; //objekt XMLHttpRequest sa podarilo vytvorit

                    }',
                    CClientScript::POS_END);
?>

<script type="text/javascript"></script> 