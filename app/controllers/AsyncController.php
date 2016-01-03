<?php

class AsyncController extends ControllerBase
{

    public function addUserAction($api,$guid)
    {
        $ret = unserialize($this->redis->hGet($api,$guid));

        /*********************************/
        // When called with updateUserAction together, large request eg 100000, return some error say "Record cannot be created because it already exists" but record finally insert failed
        // When called alone, success
        // 

        try{
            // $this->db->connect();
            $statement = $this->modelsManager->createQuery("INSERT INTO User(name,department) VALUES(:name:,:department:)");
            $result = $statement->execute(
                    array('name'=>$ret['param']['name'],'department'=>$ret['param']['department'])
                );

            $this->redis->hDel($api,$guid);

            if ($result->success() == false) {
                foreach ($result->getMessages() as $message) {
                    echo '\r\n' .$message->getMessage().'\r\n' ;
                }
            }else{
                // $this->db->close();
                unset($statement);
                // $this->db->close();
                return 'done';
            }
           

        } catch (\Exception $e) {
                echo $e->getMessage() . '\r\n';
                echo '\r\n' . $e->getTraceAsString() . '\r\n';
        }


    }

    //test good,connection_num = task_work_num 140
    public function updateUserAction($api,$guid)
    {
        $ret = unserialize($this->redis->hGet($api,$guid));
        //just for test, export to server terminal console
        if(!$ret)
        {
            var_export(array($api."-------".$guid,$ret));
             echo "\r\n";
        }

        $user = new User();

        $user->name = $ret['param']['name'];
        $user->department = $ret['param']['department'];

        $success =  $user->save();
        $this->redis->hDel($api,$guid);

        if ($success) {
            unset($user);
            // echo "Thanks for registering!\r\n";
            return 'success';
        } else {
            echo "Sorry, $api:::::::$guid \r\n";
            var_export($ret);
            foreach ($user->getMessages() as $message) {
                echo "\r\n".$message->getMessage(), "\r\n";
            }
        }

    }

    // very good connection num very low
    public function insertUserAction($api,$guid)
    {
        $ret = unserialize($this->redis->hGet($api,$guid));
        if(!$ret)
        {
            var_export(array($api."-------".$guid,$ret));
            echo "\r\n";
        }
        try{
            
            $statement = $this->db->prepare("INSERT INTO user(name,department) VALUES (:name,:department)");
            $result = $statement->execute(
                array('name'=>$ret['param']['name'],'department'=>$ret['param']['department'])
            );
            // var_export($result);
            if ($result == false) {
                foreach ($result->getMessages() as $message) {
                    echo '\r\n' .$message->getMessage().'\r\n' ;
                }
            }else{
                unset($statement);
                return 'work!';
            }
            // 
        }catch (\Exception $e) {
                echo $e->getMessage() . '\r\n';
                echo '\r\n' . $e->getTraceAsString() . '\r\n';
        }
    }

    //test good, connection_num = task_work_num 140
    public function plusUserAction($api,$guid)
    {
        $ret = unserialize($this->redis->hGet($api,$guid));
        try{
           
            $result = $this->db->execute("INSERT INTO user(name,department) VALUES ('".$ret['param']['name']."','".$ret['param']['department']."')");
            // var_export($statement);
            if ($result== false) {
                foreach ($result->getMessages() as $message) {
                    echo '\r\n' .$message->getMessage().'\r\n' ;
                }
            }else{
                return 'chenggong!';
            }
        }catch (\Exception $e) {
                echo $e->getMessage() . '\r\n';
                echo '\r\n' . $e->getTraceAsString() . '\r\n';
        }
    }

}

