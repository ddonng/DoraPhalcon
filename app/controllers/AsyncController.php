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

    	$user->save();
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

    	$user->save();
    	// $arr = unserialize($ret);
    	// return array("heihei"=>$ret,"ret2"=>$ret2);
    }

}

