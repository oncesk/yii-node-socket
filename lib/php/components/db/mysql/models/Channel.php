<?php

/**
 * This is the model class for table "ns_channel".
 *
 * The followings are the available columns in table 'ns_channel':
 * @property integer               $id
 * @property string                $name
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
class Channel extends CActiveRecord {


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Channel the static model class
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
			array('name, allowed_roles', 'length', 'max' => 255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, is_authentication_required, allowed_roles, subscriber_source, event_source, create_date', 'safe', 'on' => 'search'),
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

	public function beforeSave() {
		if ($this->getIsNewRecord()) {
		}
		return parent::beforeSave();
	}

	/**
	 * @return array behaviors rules.
	 */
	public function behaviors() {
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => Yii::t('admin', 'ID'),
			'name' => Yii::t('admin', 'Name'),
			'is_authentication_required' => Yii::t('admin', 'Is Authentication Required'),
			'allowed_roles' => Yii::t('admin', 'Allowed Roles'),
			'subscriber_source' => Yii::t('admin', 'Subscriber Source'),
			'event_source' => Yii::t('admin', 'Event Source'),
			'create_date' => Yii::t('admin', 'Create Date'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('is_authentication_required', $this->is_authentication_required);
		$criteria->compare('allowed_roles', $this->allowed_roles, true);
		$criteria->compare('subscriber_source', $this->subscriber_source);
		$criteria->compare('event_source', $this->event_source);
		$criteria->compare('create_date', $this->create_date, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}