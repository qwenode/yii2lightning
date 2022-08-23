<?php

use qwenode\yii2lightning\RuleBuilder;

require '../vendor/autoload.php';

var_dump(RuleBuilder::stringLength(1, 30, 'xxx', 'bbb', 'ccc'));