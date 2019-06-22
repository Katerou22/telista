<?php

	namespace App;

	use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

	class Photo extends Eloquent {
		protected $fillable = [
			'set_at',
		];
		protected $dates = ['set_at'];
	}
