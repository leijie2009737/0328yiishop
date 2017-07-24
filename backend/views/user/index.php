
<?=\yii\helpers\Html::a('添加',['user/add'],['class'=>'btn btn-sm btn-primary'])?>
<table style="margin: 20px 0" class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>密码</th>
            <th>邮箱</th>
            <th>操作</th>

        </tr>
        <?php foreach($models as $model): ?>

    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->username?></td>
        <td>********</td>
        <td><?=$model->email?></td>
        <td><?=\yii\helpers\Html::a('修改',['user/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
            <?=\yii\helpers\Html::a('删除',['user/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>

    </tr>


<?php endforeach;?>

</table>