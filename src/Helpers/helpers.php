<?php



if(!function_exists('redirect_now')){


	/**
	 * Redirect the user no matter what. No need to use a return
	 * statement. Also avoids the trap put in place by the Blade Compiler.
	 *
	 * @param string $url
	 * @param int $code http code for the redirect (should be 302 or 301)
	 */
	function redirect_now($url, $code = 302)
	{
		try {
			\App::abort($code, '', ['Location' => $url]);
		} catch (\Exception $exception) {
			// the blade compiler catches exceptions and rethrows them
			// as ErrorExceptions :(
			//
			// also the __toString() magic method cannot throw exceptions
			// in that case also we need to manually call the exception
			// handler
			$previousErrorHandler = set_exception_handler(function () {
			});
			restore_error_handler();
			call_user_func($previousErrorHandler, $exception);
			die;
		}
	}
}

if(!function_exists('page_route')){

	/**
	 * Generate route url by page code or page model.
	 *
	 * @param $page
	 * @param array $params
	 *
	 * @return mixed
	 */
	function page_route($page, $params = []){

		return Pages::routeByPage($page, $params);
	}
}