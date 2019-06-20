<?php


namespace app\manageconfig\validate;

use think\Validate;

class ScheduleDefault extends Validate
{
    protected $rule=[
        'day'=>'require|integer|between:1,7',
        'time'=>'require',
        'place'=>'require|length:1,30',
        'item'=>'require|length:1,30',
        'note'=>'max:2000'
    ];
    protected $message=[
        'day'=>'日期为1到7的整数',
        'time'=>'地点长度为1到30',
        'place'=>'地点长度为1到30',
        'item'=>'事项长度为1到30',
        'note'=>'备注长度最大2000'
    ];
}