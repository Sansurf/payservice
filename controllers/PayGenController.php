<?php

/*
1. Эмулятор платежной системы.
    Рандомно генерирует от 1 до 10 запросов в формате:
    {
        id: идентификатор транзакции 
        sum: сумма (от 10р. до 500р.)
        commision: коммиссия (от 0,5% до 2%)
        order_number: идентификатор клиента (от 1 до 20)
    }
    Сохраняет данные локально.
    Делает цифровую подпись (механизм на усмотрение соискателя)
    Отправляет пакетом с интервалом 20 секунд на второй сервис
    Повторяет циклично
 */

namespace app\controllers;

use yii\web\Controller;


/**
 * Description of PayGenController
 *
 * @author alexandr
 */
class PayGenController extends Controller
{
    private $pack;
    private $key;

    /**
     * Рендерит Пакет с данными и сообщает об успешности записи в файл и отправки данных
     */
    public function actionIndex()
    {
        if ($data = $this->createDataPack()) {
            if ($this->savePackLocally('../runtime/data.json', $this->pack)) {
                if ($resp = $this->send())
                    \YII::$app->session->setFlash('writed', 'Данные сохранены и отправлены');
                else
                    \YII::$app->session->setFlash('notWrited', 'Не удалось сохранить или отправить данные');

                return $this->render('index', ['data' => $data, 'resp' => $resp]);
            }
        }

        return false;
    }

    /**
     * Создает Пакет с запросами
     * 
     * @return array
     */
    private function createDataPack() 
    {
        $data = [];
        $rand = rand(1, 10);
        
        for ($i=1; $i<=$rand; $i++) {
            $sum = rand(10, 500);
            $commission = rand(5, 20)/10;
            $order_number = rand(1, 20);
            
            $data[$i] = ['sum' => $sum, 'commission' => $commission, 'user_id' => $order_number];
        }

        // Записывает переменные класса
        $this->pack = json_encode($data);
        $this->key = md5($this->pack);

        return $data;
    }

    /**
     * Сохраняет Пакет с запросами в файл
     *
     * @param string    $path   Путь для сохранения файла
     * @param string    $data   Данные для сохранения
     * @return boolean
     */
    private function savePackLocally($path, $data)
    {
        $writeData = file_put_contents($path, $data, FILE_APPEND);

        if ($writeData) return true;

        return false;
    }

    /**
     * Отправляет пакет данных на второй сервис
     *
     * @return bool|string
     */
    private function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://payservice/pay-get');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=$this->pack");
        curl_setopt($ch, CURLOPT_COOKIE, "key=$this->key");
        $out = curl_exec($ch);
        curl_close($ch);

        if ($out)
            return $out;
        else
            return "cURL Error: " . curl_error($ch);
    }
}