<?php

	namespace App;

	use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

	class Order extends Eloquent {
		protected $fillable = [
			'instagram_id',
			'instagram_user',
			'has_caption',
			'has_image',
			'amount',
			'account_id',
			'current_value',
		];
	}
