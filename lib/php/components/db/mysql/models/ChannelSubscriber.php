<?php

/**
 * This is the model class for table "ns_channel_subscriber".
 *
 * The followings are the available columns in table 'ns_channel_subscriber':
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $channel_id
 * @property integer $can_send_event_from_php
 * @property integer $can_send_event_from_js
 * @property string $create_date
 *
 * Behaviors
 *
 * The followings are the available model relations:
 * @property NsChannel $channel
 * @property NsSubscriber $subscriber
 */
class ChannelSubscriber extends CActiveRecord
{


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ChannelSubscriber the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ns_channel_subscriber';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js', 'required'),
			array('subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, subscriber_id, channel_id, can_send_event_from_php, can_send_event_from_js, create_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'channel' => array(self::BELONGS_TO, 'NsChannel', 'channel_id'),
			'subscriber' => array(self::BELONGS_TO, 'NsSubscriber', 'subscriber_id'),
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
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('admin', 'ID'),
			'subscriber_id' => Yii::t('admin', 'Subscriber'),
			'channel_id' => Yii::t('admin', 'Channel'),
			'can_send_event_from_php' => Yii::t('admin', 'Can Send Event From Php'),
			'can_send_event_from_js' => Yii::t('admin', 'Can Send Event From Js'),
			'create_date' => Yii::t('admin', 'Create Date'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('subscriber_id',$this->subscriber_id);
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('can_send_event_from_php',$this->can_send_event_from_php);
		$criteria->compare('can_send_event_from_js',$this->can_send_event_from_js);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}