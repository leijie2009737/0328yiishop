<table class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['goods/add'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>ID</th>
        <th>货号</th>
        <th>商品名称</th>
        <th>LOGO图片</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->sn?></td>
            <td><?=$model->name?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo/*?$student->img:'/upload/default.png'*/,['height'=>50])?></td>
            <td><?=$model->shop_price?></td>
            <td><?=$model->stock?></td>
            <td><?=$model->is_on_sale?'在售':'下架'?></td>
            <td><?=\yii\helpers\Html::a('修改',['goods/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['goods/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);