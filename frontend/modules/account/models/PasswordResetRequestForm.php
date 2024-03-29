<?php

namespace frontend\modules\account\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset request form.
 */
class PasswordResetRequestForm extends Model
{
    public $email;
//    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => User::class, 'filter' => ['status' => User::STATUS_ACTIVE]],

            // verifyCode needs to be entered correctly
            //['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'Email'),
//            'verifyCode' => Yii::t('frontend', 'Verification code'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $user->generateAccessToken();
        } else {
            return false;
        }

        if (!$user->save()) {
            return false;
        }
        //\appxq\sdii\utils\VarDumper::dump($user);
        $name = 'คลังสมบัติของพิพิธภัณฑ์ศิริราช(Siriraj Museum Treasure)';
        return Yii::$app->mailer->compose('passwordReset', ['user' => $user])
            ->setFrom([Yii::$app->params['robotEmail'] => 'Siriraj Museum Treasure' . ' robot'])
            ->setTo($this->email)
            ->setSubject(Yii::t('frontend', 'รีเซ็ตรหัสผ่าน {name}', ['name' => $name]))
            ->send();
    }
}
