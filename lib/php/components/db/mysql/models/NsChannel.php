<?php
namespace YiiNodeSocket\Components\Db\Mysql\Models;

/**
 * This is the model class for table "ns_channel".
 *
 * The followings are the available columns in table 'ns_channel':
 * @property integer               $id
 * @property string                $name
 * @property string                $properties
 * @property integer               $is_authentication_required
 * @property string                $allowed_roles
 * @property integer               $subscriber_source
 * @property integer               $event_source
 * @property string                $create_date
 *
 * Behaviors
 *
 * The followings are the available model relations:
 * @property NsChannelSubscriber[] $nsChannelSubscribers
 */
class NsChannel extends \CActiveRecord {


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return NsChannel the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'ns_channel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, is_authentication_required, subscriber_source, event_source', 'required'),
			array('is_authentication_required, subscriber_source, event_source', 'numerical', 'integerOnly' => true),
			array('properties', 'length', 'max' => 65000, 'allowEmpty' => true),
			array('name, allowed_roles', 'length', 'max' => 255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, properties, is_authentication_required, allowed_roles, subscriber_source, event_source, create_date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'nsChannelSubscribers' => array(self::HAS_MANY, 'NsChannelSubscriber', 'channel_id'),
		);
	}
}