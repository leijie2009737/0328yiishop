<form action="" method="get">
    <div class="input-group col-md-3 pull-right" >
        <input name="keywords" type="text" class="form-control"placeholder="请输入分类名字" / >
        <span class="input-group-btn">
               <button class="btn btn-info btn-search">查找</button>
            </span>
    </div>
</form>
<table class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['goods-category/add'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>父级分类id</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->parent_id?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\helpers\Html::a('修改',['goods-category/edit/','parent_id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['goods-category/del','parent_id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);