<?php
/**
  * 此类实现的是sync接口
  * 由于使用了swoole的task，task进程必须是同步阻塞的
  * @author XiaodongHuang
  */

class IndexController extends ControllerBase
{
    /**
 	 * 私有，用$key获取参数并序列化返回
 	 *
 	 * @param string $key 键
 	 * @return array client传参数组
	 */

    private function __getParam($key)
    {
        $data = $this->table->get($key);
        return unserialize($data['param']);
    }

    /**
 	 * 用user_id获取用户信息,不返回密码;
 	 *
 	 * @return array 返回不包含password的用户数据
	 */

    public function getUserByIdAction($key)
    {
        $param = $this->__getParam($key);
        $user_id = $param['user_id'];

        $user = User::findFirst(array(
            "user_id = :user_id:",
            "bind" => array("user_id"=>intval($user_id))
        ));

        $ret = array_merge(
            $user->toArray(),
            array(
                "institution_name" => $user->Institution->institution_name,
                "institution_desc" => $user->Institution->institution_desc,
                "department_name"  => $user->Departments->department_name,
                "department_desc"  => $user->Departments->department_desc
            )
        );
        unset($ret['passwd']);//密码不返回

        $this->table->del($key);
        return $ret;
    }

    /**
 	 * 检查登陆用户名与密码是否匹配,使用手机号码、工号、qq号、邮箱与密码匹配.
 	 * 客户端需要提供的参数dorarpc client param:
     *
     * [1] login_type [login_q, login_phone, email, stuff_id]中一个
     *
     * [2] login_name 登录用户名值
     *
     * [3] password 用户密码.
 	 * @return array
	 */

    public function checkUserLoginInfoAction($key)
    {
        $param = $this->__getParam($key);
        $login_type = $param['login_type'];
        $login_name = $param['login_name'];
        $password = $param['password'];

        $user = User::findFirst(array(
            "$login_type = :login_name: AND passwd = :password:",
            "bind"=>array(
                "login_name"=>$login_name,
                "password"=>$password
            )
        ));

        if($user)
        {
            // 用户名与密码匹配，返回用户id
            $ret = array("result"=>"SUCCESS","user_id"=>$user->user_id);
        }else{
            // 不匹配
            $ret = array("result"=>"FAIL");
        }

        $this->table->del($key);
        return $ret;
    }

    /**
 	 * 用户名是否存在
 	 *
     * 客户端需要提供的参数dorarpc client param:
     *
     * [1] login_type [login_q, login_phone, email, stuff_id]中一个
     *
     * [2] login_name 登录用户名值
 	 * @return array
	 */

    public function checkLoginNameAction($key)
    {
        $param = $this->__getParam($key);
        $login_type = $param['login_type'];
        $login_name = $param['login_name'];

        $user = User::findFirst(array(
            "$login_type = :login_name:",
            "bind"=>array(
                "login_name"=>$login_name
            )
        ));

        if($user)
        {
            // 用户名与密码匹配，返回用户id
            $ret = array("result"=>"SUCCESS","user_id"=>$user->user_id);
        }else{
            // 不匹配
            $ret = array("result"=>"FAIL");
        }

        unset($user);
        $this->table->del($key);
        return $ret;
    }

    /**
 	 * 新增用户,客户端提供用户数据；不验证直接insert，返回user_id
	 */
	public function addUserAction($key)
    {
        $param = $this->__getParam($key);
        $new_user = $param['user'];

        $user = new User();
        $user->assign($new_user);
        if($user->save())
        {
            $ret = array("result"=>"SUCCESS","user_id"=>$user->user_id);
        }else{
            $ret = array("result"=>"FAIL","msg"=>$user->getMessages());
        }

        unset($user);
        $this->table->del($key);
        return $ret;
    }

    /**
 	 * 更新用户，客户端需提供user_id与其他键值对
	 */

     public function updateUserAction($key)
     {
         $param = $this->__getParam($key);
         $update_user = $param['user'];
         if(!isset($update_user['user_id'])){
             return array("result"=>"FAIL","msg"=>"Must need user ID.");
         }

         $user = User::findFirst(array(
            "user_id = :user_id:",
            "bind"=>array("user_id"=>$update_user['user_id'])
         ));

         if($user)
         {
             $user->assign($update_user);
             if($user->update())
             {
                 $ret = array("result"=>"SUCCESS","user_id"=>$user->user_id);
             }else{
                 $ret = array("result"=>"FAIL","msg"=>$user->getMessages());
             }
         }
         unset($user);
         $this->table->del($key);
         return $ret;

     }

     /**
 	 * 删除用户，需提供user_id
	 */
	public function deleteUserAction($key)
    {
        $param = $this->__getParam($key);
        if(isset($param['user_id']))
            $user_id = $param['user_id'];

        $user = User::findFirst(array(
           "user_id = :user_id:",
           "bind"=>array("user_id"=>$user_id)
        ));

        if($user->delete())
        {
            $ret = array("result"=>"SUCCESS");
        }else{
            $ret = array("result"=>"FAIL");
        }

        unset($user);
        $this->table->del($key);
        return $ret;
    }

    /**
 	 * 获取多用户数据，传入用户字段键值对，返回用户数组
     * fields必须为字段键值对数组
	 */
	public function getUsersAction($key)
    {
        $param = $this->__getParam($key);
        $fields = $param['fields'];

        #拼接查询
        $condition = " ";
        $and = False;
        foreach ($fields as $key => $value) {
            if($and) $condition .= " AND ";
            $condition .= "$key = :$key:";
            $and = True;
        }

        $users = User::find(array(
            $condition,
            "bind"=>$fields,
        ));
        if($users)
        {
            $users_detail = array();
            foreach ($users as $user) {
                #密码不返回
                $arr = $user->toArray();
                unset($arr["passwd"]);
                $users_detail[] = array_merge($arr,
                    array(
                        "institution_name" => $user->Institution->institution_name,
                        "institution_desc" => $user->Institution->institution_desc,
                        "department_name"  => $user->Departments->department_name,
                        "department_desc"  => $user->Departments->department_desc
                    )
                );
                unset($arr);
            }
            $ret = array("result"=>"SUCCESS","users"=>$users_detail);
        }else{
            $ret = array("result"=>"FAIL","msg"=>$users->getMessages());
        }

        unset($users);
        unset($users_detail);
        $this->table->del($key);
        return $ret;

    }

}
