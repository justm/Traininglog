
<?php if(Yii::app()->user->hasFlash('uploading-failed')):?>
        <div class="flash-error">
                <?php echo Yii::app()->user->getFlash('uploading-failed'); ?>
        </div>
<?php endif;?>
<?php if(Yii::app()->user->hasFlash('file-uploaded')):?>
        <div class="flash-success">
                <?php echo Yii::app()->user->getFlash('file-uploaded'); ?>
        </div>
<?php endif;?>