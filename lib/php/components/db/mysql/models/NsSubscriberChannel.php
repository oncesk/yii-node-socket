<?php
namespace YiiNodeSocket\Components\Db\Mysql\Models;

/**
 * This is the model class for table "ns_channel_subscriber".
 *
 * The followings are the available columns in table 'ns_channel_subscriber':
 * @property integer      $id
 * @property integer      $subscriber_id
 * @property integer      $channel_id
 * @property integer      $can_send_event_from_php
 * @property integer      $can_send_event_from_js
 * @property string       $create_date
 *
 * Behaviors
 *
 * The followings are the available model relations:
 * @property NsChannel    $channel
 * @property NsSubscriber $subscriber
 */
class NsSubscriberChannel extends \CActiveRecord {


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return NsSubscriberChannel the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'ns_channel_subscriber';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js', 'required'),
			array('subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js', 'numerical', 'integerOnly' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js, create_date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'channel' => array(self::BELONGS_TO, 'NsChannel', 'channel_id'),
			'subscriber' => array(self::BELONGS_TO, 'NsSubscriber', 'subscriber_id'),
		);
	}
}