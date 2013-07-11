<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle=Yii::app()->name . ' - Contact Us';
?>
<div id="pagelogo"><img src="<?php echo Yii::app()->request->baseUrl?>/images/css/pageLogo/contact.png" height="42" width="263"/></div>
<h2 class="flying"><?php echo Yii::t('contact','Contact us')?></h2>

<?php if(Yii::app()->user->hasFlash('contact')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('contact'); ?>
</div>

<?php else: ?>

<p>
<?php
    echo Yii::t('contact','If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.');
?>
</p>
<p class="note"><?php echo Yii::t('global','Fields with ');?><span class="required">*</span><?php echo Yii::t('global',' are required');?></p>

<div class="form">
<?php echo CHtml::beginForm('','post',array('id'=>'contact-form')); ?>
<?php echo Chtml::errorSummary($model); ?>
	<div class="row">
		<?php echo CHtml::activeLabel($model,'name',array('class'=>'requiredField'))?>
                <?php echo CHtml::activeTextField($model,'name',array('size'=>'25'))?>
        </div>
        <div class="row">
                <?php echo CHtml::activeLabel($model,'email',array('class'=>'requiredField'))?>
                <?php echo CHtml::activeTextField($model,'email', array('size'=>'25'))?>

	</div>
        <div class="row">
                <?php echo CHtml::activeLabel($model,'subject',array('class'=>'requiredField'))?>
                <?php echo CHtml::activeTextField($model,'subject', array('size'=>60,'maxlength'=>128))?>
	</div>
        <div class="row">
                <?php echo CHtml::activeLabel($model,'body',array('class'=>'requiredField'))?>
                <?php echo CHtml::activeTextArea($model,'body', array('cols'=>60,'rows'=>8))?>
	</div>

        <?php if(CCaptcha::checkRequirements()): ?>
            <div class="row float-left">
                <div class="float-left">
                    <?php echo CHtml::activeLabel($model,'verifyCode',array('class'=>'requiredField')); ?>
                    <?php echo CHtml::activeTextField($model,'verifyCode'); ?><br />
                    <?php echo CHtml::error($model,'verifyCode'); ?>
                </div>
                <span id="captcha" class="float-left"><?php 
                    $this->widget('CCaptcha',array(
                        'buttonLabel'=>Yii::t('contact','Click here to refresh'),
                        'buttonOptions'=>array('id'=>'ccaptchaButton'),
                        'clickableImage'=>true,
                        'imageOptions'=>array('title'=>Yii::t('contact','Click here to refresh')),
                        ));
                ?></span>
                <div class="note" style="clear: both"><?php echo Yii::t('contact','Please retype the word appearing in the picture')?></div>

            </div>
        <?php endif; ?>
        <div class="row buttons" style="clear: both;width: 500px">
            <input type="submit" class="big-button" value="<?php echo Yii::t('global','Send')?>" id="send-button" title="<?php echo Yii::t('contact','Send message');?>" style="margin: auto"/>
	</div>

    <?php echo CHtml::endForm(); ?>
    </div><!--end of form -->
<?php endif; ?>