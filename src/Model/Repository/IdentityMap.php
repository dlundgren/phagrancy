<?php

/**
 * @file
 * Contains Phagrancy\Model\Repository\IdentityMap
 */

namespace Phagrancy\Model\Repository;

/**
 * Caching mechanism for the other repositories in the same request
 *
 * @package Phagrancy\Model\Repository
 */
class IdentityMap
{
	/**
	 * @var array cache of items
	 */
	private static $map = [];

	/**
	 * Prevents the identitymap from being instantiated
	 *
	 * @codeCoverageIgnore
	 */
	private function __construct()
	{
	}

	/**
	 * Clears the map
	 *
	 * @seam
	 */
	public static function clear()
	{
		self::$map = [];
	}

	/**
	 * @param mixed  $type The type of object to retrieve
	 * @param string $id   The id of the object to retrieve
	 * @return mixed|null  The item if it exists or null
	 */
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

	/**
	 * @param object $entity The item to save
	 * @param string $id     The items id
	 */
	public static function set($entity, $id)
	{
		$key = get_class($entity) . ".{$id}";
		if (isset(self::$map[$key])) {
			throw new \RuntimeException("Entity already exists: {$key}");
		}

		self::$map[$key] = $entity;
	}

	/**
	 * @param object|string $type The item or name of object
	 * @param string        $id   The id of the item
	 * @return bool Whether or not it exists
	 */
	public static function exists($type, $id)
	{
		if (is_object($type)) {
			$type = get_class($type);
		}

		return isset(self::$map["{$type}.{$id}"]);
	}

}