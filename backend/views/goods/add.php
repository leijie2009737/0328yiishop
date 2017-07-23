<?php
use yii\web\JsExpression;
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');

//echo $form->field($model,'logoFile')->fileInput();
echo $form->field($model,'logo')->hiddenInput();
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
//显示用户上传的图片
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,['id'=>'img','height'=>50]);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 100,
        'height' => 30,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片的地址赋值给logo字段
        $("#goods-logo").val(data.fileUrl);
        //将上传成功的图片回显
        $("#img").attr('src',data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//商品分类
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>';

//品牌分类
//$brands[0]="请选择";
echo $form->field($model,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brands,'id','name'),['prompt'=>'---请选择---']);
echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
echo $form->field($model,'is_on_sale',['inline'=>1])->radioList(\backend\models\Goods::$getIsOnSale);

echo $form->field($model,'sort')->textInput(['type'=>'number']);

//商品详情
$content =$goods_intro->content;
//editor.setContent($content);

echo $form->field($goods_intro
    ,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'en', //中文为 zh-cn
        //定制菜单
        'toolbars' => [
            ['fullscreen', 'source', 'undo', 'redo', 'bold', 'italic',
                'underline','fontborder', 'backcolor', 'fontsize', 'fontfamily',
                'justifyleft', 'justifyright','justifycenter', 'justifyjustify',
                'strikethrough','superscript', 'subscript', 'removeformat',
                'formatmatch','autotypeset', 'blockquote', 'pasteplain', '|',
                'forecolor', 'backcolor','insertorderedlist', 'insertunorderedlist',
                'selectall', 'cleardoc', 'link', 'unlink','emotion', 'help',                        'simpleupload','insertimage'
            ]
        ]
    ]
]);

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

//加载分类的js
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//加载js文件
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$categories[] = ['id'=>0,'parent_id'=>0,'name'=>'顶级分类','open'=>1];
$nodes = \yii\helpers\Json::encode($categories);
$nodeId = $category->id;
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode){
		            //console.log(treeNode.id);
		            //将当期选中的分类的id，赋值给parent_id隐藏域
		            $("#goods-goods_category_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
  
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);//展开全部节点
        
        //获取节点
        var node = zTreeObj.getNodeByParam("id", "{$nodeId}", null);
        //选中节点
        zTreeObj.selectNode(node);
        
        

JS

));