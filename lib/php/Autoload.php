<?php
namespace YiiNodeSocket;

class Autoload {

	public static function register($nodeSocketDirectory) {
		$loader = new Autoload($nodeSocketDirectory);
		$res = spl_autoload_register(array($loader, 'autoload'));
                $res;
	}

	private $_nodeSocketDirectory;

	/**
	 * @param $dir
	 */
	public function __construct($dir) {
		$this->_nodeSocketDirectory = $dir . DIRECTORY_SEPARATOR;
	}

	public function autoload($className, $classMapOnly=false) {
		if (strpos($className, 'YiiNodeSocket\\') === 0) {
			$class = $this->_nodeSocketDirectory;
			$chunks = explode('\\', $className);
			array_shift($chunks);
			$cl = array_pop($chunks);
			$class .= strtolower(join('/', $chunks)) . '/' . $cl . '.php';
			if (file_exists($class)) {
				include $class;
				return true;
			}
			return false;
		}
	}
}