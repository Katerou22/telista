<?php


	Route::get('/tester', function () {

	});
	Route::get('/users', function () {
		return \App\User::all();
	});
	Route::get('/accounts', function () {
		return \App\Account::all();
	});
	Route::any('/bot/message', 'BotController@message');
	Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
