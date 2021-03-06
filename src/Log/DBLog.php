<?php
namespace Utils\Log;

/**
 * 数据库日志记录类
 * @author Tianfeng.Han
 */
class DBLog extends \Utils\Log implements \Utils\IFace\Log
{
    /**
     * @var \Utils\Database;
     */
    protected $db;
    protected $table;

    function __construct($config)
    {
        if (empty($config['table']))
        {
            throw new \Exception(__CLASS__ . ": require \$config['table']");
        }
        $this->table = $config['table'];
        if (isset($config['db']))
        {
            $this->db = \Utils::$php->db($config['db']);
        }
        else
        {
            $this->db = \Utils::$php->db('master');
        }
        parent::__construct($config);
    }

    function put($msg, $level = self::INFO)
    {
        $put['logtype'] = self::convert($level);
        $msg = $this->format($msg, $level);
        if ($msg)
        {
            $put['msg'] = $msg;
            \Utils::$php->db->insert($put, $this->table);
        }
    }

    function create()
    {
        return $this->db->query("CREATE TABLE `{$this->table}` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `addtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `logtype` TINYINT NOT NULL ,
            `msg` VARCHAR(255) NOT NULL
            )");
    }
}
