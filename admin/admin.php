<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
load_language('plugin.lang', AP_PATH);

global $conf, $template;

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$my_base_url = get_admin_plugin_menu_link(__FILE__);

$query = 'SELECT id
FROM ' . ADD_PAGES_TABLE . '
LIMIT 1
;';
$page_exist = array_from_query($query, 'id');

if (!isset($_GET['tab']))
{
  $page['tab'] = $page_exist ? 'manage' : 'add_page';
}
else
{
  $page['tab'] = $_GET['tab'];
}

include(AP_PATH.'admin/'.$page['tab'].'.inc.php');

$tabsheet = new tabsheet();
if ($page_exist)
{
  $tabsheet->add('manage', l10n('Manage'), $my_base_url.'&amp;tab=manage');
}
$tabsheet->add('add_page', l10n('ap_add_page'), $my_base_url.'&amp;tab=add_page');
$tabsheet->add('config', l10n('Configuration'), $my_base_url.'&amp;tab=config');
if ($page['tab'] == 'edit_page')
{
  $tabsheet->add('edit_page', l10n('ap_edit_page'), $my_base_url.'&amp;tab=edit_page&amp;edit='.$_GET['edit']);
}
$tabsheet->select($page['tab']);
$tabsheet->assign();

?>