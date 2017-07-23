<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>货号</th>
        <th>商品名称</th>
        <th>状态</th>
        <th>LOGO图片</th>
<!--        <th>商品价格</th>-->
<!--        <th>库存</th>-->
<!--        <th>是否在售</th>-->
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->sn?></td>
            <td><?=$model->name?></td>
            <td><?=$model->status?'已恢复':'已删除'?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo/*?$student->img:'/upload/default.png'*/,['height'=>50])?></td>
            <td>
                <?=\yii\bootstrap\Html::a('恢复',['goods/recover','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
                <?=\yii\bootstrap\Html::a('清除',['goods/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
