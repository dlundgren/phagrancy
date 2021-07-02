<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Delete
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for deleeting a box from the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Delete
{
    /**
     * @var Repository\Box
     */
    private $boxes;

    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var Input\BoxDelete
     */
    private $input;

    public function __construct(Repository\Box $boxes, Input\BoxDelete $input, $storagePath)
    {
        $this->boxes      = $boxes;
        $this->input      = $input;
        $this->storagePath = $storagePath;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /**
         * The route controls these params, and they are validated so safe
         *
         * @var string $name
         * @var string $scope
         * @var string $version
         * @var string $provider
         */
        $params = $this->input->validate($request->getAttribute('route')->getArguments());
        if (!$params) {
            return new Response\NotFound();
        }

        extract($params);
        $box = $this->boxes->ofNameInScope($name, $scope);

        if ($box) {
            $path = "{$this->storagePath}/{$box->path()}/{$version}/{$provider}.box";

            if (file_exists($path) && unlink($path)) {
                return new Response\Json([]);
            }
        }

        return new Response\NotFound();
    }
}
