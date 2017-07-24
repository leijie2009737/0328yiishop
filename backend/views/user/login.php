<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'email');

echo $form->field($model, 'auth_key')->checkbox();
echo \yii\bootstrap\Html::submitButton('登陆',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();