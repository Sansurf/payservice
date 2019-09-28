<?php

/*
2. Эмулятор приема платежа
    Получает запрос
    Проверяет подпись
    Парсит пакет данных и добавляет данные в очередь обработки
    Высчитывает сумму с коммиссией
    Сохраняет в локальную базу в формате
    {
    	id: идентификатор транзакции
        user_id: идентификатор клиента
        sum: сумма с учетом коммиссии
    }
    и добавляет запись в таблицу user_wallet либо наращивает сумму в записи в формате
    {
        user_id: идентификатор пользователя
        sum: сумма на счету
    }
 */

namespace app\controllers;

use yii\web\Controller;
use app\jobs\PaymentJob;


/**
 * Description of PaymentGetController
 *
 * @author alexandr
 */
class PayGetController extends Controller
{
    public function actionIndex()
    {
        if ($_POST | $_COOKIE) {
            if ($this->verifyKey()) {
                if ($this->send()) {
                    echo "Данные добавлены в очередь";
                }
                else
                    echo "Данные не удалось добавить в очередь";
            }
        }
    }

    /**
     * Высчитывает сумму с коммиссией и передает на добавление в очередь
     */
    private function send()
    {
        if ($data = json_decode($_POST['data'], true)) {
            foreach ($data as $key => $entry) {
                $sum = $entry['sum'];
                $commission = $entry['commission'];
                $userId = $entry['user_id'];

                $total = 0; // сумма с учетом коммиссии
                if ($commission < 1)
                    $total = $sum * $commission;
                elseif ($commission > 1)
                    $total = $sum + $sum * ($commission - 1);
                else
                    $total = $sum;

                $this->queueAdd(new PaymentJob([
                    'user_id' => $userId,
                    'sum' => $total
                ]));
            }

            return true;
        }

        return false;
    }

    /**
     * Добавляет данные в очередь
     *
     * @param $data
     * @return bool|int
     */
    private function queueAdd($data)
    {
        if ($id = \Yii::$app->queue->push($data))
            if (\Yii::$app->queue->run(false))
                return $id;

        return false;
    }

    /**
     * Проверяет ключ
     *
     * @return bool
     */
    private function verifyKey()
    {
        if ($_COOKIE['key'] == md5($_POST['data']))
            return true;

        return false;
    }
}
