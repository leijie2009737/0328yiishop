<?php
if(\Yii::$app->user->isGuest){
    echo \yii\helpers\Html::a('登陆',['user/login'],['class'=>'btn btn-big btn-primary pull-right']);
} ?>
<?php
//var_dump(\Yii::$app->user->isGuest);exit;

if(!\Yii::$app->user->isGuest){
    echo "<h3>".'欢迎用户'.\Yii::$app->user->identity->username.'回来!'."</h3>";
}
if(!\Yii::$app->user->isGuest){
    echo \yii\helpers\Html::a('注销',['user/logout'],['class'=>'btn btn-big btn-primary pull-right']);
}
?>
<?=\yii\helpers\Html::a('添加',['user/add'],['class'=>'btn btn-big btn-primary'])?>
<table style="margin: 20px 0" class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>密码</th>
            <th>邮箱</th>
            <th>最后登录时间</th>
            <th>最后登录IP</th>
            <th>操作</th>

        </tr>
        <?php foreach($models as $model): ?>

    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->username?></td>
        <td>********</td>
        <td><?=$model->email?></td>
        <td><?=($model->last_login_time)?date('Y-m-d:H:i:s',$model->last_login_time):(date('Y-m-d:H:i:s',$model->created_at))?></td>
        <td><?=long2ip($model->last_login_ip)?></td>
        <td><?=\yii\helpers\Html::a('修改',['user/edit/','id'=>$model->id],['class'=>'btn btn-sm btn-warning'])?>
            <?=\yii\helpers\Html::a('删除',['user/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger pull-right'])?></td>

    </tr>


<?php endforeach;?>

</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$page,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);