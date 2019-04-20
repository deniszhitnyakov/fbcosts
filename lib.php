<?php
function getFacebookApi($path, $data) {
	$url = 'https://graph.facebook.com/v3.0' . $path . '?' . http_build_query($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$result = curl_exec($ch);
	curl_close($ch);
	usleep (500000);
	return json_decode($result, TRUE);
}

function parse_accounts($accounts_dirty) {

	$accounts = [];

	foreach ($accounts_dirty as $acc) {

		//
		// если в конце записи аккаунта есть комментарий, то удаляем его
		//
		if ( preg_match('/\/\/.*/', $acc, $comments) ) {
			$acc = preg_replace('/\/\/.*/', '', $acc);
			$comment = str_replace('//', '', $comments[0]);
		}

		//
		// если указан список рекланмых кабинетов в начале строки, то парсим его
		//
		if ( preg_match('/\(.*\)/', $acc, $cabinets) ) {
			$cabinets = $cabinets[0];
			$cabinets = str_replace('(', '', $cabinets);
			$cabinets = str_replace(')', '', $cabinets);
			$cabinets = str_replace(' ', '', $cabinets);
			$cabinets = explode(',', $cabinets);

			$acc = preg_replace('/\(.*\)/', '', $acc);
		}

		$accounts[] = ['access_token' => $acc, 'cabinets' => @$cabinets ? $cabinets : 0, 'comment' => @$comment ? $comment : ''];

	} 

	return $accounts;

} 