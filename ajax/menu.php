<?php
session_start();

if ( empty($_SESSION['user_id']) )
	die('');
$user_id = intval($_SESSION['user_id']);
	
define( 'DC', TRUE);

define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

set_time_limit(0);

//require_once( ABSPATH . 'includes/errors.php' );
if (file_exists(ABSPATH . 'db_config.php')) {
	require_once( ABSPATH . 'db_config.php' );
	require_once( ABSPATH . 'includes/autoload.php' );
	$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
}
else {
	die ('');
}


if ( isset($db) && get_community_users($db) )
	define('COMMUNITY', true);

if ( defined('COMMUNITY') ) {
	$pool_admin_user_id = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `pool_admin_user_id`
				FROM `".DB_PREFIX."config`
				", 'fetch_one' );
	if ( (int)$_SESSION['user_id'] === (int)$pool_admin_user_id ) {
		define('POOL_ADMIN', true);
	}
}

if ($user_id>0 && $user_id!='wait') {
	$data = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT `name`,
						 `avatar`
			FROM `".DB_PREFIX."users`
			WHERE `user_id`= {$user_id}
			", 'fetch_array');
	$tpl['name'] = $data['name'];
	$tpl['avatar'] = $data['avatar'];
}
if (empty($tpl['name'])) {
	$miner = $db->query(__FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__, "
			SELECT `miner_id`
			FROM `" . DB_PREFIX . "miners_data`
			WHERE `user_id` = {$user_id}
			", 'fetch_one');
	if ($miner)
		$tpl['name'] = 'ID '.$user_id.' (miner)';
	else
		$tpl['name'] = 'ID '.$user_id;
}

$lang = get_lang();
require_once( ABSPATH . 'lang/'.$lang.'.php' );
$tpl['lang'] = $lang;

$tpl['face_urls'] = array();
if (empty($tpl['avatar'])) {
	$data = $db->query(__FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__, "
		SELECT `photo_block_id`,
					 `photo_max_miner_id`,
					 `miners_keepers`
		FROM `" . DB_PREFIX . "miners_data`
		WHERE `user_id` = {$user_id}
		LIMIT 1
		", 'fetch_array');
	if ($data) {
		// получим ID майнеров, у которых лежат фото нужного нам юзера
		$miners_ids = ParseData::get_miners_keepers($data['photo_block_id'], $data['photo_max_miner_id'], $data['miners_keepers'], true);
		if ($miners_ids) {
			$hosts = $db->query(__FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__, "
					SELECT `host`
					FROM `" . DB_PREFIX . "miners_data`
					WHERE `miner_id` IN  (" . implode(',', $miners_ids) . ")
					", 'array');
			for ($i = 0; $i < sizeof($hosts); $i++) {
				$tpl['face_urls'][] = "{$hosts[$i]}public/face_{$user_id}.jpg";
			}
		}
	}
}

$tpl['no_avatar'] = 'img/noavatar.png';

$tpl['ver'] = file_get_contents(ABSPATH.'version');

$tpl['miner_id'] = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__,"
		SELECT `miner_id`
		FROM `".DB_PREFIX."miners_data`
		WHERE `user_id` = {$user_id}
		", 'fetch_one' );

// ID блока вверху
$tpl['block_id'] = get_block_id($db);
if ($_SESSION['user_id']) {
	define('MY_PREFIX', get_my_prefix($db));
	$tpl['my_notice'] = get_my_notice_data();
}

// для сингл-мода, кнопка включения и выключения демонов
if ( !defined('COMMUNITY') ) {
	$script_name = $db->query(__FILE__, __LINE__, __FUNCTION__, __CLASS__, __METHOD__, "
		SELECT `script_name`
		FROM `" . DB_PREFIX . "main_lock`
		", 'fetch_one');
	if ($script_name == 'my_lock')
		$tpl['daemons_status'] = '<li title="'.$lng['daemons_status_off'].'"><a href="#" id="start_daemons" style="color:#C90600"><i class="fa fa-power-off" style="font-size: 20px"></i></a></li>';
	else
		$tpl['daemons_status'] = '<li title="'.$lng['daemons_status_on'].'"><a href="#" id="stop_daemons" style="color:#009804"><i class="fa fa-power-off" style="font-size: 20px"></i></a></li>';
}

require_once( ABSPATH . 'templates/menu.tpl' );
?>