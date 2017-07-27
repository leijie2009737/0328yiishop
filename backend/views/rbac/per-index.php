
<h1>权限列表</h1>

<table class="table table-responsive table-bordered">
    <?=\yii\helpers\Html::a('添加',['rbac/add-permission'],['class'=>'btn btn-sm btn-primary'])?>
    <thead>
    <tr>
        <th>名称(路由地址)</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    <tbody>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td style="width:156px;">
                <?=\yii\helpers\Html::a('修改',['rbac/edit-permission/','name'=>$model->name],['class'=>'btn btn-sm btn-warning'])?>
                <?=\yii\helpers\Html::a('删除',['rbac/del-permission','name'=>$model->name],['class'=>'btn btn-sm btn-danger pull-right'])?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
#$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/dataTables.bootstrap.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/dataTables.bootstrap.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({
language: {
        url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Chinese.json"
    }
});');




