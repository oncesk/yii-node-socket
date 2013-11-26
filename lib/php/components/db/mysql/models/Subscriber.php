<?php

/**
 * This is the model class for table "ns_subscriber".
 *
 * The followings are the available columns in table 'ns_subscriber':
 * @property integer $id
 * @property string $role
 * @property integer $user_id
 * @property string $sid
 * @property string $sid_expiration
 * @property string $create_date
 *
 * Behaviors
 *
 * The followings are the available model relations:
 * @property NsChannelSubscriber[] $nsChannelSubscribers
 */
class Subscriber extends CActiveRecord
{


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Subscriber the static model class
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
		return 'ns_subscriber';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('role, sid', 'length', 'max'=>255),
			array('sid_expiration', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, role, user_id, sid, sid_expiration, create_date', 'safe', 'on'=>'search'),
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
			'nsChannelSubscribers' => array(self::HAS_MANY, 'NsChannelSubscriber', 'subscriber_id'),
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
			'role' => Yii::t('admin', 'Role'),
			'user_id' => Yii::t('admin', 'User'),
			'sid' => Yii::t('admin', 'Sid'),
			'sid_expiration' => Yii::t('admin', 'Sid Expiration'),
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
		$criteria->compare('role',$this->role,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('sid',$this->sid,true);
		$criteria->compare('sid_expiration',$this->sid_expiration,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}