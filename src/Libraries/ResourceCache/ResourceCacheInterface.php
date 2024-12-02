<?php

namespace Quantum\Libraries\ResourceCache;

interface ResourceCacheInterface
{
	public function put();

	public function get();

	public function delete();
}