<?php


	Route::get('/tester', function () {
		$response = Telegram::setWebhook(['url' => 'https://noha.jostana.com/bot/message']);
		dd($response);

		$original = new imagick('sss.jpg');
		dd($original);
		$acc = \App\Account::whereHas('photos');
		dd($acc);

		//		return $acc->photos;

		//		dd($acc->instagram_id);
		$username = 'therealktr22';
		$password = '---LOL---';
		\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = TRUE;

		$ig = new \InstagramAPI\Instagram(FALSE, FALSE);
		$ig->login($username, $password);
		dd($ig->people->getInfoById($acc->instagram_id)->getUser()->getIsPrivate());
		$feed = $ig->timeline->getUserFeed($acc->instagram_id);
		dd($feed->getItems()[ 0 ]->getUser()->getFollowerCount());
		dd($feed->getItems()[ 0 ]->getImageVersions2()->getCandidates()[ 0 ]->getUrl());
	});
	Route::any('/bot/message', 'BotController@message');
	Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
