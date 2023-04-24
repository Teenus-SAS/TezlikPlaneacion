<?php
session_start();

require_once '../controllers/templateController.php';
$template = new templateController();

$template->ctrTemplateGlobal();
