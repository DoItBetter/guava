<?php

namespace common\components;

use common\configurations\EventConfiguration;
use common\filters\CorFilter;
use yii\rest\Controller;
use Yii;

class ApiController extends Controller
{
    protected $appVersion;

    protected $headers = [];

    public function behaviors()
    {
        $self = [
            'corsFilter' => [
                'class' => CorFilter::className(),
            ],
        ];
        return array_merge(parent::behaviors(), $self);
    }


    /**
     * 获取参数
     * 兼容get,post并且默认去除字符串中的空格
     * @param $key
     * @param string $default 默认值
     * @param bool $trim 默认去除左右空格，true:去除所有空格
     * @return array|mixed
     */
    protected function getParam($key, $default = '', $trim = false)
    {
        $value = Yii::$app->request->get($key);
        if (!isset($value)) {
            $value = Yii::$app->request->post($key);
        }
        if (!isset($value)) {
            $data = file_get_contents("php://input");
            $data = json_decode($data, true);
            $value = isset($data[$key]) ? $data[$key] : null;
        }
        if (!isset($value) && isset($default)) {
            $value = $default;
        }
        if ($trim && is_string($value)) {
            $value = trim($value);
        }
        return $value;
    }

    /**
     * @param $action
     * @param $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $subject = Yii::$app->controller->id . '/' . $action->id;
        Log::info($subject, $result, $subject);
        return $result;
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        (new EventConfiguration())->loadEvents();
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
