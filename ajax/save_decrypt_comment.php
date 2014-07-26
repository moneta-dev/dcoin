<?php
session_start();

if ( empty($_SESSION['user_id']) )
	die('!user_id');

define( 'DC', TRUE);

define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

//require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );
require_once( ABSPATH . 'phpseclib/Math/BigInteger.php');
require_once( ABSPATH . 'phpseclib/Crypt/Random.php');
require_once( ABSPATH . 'phpseclib/Crypt/Hash.php');
require_once( ABSPATH . 'phpseclib/Crypt/RSA.php');

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

if (!empty($_SESSION['restricted']))
	die('Permission denied');

if ( !check_input_data ($_REQUEST['id'] , 'int') )
	die('error id');

if ( !empty($data['parent_id']) && !check_input_data ($data['parent_id'] , 'int') )
	die('error parent_id');

if ( $_REQUEST['type']!=='dc_transactions' && $_REQUEST['type']!=='cash_requests' )
	die('error type');

define('MY_PREFIX', get_my_prefix($db));

// == если мы майнер и это dc_transactions, то сюда прислан зашифрованный коммент, который можно расшифровать только нод-кдючем
$miner_id = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT `miner_id`
		FROM `".DB_PREFIX."miners_data`
		WHERE `user_id` = {$_SESSION['user_id']}
		LIMIT 1
		", 'fetch_one' );
if ($miner_id>0 && $_REQUEST['type']=='dc_transactions' ) {
	$node_private_key = get_node_private_key($db, MY_PREFIX);
	// расшифруем коммент
	$rsa = new Crypt_RSA();
	$rsa->loadKey($node_private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
	$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
	$_REQUEST['comment'] = $rsa->decrypt(hextobin($_REQUEST['comment']));
	unset($rsa);
}
// ==

// $_REQUEST['comment'] - может содержать зловред
$comment = filter_var($_REQUEST['comment'], FILTER_SANITIZE_STRING);
$comment = str_ireplace(array('\'', '"'),  array('', ''), $comment);
$comment = $db->escape($comment);


$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "SET NAMES UTF8");
$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__,"
		UPDATE `".DB_PREFIX.MY_PREFIX."my_{$_REQUEST['type']}`
		SET `comment`='{$comment}',
			   `comment_status` = 'decrypted'
		WHERE `id` = {$_REQUEST['id']}
		");
print htmlentities($comment);

?>