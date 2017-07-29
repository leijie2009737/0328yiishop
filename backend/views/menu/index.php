
<table class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['menu/add'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>ID</th>
        <th>菜单名称</th>
        <th>上级分类</th>
        <th>路由地址</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?='顶级分类'?></td>
            <td><?=$model->url?></td>
            <td><?=\yii\helpers\Html::a('修改',['menu/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['menu/del/','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
            <?php foreach($model->children as $child):?>
                <tr>
                    <td></td>
                    <td>——<?=$child->name?></td>
                    <td><?=$model->parentId->name?></td>
                    <td><?=$child->url?></td>

                    <td><?=\yii\helpers\Html::a('修改',['menu/edit/','id'=>$child->id],['class'=>'btn btn-sm btn-warning'])?>
                        <?=\yii\helpers\Html::a('删除',['menu/del/','id'=>$child->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
                </tr>
            <?php endforeach;?>
    <?php endforeach;?>
</table>
