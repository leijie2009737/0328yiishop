<h1>权限列表</h1>
<!--<form action="" method="get">
    <div class="input-group col-md-3 pull-right" >
        <input name="keywords" type="text" class="form-control"placeholder="请输入文章名字" / >
        <span class="input-group-btn">
               <button class="btn btn-info btn-search">查找</button>
            </span>
    </div>
</form>-->
<table style="margin: 10px 0" class="table table-bordered table-condensed">
    <?=\yii\helpers\Html::a('添加',['rbac/add-permission'],['class'=>'btn btn-sm btn-primary'])?>
    <tr>
        <th>名称(路由地址)</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td style="width:156px;">
                <?=\yii\helpers\Html::a('修改',['rbac/edit-permission/','name'=>$model->name],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['rbac/del-permission','name'=>$model->name],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
</table>
