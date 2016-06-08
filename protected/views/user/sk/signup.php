<?php
/* @var $this UserController */
/* @var $model User */
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/signup.png" height="42" width="389"></div>

<h2 class="flying">Pridajte sa</h2>
<h3 style="text-align: center">Pripojte sa k nám a začnite trénovať ako naozajstný profesionál</h3>

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
            <p class="note"><?php echo Yii::t('global','Fields with ');?><span class="required">*</span><?php echo Yii::t('global',' are required');?></p>
        </div>
        <div class="float-left" style="width: 205px; text-align: center">
            <?php echo CHtml::activeLabel($model,'gender',array('class'=>'requiredField'))?>
            <?php echo CHtml::activeDropDownList($model,'gender',array(''=>'','M'=>'Muž','F'=>'Žena'))?>
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
            <input type="submit" value="Registrovať" class="big-button" id="signup-button" title="Pridať sa" style="margin-left: 135px; width: 150px !important"/>
	</div>
<?php echo CHtml::endForm(); ?>

</div><!-- form -->
<div class="float-right" id="sign-up-reason">
    <div class="float-left" id="reason1"><span class="bold-red">Dosiahnite svoje ciele</span> so sofistikovaným systémom pre plánovanie a analýzu vášho tréningu. Víťaziť nebolo nikdy jednoduchšie s prehľadným zoznamom vašich cieľov.</div>
    <div class="float-left" id="reason2"><span class="bold-red">Nezáleží</span> na tom či sa chcete stať šampiónom alebo len zhodiť niekoľko kilogramov. Tréning s nami je profesiónalny a zábavný. Spojte sa s priateľmi a zdieľajte vaše tréningy a zlepšenie.</div>
    <div class="float-left" id="reason3"><span class="bold-red">Ponúkame jednoduchosť.</span> Rýchly vstup no maximálny výstup so všetkým čo potrebujete. Chýba vám niečo? Stačí to jednoducho vytvoriť a začať používať.</div>
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
                                       if(id[1]=="username"){
                                           $input.parent().find(".validation-info").html("<span class=\"used\">Používateľské meno obsadené</span>");
                                       }
                                       else{
                                           $input.parent().find(".validation-info").html("<span class=\"used\">K emailovej adrese existuje účet</span>");
                                       }
                                    }
                                    else{
                                       $input.removeClass("error"); 
                                       $input.parent().removeClass("notconfirmed-ws").addClass("confirmed-ws");
                                       if(id[1]=="username"){
                                           $input.parent().find(".validation-info").html("<span class=\"free-to-use\">Používateľské meno je voľné</span>");
                                       }
                                       else{
                                           $input.parent().find(".validation-info").html("<span class=\"free-to-use\">Email zatiaľ nepoužívaný</span>");
                                       }
                                    }
                                }
                                catch(e){
                                    alert("Je nám ľúto, aplikácia nemohla prečítať odpoveď zo servera. Prosím skúste znovu.");
                                }
                            }
                            else{
                                alert("Je nám ľúto, dáta sa niekde stratili. Prosím skúste znovu.");
                            }
                        }
                    }
                    xmlHttp.send(null);
                }
                catch(e){
                    alert("Je nám ľúto, aplikácia sa nemôže pripojiť na server. Prosím skúste znovu.");
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
                                    xmlHttp = new ActiveObject(XmlHttpVersions[i]); //pokus o vytvorenie objekt XMLHttpRequest
                                }
                                catch(e){}
                            } 
                        }
                        if(!xmlHttp) {
                           alert("Je nám ľúto, došlo k chybe behom vytvrárania požiadavky na server. Prosím skúste znovu.");
                            return null;
                        }
                        else
                            return xmlHttp; //objekt XMLHttpRequest sa podarilo vytvorit

                    }',
                    CClientScript::POS_END);
?>

