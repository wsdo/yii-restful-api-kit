<?php
namespace app\models;

use yii\base\Model;
use app\models\User;
// use yii\helpers\VarDumper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    // public $realname;
    public $email;
    public $password;    
    public $password_repeat;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => '用户名已经存在.'],
            ['username', 'string', 'min' => 4, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => '邮件地址已经存在.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            
            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致！'],
            
            	// ['realname','required'],
        	    // ['realname','string','max'=>128],
        	 	
        ];
    }

    public function attributeLabels()
    {
    	return [
    			'username' => '用户名',
    			// 'realname' => '姓名',
    	        'password' => '密码',
    	        'password_repeat'=>'重输密码',
    			'email' => '电子邮箱',
    	];
    }
    
    
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        // $user->realname = $this->realname;
        $user->email = $this->email;
         
        
        $user->setPassword($this->password);
        $user->generateAuthKey();
 //  $user->save(); VarDumper::dump($user->errors);exit(0);
        return $user->save() ? $user : null;
    }
}
