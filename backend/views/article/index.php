<table class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['article/add'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td style="width:200px"><?=$model->name?></td>
            <td style="width: 528px"><?=$model->intro?></td>
            <td><?=$model->articleCategory->name?></td>
            <td><?=$model->sort?></td>
            <td><?=$model->statusoptions[$model->status]?></td>
            <td><?=date("Y-m-d",$model->create_time)?></td>
            <td style="width:156px;"><?=\yii\helpers\Html::a('查看',['article/show/','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
                <?=\yii\helpers\Html::a('修改',['article/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['article/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);