<?php

	namespace App\Console\Commands;

	use App\Account;
	use Illuminate\Console\Command;

	class CheckAccountsCommand extends Command {

		protected $signature = 'check:account';

		public function handle() {
			$accounts = Account::all();

		}
	}
