<?php

class AsyncController extends ControllerBase
{

    public function addUserAction($yacPrefix,$key)
    {
    	$yac = new Yac($yacPrefix);
    	$ret = $yac->get($key);

        $phql = "INSERT INTO User(name,department) VALUES(:name:,:department:)";
        $ret2 = $this->modelsManager->executeQuery($phql,array('name'=>$ret['param']['name'],'department'=>$ret['param']['department']));
    	// $user = new User();

    	// $user->name = $ret['param']['name'];
    	// $user->department = $ret['param']['department'];

    	// $success =  $user->save();
    	// if ($success) {
     //        unset($user);
    	// 	echo "Thanks for registering!\r\n";
    	// } else {
    	// 	echo "Sorry, $yacPrefix:::::::$key \r\n";
    	// 	foreach ($user->getMessages() as $message) {
    	// 		echo $message->getMessage(), "\r\n";
    	// 	}
    	// }
        if($ret2)
        {
            var_export(array($key,$ret));
        }

        $yac->delete($key);
        return true;
    	// $arr = unserialize($ret);
    	// return array("heihei"=>$ret,"ret2"=>$ret2);
    }

    public function updateUserAction($yacPrefix,$key)
    {
    	$yac = new Yac($yacPrefix);
    	$ret = $yac->get($key);
    	// Q: Does ttl expired key flushed or not?
        
    	$user = new User();

    	$user->name = $ret['param']['name'];
    	$user->department = $ret['param']['department'];

    	$success =  $user->save();
        if ($success) {
            unset($user);
            // echo "Thanks for registering!\r\n";
        } else {
            echo "Sorry, $yacPrefix:::::::$key \r\n";
            foreach ($user->getMessages() as $message) {
                echo $message->getMessage(), "\r\n";
            }
        }
        if(!$ret)
        {
            var_export(array($key,$ret));
            echo "\r\n";
        }

        $yac->delete($key);
        return true;
    	// $arr = unserialize($ret);
    	// return array("heihei"=>$ret,"ret2"=>$ret2);
    }

}

