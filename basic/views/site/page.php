<?php
/** @var $this yii\web\View */
/** @var $page_model \app\models\Page */

$this->title = "Panoff Design: " . $page_model->title;
$this->params['breadcrumbs'][] = $page_model->title;

echo $page_model->content;