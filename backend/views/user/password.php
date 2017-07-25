<?php
$form = \yii\bootstrap\ActiveForm::begin();
//var_dump(\Yii::$app->user->identity->password_hash);exit;
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'new_password1')->passwordInput();
echo $form->field($model,'new_password2')->passwordInput();

echo \yii\bootstrap\Html::submitButton('修改密码',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();