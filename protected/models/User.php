<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id_user
 * @property string $username
 * @property string $password
 * @property string $enkrip
 * @property string $email
 * @property string $inisial
 * @property string $deskripsi
 * @property integer $id_level
 *
 * The followings are the available model relations:
 * @property Level $idLevel
 */
class User extends CActiveRecord
{
	public $password2;
	public $verifyCode;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password,  email, verifyCode', 'required'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')),
			array('id_level', 'numerical', 'integerOnly'=>true),
			array('username, email', 'length', 'max'=>30),
			array('username', 'filter', 'filter'=>'strtolower'),
			array('username','unique'),
			array('password, enkrip', 'length', 'max'=>50,'min'=>5),
			array('password2', 'length', 'max'=>50, 'min'=>5),
			array('password', 'compare','compareAttribute'=>'password2'),
			array('inisial', 'length', 'max'=>10),
			array('email','email','checkMX'=>true),
			array('deskripsi', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_user, username, email, inisial, deskripsi, id_level', 'safe', 'on'=>'search'),
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
			'idLevel' => array(self::BELONGS_TO, 'Level', 'id_level'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_user' => 'Id User',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'inisial' => 'Inisial',
			'deskripsi' => 'Deskripsi',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('enkrip',$this->enkrip,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('inisial',$this->inisial,true);
		$criteria->compare('deskripsi',$this->deskripsi,true);
		$criteria->compare('id_level',$this->id_level);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function validatePassword($password)
	{
		return $this->hashPassword($password,$this->enkrip)===$this->password;
	}

	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}

	public function beforeSave()
	{
		$isinya=$this->generateSalt();
		$dua=$this->password;
		$this->enkrip=$isinya;
		$this->password=$this->hashPassword($dua,$isinya);
		$this->id_level=3;
		return true;
	}

	protected function generateSalt()
	{
		return uniqid('',true);
	}
}
