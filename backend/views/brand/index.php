<table class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['brand/add'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo/*?$student->img:'/upload/default.png'*/,['height'=>50])?></td>
            <td><?=$model->sort?></td>
            <td><?=$model->statusoptions[$model->status]?></td>
            <td><?=\yii\helpers\Html::a('修改',['brand/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['brand/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page]);