<?php

namespace qwenode\yii2lightning\validators;

use Yii;
use yii\validators\Validator;

class DomainValidator extends Validator
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is not a valid domain.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $value  = $model->$attribute;
        $result = $this->validateValue($value);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if ((preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $value) //valid chars check
                && preg_match("/^.{1,253}$/", $value) //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $value))) {//length of each label
                return null;
            }
        }

        return [$this->message, []];
    }
}