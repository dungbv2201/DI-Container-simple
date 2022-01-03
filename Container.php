<?php

class Container
{

	public $bindings = [];

	public function bind($abstract, $concrete = NULL)
	{
		if (is_null($concrete)) {
			$concrete = $abstract;
		}
		$this->bindings[$abstract] = $concrete;
	}

	public function make($abstract, $parameters = [])
	{
		if (!isset($this->bindings[$abstract])) {
			$this->bind($abstract);
		}

		return $this->resolve($this->bindings[$abstract], $parameters);
	}

	protected function resolve($concrete, $parameters)
	{
		if ($concrete instanceof Closure) {
			return $concrete($this, $parameters);
		}

		$reflector = new ReflectionClass($concrete);

		if (!$reflector->isInstantiable()) {
			throw new Exception("Class {$concrete} is not instantiable");
		}

		$constructor = $reflector->getConstructor();

		if (is_null($constructor)) {
			return $reflector->newInstance();
		}

		$parameters = $constructor->getParameters();
		$dependencies = $this->resolveDependencies($parameters);

		return $reflector->newInstanceArgs($dependencies);
	}

	protected function resolveDependencies($parameters)
	{
		$dependencies = [];

		foreach ($parameters as $parameter) {
			$dependency = $parameter->getClass();

			if (is_null($dependency)) {
				if ($parameter->isDefaultValueAvailable()) {
					$dependencies[] = $parameter->getDefaultValue();
				} else {
					throw new Exception("Can not resolve dependency {$parameter->name}");
				}
			} else {
				$dependencies[] = $this->make($dependency->name);
			}
		}

		return $dependencies;
	}
}
