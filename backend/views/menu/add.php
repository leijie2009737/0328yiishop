<?php
$form =\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');

echo $form->field($model,'parent_id')->dropDownList($models,['prompt'=>'---请选择上级菜单---']);
echo $form->field($model,'url')->dropDownList(
    \yii\helpers\ArrayHelper::map(Yii::$app->authManager->getPermissions(),'name','name'),['prompt'=>'---请选择路由地址---']);
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();