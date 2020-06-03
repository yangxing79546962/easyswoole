<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Component\Di;
use EasySwoole\ORM\DbManager;
use EasySwoole\Utility\File;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\Config;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        self::loadConf();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $dbConf = \EasySwoole\EasySwoole\Config::getInstance()->getConf('mysql');
//        var_dump($dbConf);
        $config = new Config();
        $config->setDatabase($dbConf['database']);
        $config->setUser($dbConf['username']);
        $config->setPassword($dbConf['password']);
        $config->setHost($dbConf['host']);
//        $config->setGetObjectTimeout(3.0); //设置获取连接池对象超时时间
//        $config->setIntervalCheckTime(30*1000); //设置检测连接存活执行回收和创建的周期
//        $config->setMaxIdleTime(15); //连接池对象最大闲置时间(秒)
//        $config->setMaxObjectNum(100); //设置最大连接池存在连接对象数量
//        $config->setMinObjectNum(50); //设置最小连接池存在连接对象数量
        DbManager::getInstance()->addConnection(new Connection($config));
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {

        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    /**
     * 加载配置文件
     */
    public static function loadConf()
    {
        //遍历目录中的文件
        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/Conf');
        if (is_array($files)) {
            //$files['files'] 一级目录下所有的文件,不包括文件夹
            foreach ($files['files'] as $file) {
                $fileNameArr = explode('.', $file);
                $fileSuffix = end($fileNameArr);

                if ($fileSuffix == 'php') {
                    \EasySwoole\EasySwoole\Config::getInstance()->loadFile($file);//引入之后,文件名自动转为小写,成为配置的key
                }
            }
        }
    }
}