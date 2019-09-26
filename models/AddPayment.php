<?php

namespace app\models;

use yii\base\BaseObject;
use yii\queue\JobInterface;


class AddPayment extends BaseObject implements JobInterface
{
    public $user_id;
    public $sum;

    public function execute($queue)
    {
        // Добавляем или обновляем запись в user_wallet
        $wallet = UserWallet::findOne($this->user_id);
        if ($wallet) {      // если клиент найден - добавляем к имеющейся сумме
            $wallet->sum = $wallet->sum + $this->sum;
        } else {            // если не найден - создаем
            $wallet = new UserWallet();
            $wallet->setIsNewRecord(true);
            $wallet->id = $this->id;
            $wallet->sum = $this->sum;
        }
        $wallet->save();

        // Добавляем запись в payment
        $payment = new Payment();   // добавляем запись о платеже
        $payment->setIsNewRecord(true);
        $payment->user_id = $this->user_id;
        $payment->sum = $this->sum;
        $payment->save();
    }
}