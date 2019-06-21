<?php


	Route::get('/tester', function () {
		dd(\App\Account::all());
		$username = 'therealktr22';
		$password = '---LOL---';
		\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = TRUE;

		$ig = new \InstagramAPI\Instagram(FALSE, FALSE);
		$ig->login($username, $password);

		$feed = $ig->timeline->getUserFeed('1727863964');

		dd($feed->getItems()[ 0 ]->getImageVersions2()->getCandidates()[ 0 ]->getUrl());
	});
	Route::get('/message', 'BotController@message');
