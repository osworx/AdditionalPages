<?phpif (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');// Enregistrement de la configurationif (isset($_POST['submit'])){  $params = array('show_home', 'group_perm', 'user_perm');  $new_conf = array();  foreach ($params as $param)  {    $new_conf[$param] = isset($_POST[$param]);  }  $new_conf['languages'] = array();	foreach($_POST['menu_lang'] as $language_code => $name)  {		if (!empty($name))      $new_conf['languages'][$language_code] = $name;	}  $new_conf['homepage'] = $conf['additional_pages']['homepage'];    $query = 'UPDATE ' . CONFIG_TABLE . '  SET value="'.addslashes(serialize($new_conf)).'"  WHERE param="additional_pages"  LIMIT 1';    pwg_query($query);    array_push($page['infos'], l10n('ap_conf_saved'));    $conf['additional_pages'] = $new_conf;}// Gestion des langues pour le bloc menu$template->assign('LANG_DEFAULT_VALUE', @$conf['additional_pages']['languages']['default']);foreach (get_languages() as $language_code => $language_name){	$template->append('language', array(    'LANGUAGE_NAME' => $language_name,    'LANGUAGE_CODE' => $language_code,    'VALUE' => isset($conf['additional_pages']['languages'][$language_code]) ? $conf['additional_pages']['languages'][$language_code] : '',    )  );}// Parametrage du template$template->assign('ap_conf', $conf['additional_pages']);$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__) . '/template/config.tpl'));$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');?>