<?php
namespace YiiNodeSocket\Model\Driver;

use YiiNodeSocket\Model\AModel;
use YiiNodeSocket\Model\Channel;
use YiiNodeSocket\Model\Subscriber;
use YiiNodeSocket\Model\SubscriberChannel;

class FilesDriver extends ADriver {

	public $storageDir = '';

	protected function afterConstruct() {
		if (!is_dir($this->storageDir)){
			if (!@mkdir($this->storageDir)) {
				throw new \CException('Can not create storage directory in "' . $this->storageDir . '"');
			}
		}
	}

	/**
	 * @param AModel $model
	 *
	 * @return bool
	 */
	public function save(AModel $model) {
		return $this->{'_save' . get_class($model)}($model);
	}


	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function update(AModel $model) {
		return $this->save($model);
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function delete(AModel $model) {
		// TODO: Implement delete() method.
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function refresh(AModel $model) {
		// TODO: Implement refresh() method.
	}

	/**
	 * @param string $id
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function findByPk($id, AModel $model) {
		// TODO: Implement findByPk() method.
	}

	/**
	 * @param string         $attribute
	 * @param string|integer $attributeValue
	 * @param string         $modelClass
	 *
	 * @return array
	 */
	public function findByAttribute($attribute, $attributeValue, $modelClass) {
		// TODO: Implement findByAttribute() method.
	}

	/**
	 * @param Channel $channel
	 *
	 * @return bool
	 */
	private function _saveChannel(Channel $channel) {
		$file = $this->_resolveModelDirectory($channel) . $this->_resolveFileName($channel);
		return (bool) $this->_writeData($file, $channel->toArray());
	}

	/**
	 * @param Subscriber $subscriber
	 */
	private function _saveSubscriber(Subscriber $subscriber) {
		$file = $this->_resolveModelDirectory($subscriber) . $this->_resolveFileName($subscriber);
		$links = array();
		/** @var SubscriberChannel $subscriberChannel */
		foreach ($subscriber->getSubscriberChannel() as $subscriberChannel) {
			$links[] = $subscriberChannel->toArray();
		}
		$container = array(
			'subscriber' => $subscriber->toArray(),
			'links' => $links
		);
		return (bool) $this->_writeData($file, $container);

	}

	private function _saveSubscriberChannel(SubscriberChannel $subscriberChannel) {
		$file = $this->_resolveModelDirectory($subscriberChannel) . $this->_resolveFileName($subscriberChannel);
		return (bool) $this->_writeData($file, $subscriberChannel->toArray());
	}

	/**
	 * @param string $file
	 * @param array $data
	 *
	 * @return int
	 */
	private function _writeData($file, array $data) {
		return $this->_putToFile($file, json_encode($data));
	}

	/**
	 * @param $file
	 * @param $data
	 *
	 * @return int
	 */
	private function _putToFile($file, $data) {
		return file_put_contents($file, $data);
	}

	/**
	 * @param AModel $model
	 *
	 * @return string
	 */
	private function _resolveFileName(AModel $model) {
		return
	}

	/**
	 * @param AModel $model
	 *
	 * @return string
	 * @throws \CException
	 */
	private function _resolveModelDirectory(AModel $model) {
		$directory =  $this->storageDir . strtolower(get_class($model)) . '/';
		if (!is_dir($directory) || !@mkdir($directory)) {
			throw new \CException('Can not create storage directory in "' . $directory . '"');
		}
		return $directory;
	}
}