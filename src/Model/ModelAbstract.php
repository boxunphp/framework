<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Model;

use Fw\Cache\CacheAbstract;
use Fw\Instance\InstanceTrait;
use Fw\Database\Drivers\MysqlDriver;

class ModelAbstract
{
    use InstanceTrait;
    /**
     * @var MysqlDriver
     */
    private $db;
    protected $configKey = '';

    protected $primaryKey = 'id';
    protected $isAutoIncr = true;
    protected $table = '';

    protected $allowCache = false;
    /**
     * @var CacheAbstract
     */
    protected $cacheClass;

    /**
     * ModelAbstract constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        // 必须在全局定义环境函数, 用于获取配置
        if (!function_exists('env')) {
            throw new \Exception('function env cannot be defined!', E_ERROR);
        }

        $config = env($this->configKey);
        $this->db = MysqlDriver::getInstance($config);
    }

    /**
     * @return MysqlDriver
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * @return CacheAbstract
     */
    protected function cache()
    {
        $cacheClass = $this->cacheClass;
        return $cacheClass::getInstance();
    }

    /**
     * 获取单个
     *
     * @param int|string $id
     * @return bool|mixed|null
     * @throws \Fw\Exception\Exception
     */
    public function getOne($id)
    {
        if ($this->allowCache) {
            $info = $this->cache()->get($id);
            if (!is_null($info) && $info !== false) {
                return $info;
            }
        }
        $info = $this->db()->table($this->table)->where($this->primaryKey, $id)->limit(1)->fetch();
        if (!$info) {
            $info = [];
        }

        if ($this->allowCache) {
            $this->cache()->set($id, $info);
        }

        return $info;
    }

    /**
     * 批量获取
     *
     * @param array $idArr
     * @param array $fields
     * @return array|bool
     * @throws \Fw\Exception\Exception
     */
    public function getMulti(array $idArr, array $fields = [])
    {
        if (!$idArr) {
            return [];
        }
        if ($fields && !in_array($this->primaryKey, $fields)) {
            return false;
        }
        $data = [];

        if ($this->allowCache) {
            $cacheData = $this->cache()->getMulti($idArr);
            $noCacheIdArr = [];
            foreach ($idArr as $id) {
                if (!empty($cacheData[$id])) {
                    $data[$id] = $cacheData[$id];
                } elseif (!isset($cacheData[$id]) || is_null($cacheData[$id]) || $cacheData[$id] === false) {
                    $noCacheIdArr[] = $id;
                }
            }
            $idArr = $noCacheIdArr;
        }

        if ($idArr) {
            $list = $this->db()->table($this->table)->fields($fields)->where($this->primaryKey, $idArr)->fetchAll();
            if (!$list) {
                $list = [];
            }

            $noCacheData = [];
            foreach ($list as $item) {
                $data[$item[$this->primaryKey]] = $item;
                if ($this->allowCache) {
                    $noCacheData[$item[$this->primaryKey]] = $item;
                }
            }
            if ($this->allowCache && $noCacheData) {
                $this->cache()->setMulti($noCacheData);
            }
            unset($noCacheData);
        }

        return $data;
    }

    /**
     * 获取列表
     *
     * @param int $page 当前页
     * @param int $record 每页记录数
     * @param array $filter
     * @param array $fields
     * @param array $orderBy
     * @return array|bool
     * @throws \Fw\Exception\Exception
     */
    public function getList($page, $record, array $filter = [], array $fields = [], array $orderBy = [])
    {
        $this->db()->table($this->table)->fields($fields);
        if ($record) {
            $this->db()->page($page)->record($record);
        }
        foreach ($orderBy as $field => $sort) {
            $this->db()->orderBy($field, $sort);
        }
        $this->filter($filter);
        $list = $this->db()->fetchAll();
        if (!$list) {
            return [];
        }

        return $list;
    }

    /**
     * 获取记录数
     *
     * @param array $filter
     * @return int
     * @throws \Fw\Exception\Exception
     */
    public function getTotal(array $filter = [])
    {
        $this->db()->table($this->table)->fields('COUNT(*) AS total');
        $this->filter($filter);
        $info = $this->db()->fetch();
        return $info && isset($info['total']) ? intval($info['total']) : 0;
    }

    /**
     * 新增
     *
     * @param array $data
     * @return bool|int|string
     * @throws \Fw\Exception\Exception
     */
    public function insert(array $data)
    {
        if (!$data) {
            return false;
        }
        $this->db()->table($this->table)->insert($data);
        if ($this->isAutoIncr) {
            $result = $this->db()->lastInsertId();
        } else {
            $result = $this->db()->exec();
        }
        if (!$result) {
            return $result;
        }

        if ($this->allowCache) {
            if ($this->isAutoIncr) {
                $data[$this->primaryKey] = $result;
            }
            $this->cache()->set($data[$this->primaryKey], $data);
        }
        return $result;
    }

    /**
     * 更新
     *
     * @param int|string $id
     * @param array $data
     * @return bool|int
     * @throws \Fw\Exception\Exception
     */
    public function update($id, array $data)
    {
        $result = $this->db()->table($this->table)->where($this->primaryKey, $id)->update($data)->exec();
        if (!$result) {
            return $result;
        }
        if ($this->allowCache) {
            // 有可能已取到最新
            $originInfo = $this->getOne($id);
            $info = array_merge($originInfo, $data);
            $this->cache()->set($id, $info);
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param int|string $id
     * @return bool|int
     * @throws \Fw\Exception\Exception
     */
    public function delete($id)
    {
        if ($this->allowCache) {
            if (is_array($id)) {
                $this->cache()->deleteMulti($id);
            } else {
                $this->cache()->delete($id);
            }
        }
        return $this->db()->table($this->table)->where($this->primaryKey, $id)->delete()->exec();
    }

    /**
     * 批量写入
     *
     * @param array $data
     * @return bool|int|string
     * @throws \Fw\Exception\Exception
     */
    public function insertMulti(array $data)
    {
        $this->db()->table($this->table)->insertMulti($data);
        if ($this->isAutoIncr) {
            $result = $this->db()->lastInsertId();
        } else {
            $result = $this->db()->exec();
        }
        if (!$result) {
            return $result;
        }

        if ($this->allowCache) {
            $cacheData = [];
            foreach ($data as $key => $item) {
                if ($this->isAutoIncr) {
                    $id = $result + $key;
                    $item[$this->primaryKey] = $id;
                    $cacheData[$id] = $item;
                } else {
                    $cacheData[$item[$this->primaryKey]] = $item;
                }
            }
            $this->cache()->setMulti($cacheData);
        }

        return $result;
    }

    /**
     * 批量更新
     *
     * @param array $data
     * @return bool|int
     * @throws \Fw\Exception\Exception
     */
    public function updateMulti(array $data)
    {
        $result = $this->db()->table($this->table)->updateMulti($data, $this->primaryKey)->exec();
        if (!$result) {
            return $result;
        }

        if ($this->allowCache) {
            $idArr = array_column($data, $this->primaryKey);
            $this->cache()->deleteMulti($idArr);
        }

        return $result;
    }

    /**
     * getList和getTotal的过滤项
     *
     * @param array $filter
     * @return void
     */
    protected function filter(array $filter)
    {
        // ...
    }
}
