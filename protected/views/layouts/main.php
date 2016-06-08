<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="sk">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="sk" />
        <meta name="author" content="MatusMacak"> 
        <meta name="keywords" content="tréningový denník, tréningový diár, online tréner, tréning, šport" />
        <meta name="robots" content="index, follow">

        <?php 
            // For Facebook Sharing
            $this->pageOgTitle = $this->pageTitle;
            $this->pageOgDesc = $this->pageDesc;
            $this->pageOgImage = Yii::app()->request->baseUrl . '/images/open-graph-sk-logo.png'; 
            $this->display_seo(); 
        ?>

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery-1.9.1.min.js"></script>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
    
    <?php if(!Yii::app()->user->isGuest):?>
        <div id="nojavascript">
            <div id="main-logo"></div>
            <?php echo Yii::t('global','Javascript is an important part of the technology on our web site. Please enable JavaScript in your browser.')?><br /><br />
            <?php echo Yii::t('global','If want to connect from a mobile device, please wait for a first release of our mobile app.')?><br /><br /><hr>
        </div>
        <script type="text/javascript">document.getElementById("nojavascript").style.display = "none";</script>
    <?php endif;?>
<!--[if gte IE 9]>
      <style type="text/css">
	.gradient {
	  filter: none;
	}
      </style>
    <![endif]-->
    <header>
        <?php if(!Yii::app()->user->isGuest && Yii::app()->user->roles == 'athlete'):?>
        <div id="notices" title="Notices">
            <script type="text/javascript">$(document).ready(function(){loadNotices();});</script>
        </div>
        <div id="notices-wrapper">
            <div id="notices-nib"></div>
            <div id="notices-header"><?php echo Yii::t('dashboard', 'Notices').' | '. Yii::app()->user->name?></div>
            <div id="notices-inner">
                <div id="notices-preloader">
                    <img src="<?php echo Yii::app()->request->baseUrl?>/images/css/loader.gif" width="134" height="112"/>
                </div>
                <div id="notice-response-target"></div>
            </div>
        </div>
        <?php endif;

            if(Yii::app()->user->isGuest){
                $this->createNav(array(
                    'home'=>$this->createLink(Yii::t('global','Home'),Yii::t('global','Home page'),''),
                    'about'=>$this->createLink(Yii::t('global','About'),Yii::t('global','About us'),'about'),
                    'contact'=>$this->createLink(Yii::t('global','Contact'),Yii::t('global','Contact us'),'contact'),
                    'signup'=>$this->createLink(Yii::t('global','Sign up'),Yii::t('global','Sign up for free'),'signup','right_element_ofmenu'),
                ));
            }
            elseif(Yii::app()->user->roles == 'athlete'){
                $this->createNav(array(
                    $this->createLink(Yii::t('global','Dashboard'),Yii::t('global','Dashboard'),'dashboard'),
                    $this->createLink(Yii::t('global','Plan'),Yii::t('global','Training plan'),'plan'),
                    $this->createLink(Yii::t('global','Diary'),Yii::t('global','Training diary'),'diary'),
                    $this->createLink(Yii::t('global','People'),Yii::t('global','Connect with your friends'),'people'),
                    $this->createLink(Yii::app()->user->name,Yii::t('global','Your profile'),'user/profile','right_element_ofmenu'),
                ));
            }
            elseif(Yii::app()->user->roles == 'coach'){
                $this->createNav(array(
                    $this->createLink(Yii::t('global','Dashboard'),Yii::t('global','Dashboard'),'dashboard'),
                    $this->createLink(Yii::t('global','People'),Yii::t('global','Find other people'),'people'),
                    $this->createLink(Yii::app()->user->name,Yii::t('global','Your profile'),'user/profile','right_element_ofmenu'),
                ));
            }
            elseif(Yii::app()->user->roles == 'admin'){
                $this->createNav(array(
                    $this->createLink(Yii::t('global','Home'),Yii::t('global','Home page'),''),
                    $this->createLink(Yii::t('global','Create new user'),Yii::t('global','Create new user'),'user/create'),
                    $this->createLink(Yii::t('global','Manage users'),Yii::t('global','Manage users'),'admin/users'),
                    $this->createLink(Yii::t('global','Update profile'),Yii::t('global','Your profile'),'user/update','right_element_ofmenu'),
                ));
            }
            $this->printNav();
            //** END OF NAVIGATION
        ?>
    </header>
    <div id="main-content">
        <?php if(!Yii::app()->user->isGuest):?>
            <div id="logout">
                <?php echo CHtml::link(Yii::t('global','Logout'),Yii::app()->params->homePath.'/site/logout',array('id'=>'logout-button','class'=>'styled-button','title'=>'Logout')); ?>     
            </div>
        <?php 
            else: ?>
                <div class="form" id="login">
                        <?php echo CHtml::beginForm($this->loginUser(),'post',array('id'=>'loginForm')); ?>
                        <div class="float-left">
                                <?php echo CHtml::activeLabel($this->login,'username')?>
                                <?php echo CHtml::activeTextField($this->login,'username',array('size'=>'18'))?>
                        </div>
                        <div class="float-left">
                                <?php echo CHtml::activeLabel($this->login,'password')?>
                                <?php echo CHtml::activePasswordField($this->login,'password', array('size'=>'18'))?>
                        </div>
                        <div class="float-left">
                            <input type="submit" value="<?php echo Yii::t('global','Login')?>" id="login-button" title="Login" />
                        </div>

                        <?php echo CHtml::endForm(); ?>
                </div><!--END OF LOGIN FORM-->
            <?php endif;?>
                
        <?php echo $content; ?>
                
        <div style="clear: both;"><!--Preventing collapse when containing only float elements--></div>
    </div><!--END OF MAIN CONTENT-->
    <div id="push" style="height: 210px"></div>
    
	<footer>   
            <div id="short-about-footer">
                <h3>About</h3>
                <?php echo Yii::t('global', 'MojTrening.sk is a full web application with purpose to plan, manage and evaluate training data in the form of a traning diary. It was created as a part of Bachelor thesis. Our target is to bring clean and healthy sport activity to everyone. No matter if you want to be a professional or train just for fun, cooperating with you is our pleasure.')?>
            </div>
            <div class="float-left">
                <h3>MojTrening.sk</h3>
                <?php 
                    echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/about" title="'.Yii::t('global','About us').'">'.Yii::t('global','About').'</a>';
                    echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/contact" title="'.Yii::t('global','Contact us').'">'.Yii::t('global','Contact').'</a>';
                    echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/faqs" title="'.Yii::t('global','Find the answer').'">'.Yii::t('global','Frequently Asked Questions').'</a>';
                    echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/terms" title="'.Yii::t('global','Terms of use').'">'.Yii::t('global','Terms of use').'</a>';
                    echo '<a href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/signup" title="'.Yii::t('global','Sign up for free').'">'.Yii::t('global','Sign up').'</a>';
                ?>
            </div>
            <div class="float-left">
                <h3><?php echo Yii::t('global','Language');?></h3>
                <div id="languages">
                    <?php echo CHtml::beginForm(array('site/changelang'), 'post') ?>
                        <a class="language" id="sk" title="<?php echo Yii::t('global','Switch to slovak language');?>">Slovenčina</a>
                        <a class="language" id="en" title="<?php echo Yii::t('global','Switch to english language');?>">Angličtina</a>
                        <?php echo CHtml::hiddenField('return', Yii::app()->request->url) ?>
                        <?php echo CHtml::hiddenField('lang', 'en') ?>
                    <?php echo CHtml::endForm() ?>    
                </div>
            </div>
            <div class="float-left" id="develop">
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo-footer.png" width="150" height="120">
            </div>
	</footer>  
    
    <?php if(!Yii::app()->user->isGuest && Yii::app()->user->roles == 'athlete'):?>  
        <script type="text/javascript">
            function loadNotices(){
                
                $.ajax({
                    type: "GET",
                    url: "<?php echo Yii::app()->createAbsoluteUrl('dashboard/loadnotices') ?>",
                    data: {},
                    success: function(data, status){
                        try{
                            xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                            comments = xmlDocumentElement.getElementsByTagName("comment");
                            requests = xmlDocumentElement.getElementsByTagName("request");
                            c_text = xmlDocumentElement.getElementsByTagName("commentecho").item(0).firstChild.data;
                            r_text = xmlDocumentElement.getElementsByTagName("requestecho").item(0).firstChild.data;
                            
                            $("#notices").html(comments.length+requests.length);
                            
                            response = "<div class=\"co_re_headers\"><?php echo Yii::t('dashboard', 'Sparring requests')?></div>";
                            if(xmlDocumentElement.getElementsByTagName("requestsstatus").length === 0){
                                response += parseNotice(requests,r_text);
                            }
                            else
                                response += "<div class=\"co_re_notfound\"><?php echo Yii::t('dashboard','No pending sparring requests')?></div>";
                            
                            response += "<div class=\"co_re_headers\"><?php echo Yii::t('dashboard', 'Comments')?></div>";
                            if(xmlDocumentElement.getElementsByTagName("commentsstatus").length === 0){
                                response += parseNotice(comments,c_text);
                            }
                            else
                                response += "<div class=\"co_re_notfound\"><?php echo Yii::t('dashboard','Nothing new happened')?></div>";
                            
                            $("#notice-response-target").append(response);
                            
                            $("#notices-preloader").hide();
                        }catch(e){
                        }
                    }
                });
                
            };
            
            function parseNotice(nodes,text){
                var response ="<ul>";

                for(var i=0; i < nodes.length; i++){

                    link = nodes.item(i).childNodes.item(0);
                    fullname = nodes.item(i).childNodes.item(1);
                    picture = nodes.item(i).childNodes.item(2);
                    date = nodes.item(i).childNodes.item(3);
                    
                    response += "<li><a href=\""+link.firstChild.data+"\">";
                    response += "<img class=\"response_picture\" src=\""+picture.firstChild.data+"\" height=\"40\" width=\"40\"/>";    

                    response += "<span class=\"bname\">"+fullname.firstChild.data+"</span>";
                    response += "<div class=\"date\">"+date.firstChild.data+"</div><br />";
                    response += text;

                    response += "</a></li>";
                }
                return response+"</ul>";
            }
        </script>
        
        <?php 
            Yii::app()->clientScript->registerScript('display-notices',
                    '$(document).ready(function(){
                            $("#notices").click(function(){
                                if($("#notices-wrapper").css("display") !== "none"){
                                    $("#notices-wrapper").hide();
                                    $("#notices-preloader").show();
                                }
                                else{
                                    $("#notice-response-target").html("");
                                    $("#notices-wrapper").show();
                                    loadNotices();
                                }
                            });
                    });',
                    CClientScript::POS_END);
        ?>
        <?php endif; ?>
        <?php 
            Yii::app()->clientScript->registerScript('change-lang',
                    '$(document).ready(function(){
                            $(".language").click(function(){
                                $("#lang").val($(this).attr("id"));
                                $("#languages").find("form").submit();
                            });
                    });',
                    CClientScript::POS_END);
        ?>
    
</body>
</html>
