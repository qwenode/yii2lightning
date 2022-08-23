<?php


namespace qwenode\yii2lightning;


use ErrorException;
use Exception;
use qwephp\assert\Assertion;
use Redis;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Security;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\queue\Queue;
use yii\redis\Connection;
use yii\redis\SocketException;
use yii\web\Application;
use yii\web\Controller;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\web\User;

/**
 * 所有Yii快捷访问方式,工具集合等
 * Class LightningHelper
 * @package qwenode\yii2lightning
 */
class LightningHelper
{
    /**
     * @param string $anchor
     * @return \yii\console\Response|Response
     */
    public static function refresh($anchor = '')
    {
        return Yii::$app->response->refresh($anchor);
    }
    
    /**
     * @param string $message
     * @param ...$params
     * @return string
     */
    public static function message(string $message, ...$params): string
    {
        $explode = explode('{}', $message);
        $newMsg  = '';
        foreach ($explode as $k => $value) {
            $newMsg .= $value;
            if (isset($params[$k])) {
                $fillValue = $params[$k];
                if (is_array($fillValue)) {
                    $fillValue = json_encode($fillValue);
                }
                $newMsg .= $fillValue;
            }
        }
        return $newMsg;
    }
    
    /**
     * get one model error message
     * @param Model $model
     * @return array|mixed|string
     */
    public static function getMessage(Model $model): mixed
    {
        $message = '';
        if (!$model->hasErrors()) {
            return $message;
        }
        $messages = $model->getFirstErrors();
        if (is_array($messages)) {
            if (count($messages) != 0) {
                $message = array_values($messages)[0];
            }
        } else {
            $message = $messages;
        }
        return $message;
    }
    
    /**
     * @param $model
     * @param $property
     * @param mixed|string $default
     * @return mixed|string
     */
    public static function getPropertyValue($model, $property, $default = ''): mixed
    {
        if ($model instanceof ActiveRecord) {
            if ($model->hasAttribute($property)) {
                return $model->getAttribute($property);
            }
        }
        if (!is_object($model) || !property_exists($model, $property)) {
            return $default;
        }
        return $model->{$property};
    }
    
    /**
     * @param $model BaseActiveRecord
     * @param string $field
     * @param $default
     * @return mixed|string
     */
    public static function getActiveRecordValue($model, string $field, string $default = '')
    {
        if ($model == null) {
            return $default;
        }
        if (!($model instanceof BaseActiveRecord)) {
            return $default;
        }
        return $model->getAttribute($field) ?? $default;
    }
    
    public static function getViewI18n(Controller $controller, $view = '')
    {
        $defaultView = $view;
        if ($defaultView == '') {
            $defaultView = $controller->action->id;
        }
        $app = LightningHelper::getApplication();
        if ($app->language != $app->sourceLanguage) {
            $newView = sprintf('%s-%s', $defaultView, $app->language);
            $path    = $controller->view->theme->getPath(sprintf('%s/%s.php', $controller->id, $newView));
            if (file_exists($path)) {
                $defaultView = $newView;
            }
        }
        return $defaultView;
    }
    
    /**
     * @param Model $model
     * @param int $code
     * @return void
     * @throws ErrorException
     */
    public static function throwError(Model $model, int $code = 0): void
    {
        $message = self::getMessage($model);
        if ($message != '') {
            throw new ErrorException($message, $code);
        }
    }
    
    /**
     * @throws ErrorException
     */
    public static function throwNull($var, int $code = 0, string $message = null,): void
    {
        if ($message == null) {
            $message = '数据不存在';
        }
        if (Assertion::isNull($var)) {
            throw new ErrorException($message, $code);
        }
    }
    
    /**
     * @return string|null
     */
    public static function getReturnUrl()
    {
        $referrer = self::getRequest()->getReferrer();
        if ($referrer == '') {
            $referrer = '/';
        }
        return $referrer;
    }
    
    /**
     * @return Queue
     * @throws InvalidConfigException
     */
    public static function getQueue(): Queue
    {
        return LightningHelper::getApplication()->get('queue');
    }
    
