<?php
if (!defined('DC')) die("!defined('DC')");

$tpl['data']['type'] = 'change_seller_hold_back';
$tpl['data']['type_id'] = ParseData::findType($tpl['data']['type']);
$tpl['data']['time'] = time();
$tpl['data']['user_id'] = $user_id;

$tpl['hold_back'] = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT `arbitration_days_refund`,
					 `seller_hold_back_pct`
		FROM `".DB_PREFIX."users`
		WHERE `user_id` = {$user_id}
		", 'fetch_array');

$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT *
		FROM `".DB_PREFIX."orders`
		WHERE `seller` = {$user_id}
		ORDER BY `time` DESC
		LIMIT 20
		");
while ($row = $db->fetchArray($res)) {
	if ($row['status'] == 'refund') {
		if (empty($_SESSION['restricted'])) {
			$data = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
					SELECT `comment`,
								 `comment_status`
					FROM `".DB_PREFIX.MY_PREFIX."my_comments`
					WHERE `id` = {$row['id']} AND
								 `type` = 'seller'
					LIMIT 1
					", 'fetch_array');
			$row['comment'] = $data['comment'];
			$row['comment_status'] = $data['comment_status'];
		}
	}
	$tpl['my_orders'][] = $row;
}

$tpl['currency_list'] = get_currency_list($db);

$tpl['last_tx'] = get_last_tx($user_id, types_to_ids(array('change_seller_hold_back', 'money_back')), 3);
if (!empty($tpl['last_tx']))
	$tpl['last_tx_formatted'] = make_last_txs($tpl['last_tx']);

require_once( ABSPATH . 'templates/arbitration_seller.tpl' );
?>