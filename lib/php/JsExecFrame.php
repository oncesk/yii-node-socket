<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 4:46 PM
 * To change this template use File | Settings | File Templates.
 */

class JsExecFrame extends AFrame {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_MULTIPLE_JAVASCRIPT_EXEC;
	}

	/**
	 * @param string|CJavaScriptExpression $javascriptCode
	 *
	 * @return JsExecFrame
	 */
	public function executeOnServerSide($javascriptCode) {
		return $this->addTo('server', $javascriptCode);
	}

	/**
	 * @param string|CJavaScriptExpression $javascriptCode
	 *
	 * @return JsExecFrame
	 */
	public function executeOnClientSide($javascriptCode) {
		return $this->addTo('client', $javascriptCode);
	}

	/**
	 * @param $location
	 * @param $javascriptCode
	 *
	 * @return JsExecFrame
	 */
	protected function addTo($location, $javascriptCode) {
		$js = null;
		if ($javascriptCode instanceof CJavaScriptExpression) {
			$js = $javascriptCode->code;
		} else if (is_string($javascriptCode)) {
			$js= $javascriptCode;
		}
		if (isset($js)) {
			$this->_container['exec'][$location][] = $javascriptCode;
		}
		return $this;
	}

	protected function createContainer() {
		parent::createContainer();
		$this->_container['exec'] = array(
			'server' => array(),
			'client' => array()
		);
	}
}