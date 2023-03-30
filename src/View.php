<?php

declare(strict_types = 1);

namespace DP;

class View
{
	/**
	 * @var bool
	 */
	public static $is_constructed = false;

	/**
	 * @var string
	 */
	public static $layout = 'layout';

	/**
	 * @var string
	 */
	public static $view_path = null;

	public static function construct(): void
	{
		if (self::$is_constructed) {
			return;
		}
		$app_path = '';
		if (defined('APP_PATH')) {
			$app_path = APP_PATH;
		}
		self::$view_path = $app_path . '/protected/views';
		self::$is_constructed = true;
	}

	/**
	 * @param string $view
	 * @param array <string, mixed> $data
	 * @param bool $return
	 * @return string|false|void
	 */
	public static function render_partial(string $view, array $data = [], bool $return = false)
	{
		self::construct();
		extract($data, EXTR_SKIP);
		ob_start();
		require self::$view_path . "/{$view}.php";
		$content = ob_get_clean();
		if ($return) {
			return $content;
		} else {
			echo $content;
		}
	}

	/**
	 * @param string $view
	 * @param array <string, mixed> $data
	 * @param bool $return
	 * @return string|false|void
	 */
	public static function render(string $view, array $data = [], bool $return = false)
	{
		$content = self::render_partial($view, $data, true);
		$data['content'] = $content;
		return self::render_partial(self::$layout, $data, $return);
	}
}
