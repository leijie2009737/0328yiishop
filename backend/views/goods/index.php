
<div class="table-responsive">
    <form action="" method="get">
        <div class="input-group col-md-2 pull-right" >
            <input name="price2" type="text" class="form-control"placeholder="商品最高价格" / >
            <span class="input-group-btn">
               <button class="btn btn-info btn-search">查找</button>
            </span>
        </div>
        <div class="input-group  pull-right" >：</div>
        <div class="input-group col-md-2 pull-right" >
            <input name="price1" type="text" class="form-control"placeholder="商品起始价格" />
        </div>
        <div class="input-group col-md-2 pull-right" >
            <input name="sn" type="text" class="form-control"placeholder="商品货号" / >
        </div>
        <div class="input-group col-md-2 pull-right" >
            <input name="name" type="text" class="form-control"placeholder="商品名称" / >
        </div>
    </form>


    <table style="margin: 20px 0;" class="table table-bordered table-condensed">
        <?=\yii\helpers\Html::a('添加',['goods/add'],['class'=>'btn btn-sm btn-primary'])?>
        <?=\yii\helpers\Html::a('回收站',['goods/back'],['class'=>'btn btn-sm btn-primary'])?>
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
            <td style="width: 208px"><?=\yii\helpers\Html::a('修改',['goods/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('查看',['goods/show/','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
                <?=\yii\helpers\Html::a('预览',['goods/preview/','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
                <?=\yii\helpers\Html::a('删除',['goods/recycle','id'=>$model->id],['class'=>'btn btn-sm btn-danger '])?></td>
        </tr>
    <?php endforeach;?>
</table>
</div>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);