
<?php
//var_dump(\Yii::$app->user->isGuest);exit;

if(!\Yii::$app->user->isGuest){
    echo "<h3>".'欢迎用户'.\Yii::$app->user->identity->username.'回来!'."</h3>";

}
if(!\Yii::$app->user->isGuest){
    echo \yii\helpers\Html::a('注销',['user/logout'],['class'=>'btn btn-big btn-primary pull-right']);
    echo \yii\helpers\Html::a('重置密码',['user/password'/*,'id'=>(\Yii::$app->user->identity->id)*/],['class'=>'btn btn-big btn-danger pull-right']);
}
?>