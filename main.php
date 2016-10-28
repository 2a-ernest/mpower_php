<?php
/*Author Asare-Asiedu Ernest
Provide integration with Mpower mobile payment platform*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}


if(!class_exists('ExtensionMpower')){
    include_once('core/mpower.php');
}
$controllers = array(
    'storefront' => array(
        'pages/extension/mpower',
        'responses/extension/mpower',
        'blocks/extension/mpower',
        'api/extension/mpower'),
    'admin' => array(
        'pages/extension/mpower',
        'responses/extension/mpower',
        'api/extension/mpower',
        'task/extension/mpower'));

$models = array(
    'storefront' => array(
        'extension/mpower'),
    'admin' => array(
        'extension/mpower'));

$templates = array(
    'storefront' => array(
        'pages/extension/mpower.tpl',
        'responses/extension/mpower.tpl'),
    'admin' => array(
        'pages/extension/mpower.tpl',
        'responses/extension/mpower.tpl'));

$languages = array(
    'storefront' => array(
        'english/mpower/mpower'),
    'admin' => array(
        'english/mpower/mpower'));

