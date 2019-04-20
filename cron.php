<?php
error_reporting(E_ALL);
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/config.php';

$keitaro_campaigns = str_replace(' ', '', $keitaro_campaigns);
$keitaro_campaigns = explode(',', $keitaro_campaigns);

//
// получаем список аккаунтов
//
$accounts_dirty = explode( "\n", file_get_contents( __DIR__ . '/accounts.txt' ) );
$accounts = parse_accounts($accounts_dirty);

//
// начинем идти по списку аккаунтов и отрабатывать каждый
//
foreach ($accounts as $account) {

	//
	// если какой-то косяк с токеном, то пропускаем аккаунт
	//
	if (! @$account['access_token']) continue;

	//
	// если не заданы рекламные кабинеты, то пропускаем аккаунт
	//
	if (! @$account['cabinets']) continue;

	//
	// посылаем тестовый запрос API Facebook, чтобы проверить работоспособность токена
	// если в ответ прилетает ошибка, то пропускаем аккаунт
	//
	$me = getFacebookApi('/me', ['access_token' => $account['access_token']]);
	if ( isset($me['error']) ) continue;

	//
	// начинаем идти по списку рекламных кабинетов аккаунтов
	//
	foreach ($account['cabinets'] as $cabinet_id) {

		//
		// получаем список адсетов текущего кабинета
		//
		$adsets = [];
		$adsets_dirty = getFacebookApi('/act_' . $cabinet_id . '/adsets', ['access_token' => $account['access_token']]);
		$adsets = array_merge($adsets_dirty['data'], $adsets);
		while ( isset($adsets_dirty['paging']['next']) ) {
			$adsets_dirty = json_decode(file_get_contents($adsets_dirty['paging']['next']), TRUE);
			$adsets = array_merge($adsets_dirty['data'], $adsets);
			usleep(500000);
		}

		//
		// после того, как сформировали список адсетов, запрашиваем стату по каждому
		// и посылаем стату по трекерам
		//
		foreach ($adsets as $adset) {

			$insights = [];
			$params = [
				'access_token' => $account['access_token'],
				'fields' => 'spend',
				'time_range' => ['since' => '2018-01-01', 'until' => date('Y-m-d')],
				'time_increment' => '1'
			];
			$insights_dirty = getFacebookApi('/' . $adset['id'] . '/insights', $params);
			$insights = array_merge($insights_dirty['data'], $insights);
			while( isset($insights_dirty['paging']['next']) ) {
				$insights_dirty = json_decode(file_get_contents($insights_dirty['paging']['next']), TRUE);
				$insights = array_merge($insights_dirty['data'], $insights);
				usleep(500000);
			}

			//
			// стату по дням получили
			// теперь идем по каждому дню этой статы и отправляем ее в нужные трекеры
			//

			foreach ($insights as $insight) {

				if ($keitaro) {	

					$keitaro_data = [
						'start_date' => $insight['date_start'] . ' 00:00:00',
						'end_date' => $insight['date_start'] . ' 23:59:59',
						'cost' => $insight['spend'],
						'currency' => $keitaro_currency,
						'timezone' => $keitsro_timezone,
						'only_campaign_uniques' => 1,
						'filters' => [ 'sub_id_' . $keitaro_subid => $adset['id'] ]
					];

					foreach ($keitaro_campaigns as $keitaro_campaign) {
						echo $keitaro_campaign;
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'http://' . $keitaro_domain . '/admin_api/v1/campaigns/' . $keitaro_campaign . '/update_costs');
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Api-Key: ' . $keitaro_api_key));
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($keitaro_data)); 
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_exec($ch);
					}
					

				}
			} 

		}

	}

} 