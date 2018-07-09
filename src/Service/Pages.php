<?php

namespace Vshapovalov\Pages;

use Illuminate\Support\Facades\Cache;

class Pages {
	// v1.0.4
	protected $filledRoutes;

	function __construct() {
		$this->filledRoutes = null;
	}

	public function routes()
	{
		require __DIR__.'/../../routes/Routes.php';
	}

	/**
	 * Get routes info from all pages or page by code or page object
	 *
	 * @param null $page
	 *
	 * @return array
	 */
	public function getRoutes($page = null){

		if (!$this->filledRoutes) {
			$this->filledRoutes = Cache::remember( 'pages', 1440, function () {

				return Page::all()->mapWithKeys( function ( $page ) {

					debug( $page );

					$urlPattern = strlen( $page->url ) > 1 ? trim( $page->url, '/' ) : $page->url;

					return [
						$page->code => [
							'route' => $urlPattern,
							'page'  => $page,
							'info'  => $this->getRegExpUrlInfo( $urlPattern )
						]
					];

				} );
			} );
		}

		if ($page) return $this->filledRoutes[ is_string($page) ? $page : $page->code ];

		return $this->filledRoutes;
	}


	/**
	 * Match url by patterns and return route info
	 *
	 * @param $url
	 *
	 * @return mixed|null
	 */
	public function getRouteByUrl($url){

		$urlSegments = explode('/', $url);

		foreach ($this->getRoutes() as $route){

			if ($result = preg_match($route['info']['pattern'], $url, $match)){

				if (count($route['info']['params'])) {
					foreach ( $route['info']['params'] as &$param ) {
						$param['value'] = isset($urlSegments[$param['index']])
							? $urlSegments[$param['index']]
							: null;
					}
				}

				return $route;
			}
		}

		return null;
	}

	/**
	 * Get url info(regexp pattern, params, segments)
	 *
	 * @param $url
	 *
	 * @return array
	 */
	public function getRegExpUrlInfo($url){

		$segments = explode('/', $url);
		$regexpUrl = [];
		$urlParams = [];
		$urlSegments = [];
		$defaultPattern = '[a-zA-Z0-9-_]+';

		foreach ($segments as $key => $segment){

			if (starts_with($segment, ':')) {
				$pattern = $defaultPattern;

				if (strpos($segment, '|')) {
					$segmentData = explode('|',$segment);

					$segment = $segmentData[0];
					unset($segmentData[0]);
					$pattern = implode('|', $segmentData[1]);
				}

				if (ends_with($segment, '?')){
					$pattern = '(\/'.$pattern.')?';
				}

				$segment = trim($segment, ':?');

				$regexpUrl[] = $pattern;
				$urlParams[$segment] = [
					'index' => $key,
					'name' => $segment
				];

				$urlSegments[] = [ 'type' => 'param', 'value' => $segment ];

			} else {
				$regexpUrl[] = $segment;
				$urlSegments[] = [ 'type' => 'static', 'value' => $segment ];
			}
		}

		$regexpUrl = '/^'.implode('\/', $regexpUrl).'$/iu';

		$regexpUrl = str_replace('\/(','(', $regexpUrl);

		return [
			'pattern' => $regexpUrl,
			'params' => $urlParams,
			'segments' => $urlSegments
		];
	}

	/**
	 * Get relative url by page code and params
	 *
	 * @param string|object $code
	 * @param array $params
	 *
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */
	public function routeByPage($page, $params = []){

		if ($filledRoutes = $this->getRoutes($page)){

			$result = [];

			$segments = $filledRoutes['info']['segments'];
			$paramsKeys = array_keys($params);

			foreach ( $segments as $segment ) {
				if ($segment['type'] == 'static') {
					$result[] = $segment['value'];
				} else {
					if ($segment['type'] == 'param' && in_array($segment['value'], $paramsKeys)) {
						$result[] = $params[$segment['value']];
					}
				}
			}

			return url(implode('/', $result));
		} else {
			return '';
		}
	}
}