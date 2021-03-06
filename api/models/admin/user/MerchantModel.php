<?php

/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact merchant@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace app\models\admin\user;

//引入各表实体
use app\models\core\TableModel;
use yii\db\Exception;

/**
 *
 * @version   2018年04月16日
 * @author    YangJing <120912212@qq.com>
 * @copyright Copyright 2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 *
 * @Bean()
 */
class MerchantModel extends TableModel {

    /**
     * 查询列表接口
     * 地址:/admin/merchant/list
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function findall($params) {
        //数据库操作
        $table = new TableModel();

        try {
            $params['delete_time is null'] = null;
            if (isset($params['searchName'])) {
                $params['searchName'] = trim($params['searchName']);
                $params["phone like '%{$params['searchName']}%'"] = null;
                unset($params['searchName']);
            }
            $params['table'] = "merchant_user";
            $res = $table->tableList($params);
            $app = $res['app'];
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        //返回数据 时间格式重置
        for ($i = 0; $i < count($app); $i++) {
            if (isset($app[$i]['create_time'])) {
                $app[$i]['create_time'] = date('Y-m-d H:i:s', $app[$i]['create_time']);
                if ($app[$i]['last_login_time'] != "") {
                    $app[$i]['last_login_time'] = date('Y-m-d H:i:s', $app[$i]['last_login_time']);
                }
                if ($app[$i]['update_time'] != "") {
                    $app[$i]['update_time'] = date('Y-m-d H:i:s', $app[$i]['update_time']);
                }
            }
        }
        if (empty($app)) {
            return result(204, '未找到对应数据');
        } else {
            return ['status' => 200, 'message' => '请求成功', 'data' => $app, 'count' => $res['count']];
        }
    }

    /**
     * 查询单条接口
     * 地址:/admin/merchant/single
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function find($params) {
        $table = new TableModel();
        //数据库操作
        try {
            $app = $table->tableSingle('merchant_user', ['id' => $params['id'], 'delete_time is null' => null]);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (gettype($app) != 'array') {
            return result(204, '未找到对应数据');
        } else {
            $app['create_time'] = date('Y-m-d H:i:s', $app['create_time']);
            if ($app['update_time'] != "") {
                $app['update_time'] = date('Y-m-d H:i:s', $app['update_time']);
            }
            $rs = $table->tableSingle('system_auth_group_access', ['uid' => $app['id'], 'delete_time is null' => null, 'type=2']);
            $app['group_ids'] = $rs['group_ids'];
            return result(200, '请求成功', $app);
        }
    }

    /**
     * 新增接口
     * 地址:/admin/merchant/add
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function add($params) {
        //data 新增数据参数设置
        //操作数据库
        $table = new TableModel();
        try {
            $res = $table->tableAdd('merchant_user', $params);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (!$res) {
            return result(204, '新增失败');
        } else {
            return result(200, '请求成功', $res);
        }
    }

    /**
     * 删除接口
     * 地 址:/admin/merchant/delete
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function delete($params) {


        //where条件设置
        $where = ['id' => $params['id']];
        //params 参数设置
        unset($params['id']);
        $params['delete_time'] = time();
        //数据库操作
        $table = new TableModel();
        try {
            $res = $table->tableUpdate('merchant_user', $params, $where);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (!$res) {
            return result(204, '删除失败');
        } else {
            return result(200, '删除成功');
        }
    }

    /**
     * 更新接口
     * 地址:/admin/merchant/update
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function update($params) {
        //where 条件设置

        if (isset($params['id'])) {
            $where['id'] = $params['id'];
            unset($params['id']);
        }
        if (isset($params['phone'])) {
            $where['phone'] = $params['phone'];
            unset($params['phone']);
        }
        //params 参数值设置
        $params['update_time'] = time();
        //数据库操作
        $table = new TableModel();
        try {

            $res = $table->tableUpdate('merchant_user', $params, $where);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }

        if (!$res) {
            return result(204, '更新失败');
        } else {
            return result(200, '请求成功');
        }
    }

    /**
     * 查询单条接口
     * 地址:/admin/merchant/single
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function findAli($params) {
        $table = new TableModel();
        //数据库操作
        try {
            $app = $table->tableSingle('merchant_user', ['id' => $params['id'], 'delete_time is null' => null]);
            $config = json_decode($app['config'], true);
            $ali = $config['ali_pay'];
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (gettype($app) != 'array') {
            return result(204, '查询失败');
        } else {
            $app['create_time'] = date('Y-m-d H:i:s', $app['create_time']);
            if ($app['update_time'] != "") {
                $app['update_time'] = date('Y-m-d H:i:s', $app['update_time']);
            }
            return result(200, '请求成功', $ali);
        }
    }

    /**
     * 查询单条接口
     * 地址:/admin/merchant/single
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function findWeixin($params) {
        $table = new TableModel();
        //数据库操作
        try {
            $app = $table->tableSingle('merchant_user', ['id' => $params['id'], 'delete_time is null' => null]);
            $config = json_decode($app['config'], true);
            $weixin = $config['weixin_pay'];
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (gettype($weixin) != 'array') {
            return result(204, '查询失败');
        } else {
            $app['create_time'] = date('Y-m-d H:i:s', $app['create_time']);
            if ($app['update_time'] != "") {
                $app['update_time'] = date('Y-m-d H:i:s', $app['update_time']);
            }
            return result(200, '请求成功', $weixin);
        }
    }

    /**
     * 查询单条接口
     * 地址:/admin/merchant/single
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function findkey($params) {
        $table = new TableModel();
        //数据库操作
        try {
            $app = $table->tableSingle('merchant_user', ['key' => $params['id'], 'delete_time is null' => null]);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (gettype($app) != 'array') {
            return result(204, '未找到对应数据');
        } else {
            $app['create_time'] = date('Y-m-d H:i:s', $app['create_time']);
            if ($app['update_time'] != "") {
                $app['update_time'] = date('Y-m-d H:i:s', $app['update_time']);
            }
            return result(200, '请求成功', $app);
        }
    }

    /**
     * 更新接口
     * 地址:/admin/merchant/update
     * @throws Exception if the model cannot be found
     * @return array
     */
    public function updatePhone($params) {
        //where 条件设置

        if (isset($params['id'])) {
            $where['id'] = $params['id'];
            unset($params['id']);
        }
        //params 参数值设置
        $params['update_time'] = time();
        //数据库操作
        $table = new TableModel();
        try {
            $res = $table->tableUpdate('merchant_user', $params, $where);
        } catch (Exception $ex) {
            return result(500, '数据库操作失败');
        }
        if (!$res) {
            return result(204, '更新失败');
        } else {
            return result(200, '请求成功');
        }
    }
}
