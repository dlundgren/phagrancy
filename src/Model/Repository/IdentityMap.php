<?php

namespace Phagrancy\Model\Repository;

class IdentityMap
{
	private static $alias = [];
	private static $map = [];

	/**
	 * Prevents the identitymap from being instantiated
	 *
	 * @codeCoverageIgnore
	 */
	private function __construct()
	{
	}

	public static function get($type, $id)
	{
		if (is_object($type)) {
			$type = get_class($type);
		}

		$key = "{$type}.{$id}";
		return isset(self::$map[$key])
			? self::$map[$key]
			: null;
	}

	public static function set($entity, $id)
	{
		$key = get_class($entity) . ".{$id}";
		if (isset(self::$map[$key])) {
			throw new \RuntimeException("Entity already exists: {$key}");
		}

		self::$map[$key] = $entity;
	}

	public static function exists($type, $id)
	{
		if (is_object($type)) {
			$type = get_class($type);
		}

		return isset(self::$map["{$type}.{$id}"]);
	}

}