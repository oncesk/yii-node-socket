<?php
namespace YiiNodeSocket\Behavior;

/**
 * Needed for sending event into concrete channel defined via channelModel attribute
 *
 * Class ArChannelEvent
 * @package YiiNodeSocket\Behavior
 */
class ArChannelEvent extends ArBehavior {

	/**
	 * Channel model = channel owner
	 *
	 * @var string
	 */
	public $channelModel;

	/**
	 * Field related to the channel model
	 *
	 * Example:
	 *
	 * if $channelModel is a Post and current behavior(ArChannelEvent) attached
	 * to PostComment
	 *
	 * $relatedAttribute = 'post_id'
	 *
	 * @var string
	 */
	public $relatedAttribute;

	/**
	 * @var \CActiveRecord
	 */
	private $_channelModel;

	/**
	 * @var bool
	 */
	private $_canSendEvent = false;

	public function attach($owner) {
		parent::attach($owner);
		if (
			$this->channelModel &&
			$this->relatedAttribute &&
			$id = $this->getOwner()->getAttribute($this->relatedAttribute)
		) {
			$this->_loadChannelModel($id);
		} else {
			$this->_channelModel = $this->getOwner();
		}

		if ($this->_channelModel && !$this->_channelModel->getIsNewRecord()) {
			$this->_canSendEvent = true;
		}
	}

	/**
	 * @param string $event
	 * @param array  $data
	 */
	public function emit($event, array $data = array()) {
		if ($this->_canSendEvent) {
			//  create and send frame
		}
	}

	/**
	 * @param integer $id
	 */
	protected function _loadChannelModel($id) {
		$this->_channelModel = \CActiveRecord::model($this->channelModel)->findByPk($id);
	}
}