
<?php
/**
 * register_shutdown_function,注册一个会在php中止时执行的函数,中止的情况包括发生致命错误、die之后、exit之后、执行完成之后都会调用register_shutdown_function里面的函数
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/15
 * Time: 17:41
 */
 
class Shutdown
{
    public function stop()
    {
        echo 'Begin.' . PHP_EOL;
        // 如果有发生错误（所有的错误，包括致命和非致命）的话，获取最后发生的错误
        if (error_get_last()) {
            print_r(error_get_last());
        }
 
        // ToDo:发生致命错误后恢复流程处理
 
        // 中止后面的所有处理
        die('Stop.');
    }
}
 
// 当PHP终止的时候（执行完成或者是遇到致命错误中止的时候）会调用new Shutdown的stop方法
register_shutdown_function([new Shutdown(), 'stop']);
