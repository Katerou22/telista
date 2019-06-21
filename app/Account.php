<?php

	namespace App;

	use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

	class Account extends Eloquent {
		protected $fillable = [
			'user_id',
			'has_photo',
			'instagram_id',
			'username',
		];

		public function photos(): \Jenssegers\Mongodb\Relations\EmbedsMany {
			return $this->embedsMany(Photo::class, 'photos');
		}

	}
