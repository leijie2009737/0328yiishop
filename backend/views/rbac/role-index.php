<h1>角色列表</h1>
<table style="margin: 10px 0" class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['rbac/add-role'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>角色名称</th>
        <th>角色功能</th>
        <th>名下权限</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td><?=$model->description?></td>
            <td style="width:156px;">
                <?=\yii\helpers\Html::a('修改',['rbac/edit-role/','name'=>$model->name],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['rbac/del-role','name'=>$model->name],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
