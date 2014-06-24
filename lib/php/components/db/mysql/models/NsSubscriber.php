<?php
namespace YiiNodeSocket\Components\Db\Mysql\Models;

/**
 * This is the model class for table "ns_subscriber".
 *
 * The followings are the available columns in table 'ns_subscriber':
 * @property integer               $id
 * @property string                $role
 * @property integer               $user_id
 * @property string                $sid
 * @property string                $sid_expiration
 * @property string                $create_date
 *
 * Behaviors
 *
 * The followings are the available model relations:
 * @property NsChannelSubscriber[] $channelSubscribers
 */
class NsSubscriber extends \CActiveRecord {


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return NsSubscriber the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'ns_subscriber';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'numerical', 'integerOnly' => true),
			array('role, sid', 'length', 'max' => 255),
			array('sid_expiration', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, role, user_id, sid, sid_expiration, create_date', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'channelSubscribers' => array(self::HAS_MANY, 'NsChannelSubscriber', 'subscriber_id'),
		);
	}
}