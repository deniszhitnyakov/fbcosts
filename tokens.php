<?php
error_reporting(E_ALL);
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/config.php';
$accounts_dirty = explode( "\n", file_get_contents( __DIR__ . '/accounts.txt' ) );
$accounts = parse_accounts($accounts_dirty);

foreach ($accounts as $account) {

	$me = getFacebookApi('/me', ['access_token' => $account['access_token']]);
	if ( isset($me['error']) ) {
		echo $account['comment'] . ' - <span style="fotn-weight:bold;color:red;">ОШИБКА</span>';
	} else {
		echo $account['comment'] . ' - <span style="fotn-weight:bold;color:green;">OK</span>';
	}

}