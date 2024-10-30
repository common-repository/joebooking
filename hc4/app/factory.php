<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_App_Factory
{
	protected $bind = array();
	protected $appModules = array();

	public function __construct( 
		array $bind = array(),
		array $appModules = array()
	)
	{
		$bind[ __CLASS__ ] = $this;

		reset( $bind );
		foreach( $bind as $k => $v ){
			$k = strtolower( $k );
			if( ! is_object($v) ){
				$v = strtolower( $v );
			}
			$this->bind[ $k ] = $v;
		}

		$this->appModules = $appModules;
	}

/**
* Makes a functor object of a class. All of them are singletons. 
*
* @param string			$wantClassName		A class name to make object.
*
* @return object
*/
	public function make()
	{
		static $_reflections = array();

		$args = $originalArgs = func_get_args();
		$className = array_shift( $args );

		if( __CLASS__ == $className ){
			return $this;
		}

		$className = strtolower( trim($className) );

		if( ! isset($this->bind[$className]) ){
			$this->bind[$className] = $className;
		}

		if( is_object($this->bind[$className]) ){
			return $this->bind[$className];
		}

		$realClassName = $this->bind[$className];
		if( ! strlen($realClassName) ){
			return;
		}

		$args = $this->makeArgs( $realClassName, '__construct', $args );

		if( $args ){
			$class = new ReflectionClass( $realClassName );
// echo "JO '$realClassName'<br>";
			$return = $class->newInstanceArgs( $args );

			// $constructor = $class->getConstructor();
			// $constructor->setAccessible( TRUE );
			// $return = $class->newInstanceWithoutConstructor();
			// $constructor->invokeArgs( $return, $args );

		/*
			automatically assign internal properties like
			$this->varName1 = $hooks->wrap( $varName1 );
		*/
			foreach( $args as $argName => $arg ){
				if( property_exists($return, $argName) ){
					continue;
				}
				$return->{$argName} = $arg;
			}
		}
		else {
			$return = new $realClassName;
		}

		$this->bind[$className] = $return;
		return $return;
	}

	public function makeArgs( $className, $methodName, array $args = array() )
	{
		static $_reflections = array();

		$className = strtolower( $className );
		if( ! isset($_reflections[$className]) ){
			$_reflections[$className] = new ReflectionClass( $className );
		}
		$classReflection = $_reflections[$className];

		try {
			$methodReflection = $classReflection->getMethod( $methodName );
		}
		catch( ReflectionException $e ){
			return $args;
		}

		$return = array();

		$needArgs = $methodReflection->getParameters();
		$numberOfArgs = count( $needArgs );
		$suppliedNumberOfArgs = count( $args );

		for( $ii = 0; $ii < $numberOfArgs; $ii++ ){
			$needArg = $needArgs[$ii];
			$needArgName = $needArg->getName();

			if( $ii < $suppliedNumberOfArgs ){
				$return[ $needArgName ] = $args[ $ii ];
				continue;
			}

	// NEED TO INJECT MISSING ARGS
			$isOptional = $needArg->isOptional();

			$argCreated = FALSE;

			try {
				$needArgClass = $needArg->getClass();

				if( $needArgClass ){
					$needArgClassName = $needArgClass->getName();
					$needArgClassName = strtolower( $needArgClassName );

				/* NOW CHECK IF THE PARENT CLASS IS ALLOWED TO MAKE ITS ARGUMENT */
				// FIND THE MODULE OF PARENT
					$parentModuleIndex = -1;
					$childModuleIndex = -1;
					$jj = -1;
					reset( $this->appModules );
					foreach( $this->appModules as $moduleName ){
						$jj++;

						if( substr($className, 0, strlen($moduleName) ) == $moduleName ){
							$parentModuleIndex = $jj;
						}

						if( substr($needArgClassName, 0, strlen($moduleName) ) == $moduleName ){
							$childModuleIndex = $jj;
						}

						// if( ($childModuleIndex > -1) && ($parentModuleIndex > -1) ){
							// break;
						// }
					}

					if( $childModuleIndex > $parentModuleIndex ){
						echo "FACTORY: '$className' IS NOT ALLOWED TO MAKE '$needArgClassName'<br>";
// echo "$childModuleIndex VS $parentModuleIndex<br>";
// _print_r( $this->appModules );
						exit;
					}

					$arg = $this->make( $needArgClassName );
					$argCreated = TRUE;
				}
				elseif( $isOptional ){
					$arg = $needArg->getDefaultValue();
					$argCreated = TRUE;
				}
			}
			catch( ReflectionException $e ){
				echo __CLASS__ . ": class is unknown for '$needArgName' $ii argument of '$className::$methodName'!<br>";
				exit;
			}

			if( ! $argCreated ){
				echo  __CLASS__ . ": can't build '$needArgName' $ii argument of '$className::$methodName'!<br>";
				exit;
			}

			$return[ $needArgName ] = $arg;
		}

		return $return;
	}
}