<?php

namespace Phagrancy\Model\Service;

use Phagrancy\Model\Repository;

class Box
{
	private $scopes;
	private $boxes;

	public function __construct(Repository\Scope $scopes, Repository\Box $boxes)
	{
		$this->scopes = $scopes;
		$this->boxes  = $boxes;
	}

	public function findFullBoxInScope($box, $scope)
	{
		if (($scope = $this->scopes->ofName($scope)) === null) {
			return null;
		}
		return $this->boxes->ofNameInScopeWithVersions($box, $scope);
	}
	public function findBoxInScope($box, $scope)
	{
		if (($scope = $this->scopes->ofName($scope)) === null) {
			return null;
		}

		return $this->boxes->ofNameInScope($box, $scope);
	}

	public function findVersionedProviderInBoxScope($version, $provider, $box, $scope)
	{
		if (!($box = $this->findBoxInScope($box, $scope)) === null) {
			return null;
		}

		return $this->provders->ofVersionInBox($provider, $version, $box);
	}
}