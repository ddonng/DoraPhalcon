<?php

class AsyncController extends ControllerBase
{

    public function addUserAction($yacPrefix,$key)
    {
    	$yac = new Yac($yacPrefix);
    	$ret = $yac->get($key);
    	// Q: Does ttl expired key flushed or not?
    	$user = new User();

    	$user->name = $ret['param']['name'];
    	$user->department = $ret['param']['department'];

    	$success =  $user->save();
    	if ($success) {
    		echo "Thanks for registering!\r\n";
    	} else {
    		echo "Sorry, the following problems were generated: ";
    		foreach ($user->getMessages() as $message) {
    			echo $message->getMessage(), "<br/>";
    		}
    	}
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
    		echo "Thanks for registering!";
    	} else {
    		echo "Sorry, the following problems were generated: ";
    		foreach ($user->getMessages() as $message) {
    			echo $message->getMessage(), "<br/>";
    		}
    	}
    	// $arr = unserialize($ret);
    	// return array("heihei"=>$ret,"ret2"=>$ret2);
    }

}

