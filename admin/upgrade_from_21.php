<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable, $conf;

$query = 'ALTER TABLE ' . $prefixeTable . 'additionalpages
CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `pos` `pos` SMALLINT( 5 ) NULL DEFAULT NULL ,
CHANGE `lang` `lang` VARCHAR( 255 ) NULL DEFAULT NULL ,
CHANGE `text` `content` LONGTEXT NOT NULL ,
ADD `users` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD `groups` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD `permalink` VARCHAR( 64 ) NULL DEFAULT NULL;';
pwg_query($query);

$query = '
SELECT id, pos, title, lang
FROM '.$prefixeTable.'additionalpages
ORDER BY pos ASC, id ASC
;';
$result = pwg_query($query);
while ($row = mysql_fetch_assoc($result))
{
  $title = $row['title'];
  $authorized_users = 'NULL';
  $authorized_groups = 'NULL';

  if (strpos($title , '/user_id='))
  {
    $array = explode('/user_id=' , $title);
    $title = $array[0];
    $authorized_users = '"'.$array[1].'"';
  }
  if (strpos($title , '/group_id='))
  {
    $array = explode('/group_id=' , $title);
    $title = $array[0];
    $authorized_groups = '"'.$array[1].'"';
  }

  $position = $row['pos'];
  if ($row['pos'] === '0')
    $position = '-1';
  elseif (empty($row['pos']))
    $position = '0';

  $language = $row['lang'] != 'ALL' ? '"'.$row['lang'].'"' : 'NULL';

  $query = '
UPDATE '.$prefixeTable.'additionalpages
SET title = "'.addslashes($title).'",
    pos = '.$position.',
    lang = '.$language.',
    users = '.$authorized_users.',
    groups = '.$authorized_groups.'
WHERE id = '.$row['id'].'
;';
  pwg_query($query);
}

$old_conf = explode ("," , $conf['additional_pages']);

$new_conf = array(
  'show_home' => @($old_conf[2] == 'on'),
  'group_perm' => @($old_conf[6] == 'on'),
  'user_perm' => @($old_conf[7] == 'on'),
  'homepage' => null,
  );

$languages = explode('/', $old_conf[0]);
$new_conf['languages'] = array();
foreach($languages as $language)
{
  $array = explode(':', $language);
  if (!isset($array[1])) $new_conf['languages']['default'] = $array[0];
  else $new_conf['languages'][$array[0]] = $array[1];
}

$conf['additional_pages'] = $new_conf;

$query = '
UPDATE '.CONFIG_TABLE.'
SET value = "'.addslashes(serialize($new_conf)).'"
WHERE param = "additional_pages"
;';
pwg_query($query);

?>