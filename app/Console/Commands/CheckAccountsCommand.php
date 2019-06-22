<?php

	namespace App\Console\Commands;

	use App\Account;
	use App\Order;
	use Illuminate\Console\Command;
	use Imagick;

	class CheckAccountsCommand extends Command {

		protected $signature = 'check:account';

		public function handle() {
			logger('Working');
			$accs = Account::all();
			$username = 'therealktr22';
			$password = '---LOL---';
			\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = TRUE;

			$ig = new \InstagramAPI\Instagram(FALSE, FALSE);
			$ig->login($username, $password);

			$telista = [
				'noha',
				'نوها',
			];
			foreach ($accs as $acc) {
				if ($acc->photos->count() > 0) {
					$photo = $acc->photos->where('status', 'init')->first();
					if ($photo === NULL) {
						continue;
					}

					$feed = $ig->timeline->getUserFeed($acc->instagram_id);

					$user = $ig->people->getInfoById($acc->instagram_id)->getUser();
					if ($user->getIsPrivate()) {
						$photo->status = 'private';
						$photo->save();
						continue;
					}
					$items = $feed->getItems();
					if (count($items) < 1) {
						$photo->status = 'no_items';
						$photo->save();
						continue;
					}

					$last_media = $items[ 0 ];
					$caption = $last_media->getCaption();
					if ($caption !== NULL) {
						$caption = $last_media->getCaption()->getText();

						if (in_array($caption, $telista, TRUE)) {
							$has_caption = TRUE;
						} else {
							$has_caption = FALSE;

						}

					} else {
						$has_caption = FALSE;

					}

					$image = $last_media->getImageVersions2()->getCandidates()[ 0 ]->getUrl();


					$original = new imagick(public_path('test1.jpg'));
					$image = new imagick($image);

					$result = $original->compareImages($image, Imagick::METRIC_MEANSQUAREERROR);

					if (round($result[ 1 ], 2) * 100 <= 8) {
						$has_image = TRUE;
					} else {
						$has_image = FALSE;
					}

					if ($has_image === FALSE) {
						$photo->status = 'no_image';
						$photo->save();
						continue;
					}

					$acc->orders()->create([
						                       'instagram_id'   => $acc->instagram_id,
						                       'instagram_user' => $acc->username,
						                       'has_image'      => $has_image,
						                       'has_caption'    => $has_caption,
						                       'amount'         => 100,
						                       'current_value'  => $ig->people->getInfoById($acc->instagram_id)->getUser()->getFollowerCount(),
					                       ]);
					$acc->has_photo = FALSE;
					$acc->save();
					$photo->status = 'ordered_init';
					$photo->save();
				}


			}


		}
	}
