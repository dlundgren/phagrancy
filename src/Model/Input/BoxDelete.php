<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\BoxDelete
 */

namespace Phagrancy\Model\Input;

use Validator\LIVR;

/**
 * Input for the BoxDelete
 *
 * Validates the scope, name, version, provider
 *
 * @package Phagrancy\Model\Input
 */
class BoxDelete
{
    use IsValidator, ValidatesScope, ValidatesBoxName, ValidatesVersion;

    public function __construct()
    {
        LIVR::registerDefaultRules(
            [
                'scope'    => [$this, 'validateScope'],
                'name'     => [$this, 'validateBoxName'],
                'version'  => [$this, 'validateVersion'],
                'provider' => [$this, 'validateProvider']
            ]);
    }

    public function validate($params)
    {
        return $this->perform(
            $params,
            [
                'scope'    => self::$SCOPE_RULE,
                'name'     => self::$BOX_NAME_RULE,
                'version'  => self::$VERSION_RULE,
                'provider' => ['required', 'trim', 'to_lc']
            ]);
    }
}
