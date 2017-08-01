<?php

namespace frontend\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $sheng
 * @property string $area
 * @property string $city
 * @property string $address
 * @property string $tel
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','sheng','city','area','tel','address'],'required'],
            [['status'], 'safe'],
            [['name'], 'string', 'max' => 20],
            [['city', 'address','area','sheng'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收货人',
            'city' => '所在地区',
            'address' => '详细地址',
            'tel' => '电话',
            'status' => '设为默认地址',
            'area'=>'省、市、区',
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            //增加
           $this->sheng=self::getName($this->sheng)->name;
            $this->city=self::getName($this->city)->name;
            $this->area=self::getName($this->area)->name;
            $this->user_id=1;
//            $this->user_id=\Yii::$app->user->identity->id;
            if($this->status){
                $this->status=1;
            }else{
                $this->status=0;
            }
        }else{
            //修改
            $this->sheng=self::getName($this->sheng)->name;
            $this->city=self::getName($this->city)->name;
            $this->area=self::getName($this->area)->name;
            if($this->status){
                $this->status=1;
            }else{
                $this->status=0;
            }
        }

        return parent::beforeSave($insert);
    }

    //根据id查询省市区的名字
    public static function getName($id){
        $name=Locations::find()->select('name')->where(['id'=>$id])->one();
        return $name;
    }
}
