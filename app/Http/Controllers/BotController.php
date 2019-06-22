<?php

	namespace App\Http\Controllers;

	use App\Account;
	use App\User;
	use Illuminate\Http\Request;
	use Telegram\Bot\Laravel\Facades\Telegram;

	class BotController extends Controller {
		public function message() {
			//			$last_update = collect(Telegram::getUpdates())->reverse()->first();
			$last_update = Telegram::getWebhookUpdates();
			logger($last_update);
			$text = $last_update->getMessage()->getText();
			$sender = $last_update->getMessage()->getFrom();


			$user = User::updateOrCreate(['telegram_id' => $sender->getId()], [
				'first_name' => $sender->getFirstName(),
				'last_name'  => $sender->getLastName(),
				'username'   => $sender->getUsername(),
				'language'   => $sender->getLanguageCode(),
			]);
			if ($text[ 0 ] === '/') {
				$text = str_replace('/', '', $text);
			}
			$commands = [
				'start', 'follower',
			];
			if ($user->state !== NULL && $user->state !== 'start' && ! in_array($text, $commands, TRUE)) {
				$state = $user->state;

				return $this->$state($text, $sender, $user);

			}


			try {
				return $this->$text($text, $sender, $user);
			} catch (\Exception $e) {
				return $this->error($text, $sender, $user);
			}

		}

		public function error($text, $sender, $user) {
			$message = 'برای دریافت ۱۰۰ عدد فالوور از دستور /follower استفاده کنید.';
			$response = Telegram::sendMessage([
				                                  'chat_id'      => $sender->getId(),
				                                  'text'         => $message,
				                                  'reply_markup' => Telegram::replyKeyboardHide(),

			                                  ]);

			$user->state = 'start';
			$user->save();

			return $response;
		}

		public function start($text, $sender, $user) {
			$message = 'برای دریافت ۱۰۰ عدد فالوور از دستور /follower استفاده کنید.';
			$response = Telegram::sendMessage([
				                                  'chat_id'      => $sender->getId(),
				                                  'text'         => $message,
				                                  'reply_markup' => Telegram::replyKeyboardHide(),

			                                  ]);

			$user->state = 'start';
			$user->save();

			return $response;
		}

		public function follower($text, $sender, $user) {
			$message = 'خب
			حالا اسم اکانتتو بفرست
			';
			$response = Telegram::sendMessage([
				                                  'chat_id'      => $sender->getId(),
				                                  'text'         => $message,
				                                  'reply_markup' => Telegram::replyKeyboardHide(),

			                                  ]);
			$user->state = 'account';
			$user->save();

			return $response;
		}

		public function account($text, $sender, $user) {
			Telegram::sendMessage([
				                      'chat_id' => $sender->getId(),
				                      'text'    => 'چند لحظه صبر کن...',
			                      ]);
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "http://cafeigapp.com/api/instagram/account/name/$text");

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$output = json_decode(curl_exec($ch), FALSE);

			curl_close($ch);
			if ($output === NULL) {
				$response = Telegram::sendMessage([
					                                  'chat_id'      => $sender->getId(),
					                                  'text'         => 'متاسفانه این اکانت پیدا نشد، دوباره امتحان کن.',
					                                  'reply_markup' => Telegram::replyKeyboardHide(),

				                                  ]);
				$user->state = 'account';
				$user->save();

				return $response;
			}

			if ($output->is_private) {
				Telegram::sendMessage([
					                      'chat_id'      => $sender->getId(),
					                      'text'         => 'اکانت شما حالت شخصی (پرایوت) است، برای استفاده از این بات باید اکانتتون غیر شخصی (پابلیک) باشه.
					                                  اکانتت رو از پرایوت در بیار و دوباره امتحان کن.',
					                      'reply_markup' => Telegram::replyKeyboardHide(),

				                      ]);
				$user->state = 'start';
				$user->save();

				return $this->start($text, $sender, $user);
			}

			$fullname = $output->full_name;
			$follower_count = $output->follower_count;
			$is_private = $output->is_private ? 'پرایوت' : 'غیر پرایوت';


			$caption = "اکانتت همینه؟ تایید میکنی؟
			نام:$fullname
			تعداد فالوور:$follower_count
			";

			$keyboard = [
				['خیر', 'بله'],

			];

			$reply_markup = Telegram::replyKeyboardMarkup([
				                                              'keyboard'          => $keyboard,
				                                              'resize_keyboard'   => TRUE,
				                                              'one_time_keyboard' => TRUE,
			                                              ]);

			$response = Telegram::sendPhoto([
				                                'chat_id'      => $sender->getId(),
				                                'photo'        => $output->profile_pic_url,
				                                'caption'      => $caption,
				                                'reply_markup' => $reply_markup,
			                                ]);
			$user->memory = $output;

			$user->state = 'account_approval';
			$user->save();

			return $response;
		}

		public function account_approval($text, $sender, $user) {

			if ($text === 'بله') {

				$account = $user->accounts()->updateOrCreate(['instagram_id' => $user->memory[ 'id' ]], [
					'username' => $user->memory[ 'username' ],
				]);


				if ($account->has_photo === TRUE) {
					Telegram::sendMessage([
						                      'chat_id' => $sender->getId(),
						                      'text'    => 'شما قبلا یک عکس دریافت کرده اید لطفا بعد از ۲۴ ساعت دوباره اقدام کنید.',
					                      ]);

					return $this->start($text, $sender, $user);
				}

				Telegram::sendMessage([
					                      'chat_id' => $sender->getId(),
					                      'text'    => 'چند لحظه صبر کن...',
				                      ]);


				$response = Telegram::sendPhoto([
					                                'chat_id'      => $sender->getId(),
					                                'photo'        => 'https://www.bhphotovideo.com/images/images2000x2000/test1.jpg',
					                                'caption'      => 'خب حالا این عکس رو پست کن و بزار یکروز بمونه  بعدش ۱۰۰ تا فالوورتو میگیری، بعد از یکروز میتونی پاکش کنی.
					                                اینو بدون من هر ساعت اکانتتو چک میکنم پس در طول روز پاکش نکن.
					                                آخرین پستت باید این عکس باشه.
					                                کپشن عکس هم فارسی بنویس: تلیستا
					                                ۲۴ ساعت از همین الان شروع شد.',
					                                'reply_markup' => Telegram::replyKeyboardHide(),
				                                ]);


				$account->has_photo = TRUE;

				$account->photos()->create([
					                           'set_at' => now(),
					                           'status' => 'init',
				                           ]);
				$user->state = 'start';
				$user->memory = NULL;
				$user->save();
				$account->save();

				return $response;
			}
			if ($text === 'خیر') {
				return $this->start($text, $sender, $user);
			}


		}

	}
