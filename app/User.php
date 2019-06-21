<?php

	namespace App;

	use Jenssegers\Mongodb\Auth\User as Authenticatable;


	class User extends Authenticatable {

		protected $fillable = [
			'first_name', 'username', 'telegram_id', 'language', 'state', 'memory', 'last_name', 'has_photo',
		];

		public function photos(): \Jenssegers\Mongodb\Relations\EmbedsMany {
			return $this->embedsMany(Photo::class, 'photos');
		}

		public function accounts() {
			return $this->hasMany(Account::class);
		}

	}
