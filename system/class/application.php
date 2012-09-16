<?php

namespace GR\System {

	TRY_DECLARE('\Application', __FILE__);

	class Application {

		static function setup() {
			\Model\Cache::setup();
			\Model\Config::setup();
			\Model\I18N::setup();
			\Model\Input::setup();
			\Model\Output::setup();
			\Model\View::setup();

			if (function_exists('\setup')) {
				return \setup();
			}
		}

		static function main($argc, $argv) {			
			if (function_exists('\main')) {
				return call_user_func('\main', $argc, $argv);
			}
		}

		static function shutdown() {
			if (function_exists('\shutdown')) {
				\shutdown();
			}
		}

	}

}

namespace {

	if (DECLARED('\Application', __FILE__)) {
		class Application extends \GR\System\Application {}
	}

}

