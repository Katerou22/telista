<?php


	Route::get('/tester', function () {
		$username = 'therealktr22';
		$password = '---LOL---';
		\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = TRUE;

		$ig = new \InstagramAPI\Instagram(FALSE, FALSE);
		$ig->setProxy('http://localhost:8118');
		$ig->login($username, $password);
		$feed = $ig->timeline->getUserFeed('1417570069');
		dd($feed);

	});
	Route::get('/users', function () {
		return \App\User::all();
	});
	Route::get('/accounts', function () {
		return \App\Account::all();
	});
	Route::any('/bot/message', 'BotController@message');
	Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
