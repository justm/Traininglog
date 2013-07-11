<?php
/**
 * @var $this AdminController
 */
?>
<h2 class="flying">Managing users</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
                    'name'=>'id',
                    'htmlOptions'=>array('width'=>'40', 'style'=>'text-align:center; font-weight:bold'),
                ),
                array(
                    'name'=>'name',
                    'htmlOptions'=>array('style'=>'font-weight:bold'),
                ),
                array(
                    'name'=>'lastname',
                    'htmlOptions'=>array('style'=>'font-weight:bold'),
                ),
                array(
                    'name'=>'username',
                    'htmlOptions'=>array('style'=>'font-style:italic'),
                ),
                array(
                    'name'=>'id_role',
                    'header'=>'Role',
                    'value'=>'$data->idRole->name',
                    'filter'=>CHtml::listData($roles, 'id', 'name'),
                    'htmlOptions'=>array('width'=>'70', 'style'=>'text-align:center'),
                ),
		'email',
                'phone',
                array(
                    'name'=>'gender',
                    'header'=>'Gender',
                    'value'=>'($data->gender=="M")?("Male"):("Female")',
                    'filter'=> array(
                        'M'=>'Male',
                        'F'=>'Female',
                    )
                ),
                array(
                    'name'=>'account_status',
                    'value'=>'($data->account_status!=0)?("banned"):("")',
                    'htmlOptions'=>array('style'=>'text-align:center'),
                    'filter'=> array(
                        '0'=>'Active',
                        '9'=>'Banned',
                    )
                ),
		array(
                    'class'=>'CButtonColumn',
                    'deleteConfirmation'=>"js:'This user will be banned.'",
                    'template'=>'{view}{update}{delete}{enable}',
                    'buttons'=>array
                    (
                        'view' => array('url'=>'Yii::app()->createUrl("user/view", array("id"=>$data->username))'),
                        'update' => array('url'=>'Yii::app()->createUrl("user/update", array("id"=>$data->id))'),
                        'delete' => array('url'=>'Yii::app()->createUrl("user/bann", array("id"=>$data->id))','options'=>array('title'=>'Disable account')),
                        'enable'=>array(
                            'imageUrl'=>Yii::app()->params->homeUrl.'/images/enable.png',
                            'options'=>array('title'=>'Enable account'),
                            'click'=>'function(){enable($(this).parent().parent().children(":first-child").text())}',
                        )
                    ),
                    'htmlOptions'=>array('width'=>'80', 'style'=>'text-align:center'),
		),
	),
)); 

Yii::app()->clientScript->registerScript('enableAccount',
        '
            function enable(id){
                $.ajax({
                    type: "POST",
                    url: "'.Yii::app()->createUrl('user/enable').'",
                    data: {id:id},
                    success: function(data, status){
                        $.fn.yiiGridView.update("user-grid");
                    }
                });
            }
        ',
        CClientScript::POS_END);

?>