    /**
     * @param callable $callable
     * @param string $collection the key you want to storage
     * @param int $duration
     * @return false|mixed
     */
    public static function withCache(callable $callable, string $collection, int $duration = 86400): mixed
    {
        $cache  = LightningHelper::getCache();
        $result = $cache->get($collection);
        if ($result !== false && $duration > 0) {
            return $result;
        }
        $result = call_user_func($callable);
        if ($result !== false) {
            $cache->set($collection, $result, $duration);
        }
        return $result;
    }
    
    /**
     * @param string $key
     * @return void
     * @see withCache
     */
    public static function invalidateCache(string $key): void
    {
        $cache = LightningHelper::getCache();
        $cache->delete($key);
    }
    
    /**
     * 跳转到上一页
     * @param null $errorMessage 错误消息
     * @return Response
     */
    public static function returnPreviousPage($errorMessage = NULL)
    {
        FlashHelper::error($errorMessage);
        return self::getResponse()->redirect(self::getReturnUrl());
    }
    
    /**
     * get default database connection
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        return self::getApplication()->getDb();
    }
    
    /**
     * quick call: Yii::$app->db->cache()
     * @param callable $callable
     * @param string $collection the key you want to storage
     * @param int $duration
     * @return mixed
     * @throws Throwable
     */
    public static function withDbCache(callable $callable, string $collection, int $duration = 86400): mixed
    {
        return self::getApplication()->getDb()->cache($callable, $duration, new TagDependency(['tags' => $collection]));
    }
    
    /**
     * @param string $collection the key you want to clean
     * @return void
     */
    public static function invalidateDbCache(string $collection): void
    {
        TagDependency::invalidate(static::getCache(), $collection);
    }
    
    /**
     * get default cache connection
     * @return CacheInterface
     */
    public static function getCache()
    {
        return self::getApplication()->getCache();
    }
    
    /**
     * @return \yii\console\Application|Application
     */
    public static function getApplication()
    {
        return Yii::$app;
    }
    
    
    /**
     * @return User
     */
    public static function getUser()
    {
        return Yii::$app->getUser();
    }
    
    /**
     * @return int|string|null
     */
    public static function getCurrentUserID()
    {
        return static::getUser()->getId();
    }
    
    /**
     * @param bool $autoRenew
     * @return bool|IdentityInterface|null
     * @throws Throwable
     */
    public static function getUserIdentity($autoRenew = TRUE)
    {
        return static::getUser()->getIdentity($autoRenew);
    }
    
    /**
     * @return \yii\console\Request|Request
     */
    public static function getRequest()
    {
        return Yii::$app->getRequest();
    }
    
    /**
     * @return \yii\console\Response|Response
     */
    public static function getResponse()
    {
        return Yii::$app->getResponse();
    }
    
    /**
     * @return Security
     */
    public static function getSecurity()
    {
        return Yii::$app->getSecurity();
    }
    
    /**
     * @return Session
     */
    public static function getSession()
    {
        return Yii::$app->getSession();
    }
    
    /**
     * @return Redis|Connection
     */
    public static function getRedis()
    {
        return Yii::$app->get('redis');
    }
    
    /**
     * 此方法会检查链接可用性并自动重连
     * @return \yii\db\Connection
     * @throws \yii\db\Exception
     */
    public static function getKeepaliveDb()
    {
        $connection = Yii::$app->getDb();
        try {
            $connection->createCommand('select 1')->execute();
            return $connection;
        } catch (Exception $exception) {
            $connection->close();
            $connection->open();
        }
        throw new Exception('database reconnect fail.');
    }
    
    /**
     * 此方法会检查链接可用性并自动重连
     * @return Redis|Connection
     * @throws \yii\db\Exception
     * @throws InvalidConfigException
     */
    public static function getKeepaliveRedis()
    {
        /**
         * @var $redis Connection
         */
        $redis = Yii::$app->get('redis');
        try {
            //检测链接可用性
            $redis->set('KEEPALIVE', 1);
            return $redis;
        } catch (Exception $exception) {
            //重连
            $redis->close();
            $redis->open();
        }
        throw new SocketException('redis reconnect fail.');
    }
    
    public static function asDate($value, $format = NULL)
    {
        return Yii::$app->getFormatter()->asDate($value, $format);
    }
    
    public static function asDatetime($value, $format = NULL)
    {
        return Yii::$app->getFormatter()->asDatetime($value, $format);
    }
    
    public static function asTime($value, $format = NULL)
    {
        return Yii::$app->getFormatter()->asTime($value, $format);
    }
}