<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Database;

interface DriverInterface
{
    public function table($table, $alias = null, $condition = null);

    public function join($table, $condition, $alias = null, $type = null);

    public function leftJoin($table, $condition, $alias = null);

    public function rightJoin($table, $condition, $alias = null);

    public function fields($fiels);

    public function where($field, $value, $operator = null, $connector = null);

    public function orWhere($field, $value, $operator = null);

    public function beginWhereGroup();

    public function orBeginWhereGroup();

    public function endWhereGroup();

    public function between($field, $beginValue, $endValue);

    public function notBetween($field, $beginValue, $endValue);

    public function orBetween($field, $beginValue, $endValue);

    public function orNotBetween($field, $beginValue, $endValue);

    public function like($field, $value);

    public function orLike($field, $value);

    public function leftLike($field, $value);

    public function orLeftLike($field, $value);

    public function whereSql($where, array $params);

    public function orderBy($field, $sort = null);

    public function groupBy($field);

    public function limit($record, $offset = 0);

    public function record($record);

    public function page($page);

    //-------- 地平线 --------//

    public function fetch();

    public function fetchAll();

    public function insert(array $data);

    public function insertMulti(array $data);

    public function update(array $data);

    public function updateMulti(array $data, $index);

    public function delete();

    public function replace(array $data);

    public function increment(array $data);

    public function exec();

    public function lastInsertId();

    //-------- 地平线 --------//

    public function begineTrans();

    public function rollbackTrans();

    public function commitTrans();

    //-------- 地平线 --------//

    public function debug();
}
