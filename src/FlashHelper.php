<?php

namespace qwenode\yii2lightning;


use yii\base\Model;

class FlashHelper
{
    public static function info($message, ...$params)
    {
        \Yii::$app->session->setFlash('info', static::getFirstMessage($message, $params));
    }

    public static function success($message, ...$params)
    {
        \Yii::$app->session->setFlash('success', static::getFirstMessage($message, $params));
    }

    public static function error($message, ...$params)
    {
        \Yii::$app->session->setFlash('error', static::getFirstMessage($message, $params));
    }

    /**
     * @param array|Model|string $messages
     * @return array|mixed|string
     */
    public static function arrayMessagesToString($messages)
    {
        if ($messages instanceof Model) {
            $messages = $messages->getFirstErrors();
        }
        $message = '';

        if (is_array($messages)) {
            if (count($messages) != 0) {
                $message = array_values($messages)[0];
                if (is_array($message)) {
                    $message = self::arrayMessagesToString($message);
                }
            }
        } else {
            $message = $messages;
        }

        return $message;
    }

    public static function getFirstMessage($messages, $params)
    {
        $message = self::arrayMessagesToString($messages);
        if (strpos($message, '{0}') !== FALSE) {
            foreach ($params as $k => $v) {
                $message = str_replace('{' . $k . '}', $v, $message);
            }
        } else {
            $explode = explode('{}', $message);
            $newMsg = '';
            foreach ($explode as $k => $value) {
                $newMsg .= $value;
                if (isset($params[$k])) {
                    $newMsg .= self::arrayMessagesToString($params[$k]);
                }
            }
            $message = $newMsg;
        }

        return $message;
    }
}