<?php

declare(strict_types = 1);

namespace DP;

class View
{
	/**
	 * @var bool
	 */
	public static $is_constructed = \false;

	/**
	 * @var string
	 */
	public static $layout = 'layout';

	/**
	 * @var string
	 */
	public static $view_path = \null;

	/**
	 * @var array <string, array<string>>
	 */
	public static $css_list = [];

	/**
	 * @var array <string, array<string|int, bool|string>>
	 */
	public static $js_list = [];

	public static function construct(): void
	{
		if (self::$is_constructed) {
			return;
		}
		$app_path = '';
		if (\defined('\APP_PATH')) {
			/* @phpstan-ignore-next-line */
			$app_path = \APP_PATH;
		}
		self::$view_path = $app_path . '/protected/views';
		self::$is_constructed = \true;
	}

	/**
	 * @param string $view
	 * @param array <string, mixed> $data
	 * @param bool $return
	 * @return string|false|void
	 */
	public static function render_partial(string $view, array $data = [], bool $return = \false)
	{
		self::construct();
		(function ($__view__, $__data__) {
			$GLOBALS['__view__'] = $__view__;
			\extract($__data__, \EXTR_SKIP);
			unset($__view__);
			unset($__data__);
			\ob_start();
			require self::$view_path . "/{$GLOBALS['__view__']}.php";
			unset($GLOBALS['__view__']);
		})($view, $data);
		$content = \ob_get_clean();
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
	public static function render(string $view, array $data = [], bool $return = \false)
	{
		$content = self::render_partial($view, $data, \true);
		$data['content'] = $content;
		return self::render_partial(self::$layout, $data, $return);
	}

	/**
	 * @param string $href
	 * @param array <string, string> $attributes
	 */
	public static function enqueue_style(string $href, array $attributes = []): void
	{
		$attributes = \array_merge([
			'rel' => 'stylesheet',
		], $attributes);
		self::$css_list[$href] = $attributes;
	}

	/**
	 * @param string $src
	 * @param array <string|int, bool|string> $attributes
	 */
	public static function enqueue_script(string $src, array $attributes = []): void
	{
		self::$js_list[$src] = $attributes;
	}

	public static function load_style(): void
	{
		$html = '';
		foreach (self::$css_list as $href => $attributes) {
			$attr = '';
			foreach ($attributes as $key => $value) {
				$attr .= ' ' . \htmlspecialchars((string)$key, \ENT_QUOTES) . '="' . \htmlspecialchars((string)$value, \ENT_QUOTES) . '"';
			}
			$html .= '<link href="' . \htmlspecialchars($href, \ENT_QUOTES) . '"' . $attr . ' />' . "\r\n";
		}
		echo $html;
	}

	public static function load_script(): void
	{
		$html = '';
		foreach (self::$js_list as $src => $attributes) {
			$attr = '';
			foreach ($attributes as $key => $value) {
				if (\is_int($key)) {
					$attr .= ' ' . \htmlspecialchars((string)$value, \ENT_QUOTES);
				} else if (\is_bool($value)) {
					if ($value) {
						$attr .= ' ' . \htmlspecialchars((string)$key, \ENT_QUOTES);
					}
				} else {
					$attr .= ' ' . \htmlspecialchars((string)$key, \ENT_QUOTES) . '="' . \htmlspecialchars((string)$value, \ENT_QUOTES) . '"';
				}
			}
			$html .= '<script src="' . \htmlspecialchars($src, \ENT_QUOTES) . '"' . $attr . '></script>' . "\r\n";
		}
		echo $html;
	}
}
