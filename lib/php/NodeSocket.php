<?php
require_once 'frames/IFrameFactory.php';
require_once 'frames/FrameFactory.php';

class NodeSocket extends CApplicationComponent {

	/**
	 * Node js server host to bind http and socket server
	 * Valid values is:
	 *   - valid ip address
	 *   - domain name
	 *
	 * Domain name must be withoud http or https
	 * Example:
	 *
	 * 'host' => 'test.com'
	 * // or
	 * 'host' => '84.25.159.52'
	 *
	 * @var string
	 */
	public $host = '127.0.0.1';

	/**
	 * @var int by default is once month
	 */
	public $cookieLifeTime = 2592000;

	/**
	 * Port in integer type only
	 *
	 * @var int
	 */
	public $port = 3001;

	/**
	 * Can be string, every domain|ip separated by a comma
	 * or array
	 *
	 * @var string|array
	 */
	public $origin;

	/**
	 * List of allowed servers
	 *
	 * Who can send server frames
	 *
	 * If is string, ip addresses should be separated by a comma
	 *
	 * @var string|array
	 */
	public $allowedServerAddresses;

	/**
	 * Default is runtime/socket-transport.server.log
	 *
	 * @var string
	 */
	public $socketLogFile;

	/**
	 * If set to false, any client can connect to websocket server
	 *
	 * @var bool
	 */
	public $checkClientOrigin = true;

	/**
	 * @var string
	 */
	public $pidFile = 'socket-transport.pid';

	/**
	 * @var int timeout for handshaking in miliseconds
	 */
	public $handshakeTimeout = 2000;

	/**
	 * @var array
	 */
	public $dbConfiguration = array('driver' => 'dummy');

	/**
	 * @var string
	 */
	protected $_assetUrl;

	/**
	 * @var \YiiNodeSocket\Frames\FrameFactory
	 */
	protected $_frameFactory;

	/**
	 * @var \YiiNodeSocket\Components\Db
	 */
	protected $_db;

	public function init() {
		parent::init();
		require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
			'..',
			'vendor',
			'elephant.io',
			'lib',
			'ElephantIO',
			'Client.php'
		));

		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once 'components/Autoload.php';
		\YiiNodeSocket\Component\Autoload::register(__DIR__);
		spl_autoload_register(array('YiiBase','autoload'));

		$this->_frameFactory = new \YiiNodeSocket\Frames\FrameFactory($this);
		$this->_db = new YiiNodeSocket\Components\Db($this);
		foreach ($this->dbConfiguration as $k => $v) {
			$this->_db->$k = $v;
		}
	}

	/**
	 * @return \YiiNodeSocket\Frames\FrameFactory
	 */
	public function getFrameFactory() {
		return $this->_frameFactory;
	}

	/**
	 * @return \YiiNodeSocket\Components\Db
	 */
	public function getDb() {
		return $this->_db;
	}

	/**
	 * @return bool
	 */
	public function registerClientScripts() {
		if ($this->_assetUrl) {
			return true;
		}
		$this->_assetUrl = Yii::app()->assetManager->publish(__DIR__ . '/../js/client');
		if ($this->_assetUrl) {
			Yii::app()->clientScript->registerScriptFile(sprintf("http://%s:%d%s", $this->host, $this->port, '/socket.io/socket.io.js'));
			Yii::app()->clientScript->registerScriptFile($this->_assetUrl . '/client.js');
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getOrigin() {
		$origin = $this->host . ':*';

		if ($this->origin) {
			$o = array();
			if (is_string($this->origin)) {
				$o = explode(',', $this->origin);
			}
			$o = array_map('trim', $o);
			if (in_array($origin, $o)) {
				unset($o[array_search($origin, $o)]);
			}
			if (!empty($o)) {
				$origin .= ' ' . implode(' ', $o);
			}
		}
		return $origin;
	}

	/**
	 * @return array
	 */
	public function getAllowedServersAddresses() {
		$allow = array();
		$serverIp = gethostbyname($this->host);
		$allow[] = $serverIp;
		if ($this->allowedServerAddresses && !empty($this->allowedServerAddresses)) {
			if (is_string($this->allowedServerAddresses)) {
				$allow = array_merge($allow, explode(',', $this->allowedServerAddresses));
			} else if (is_array($this->allowedServerAddresses)) {
				$allow = array_merge($allow, $this->allowedServerAddresses);
			}
		}
		return array_unique($allow);
	}
}