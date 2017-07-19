<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>新闻详情</title>
</head>
<body>
<h1 style="text-align: center"><?=$model2->name?></h1>
<div style="float: left"><strong> 首页 > <?=$model1->name?></strong></div>
<div style="text-align: right"><strong>发布时间：</strong><?=date("Y-m-d",$model2->create_time)?></div>
<hr>
<?php
echo $model->content;
?>
</body>
</html>