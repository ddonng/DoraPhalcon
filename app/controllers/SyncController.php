<?php

class SyncController extends ControllerBase
{

    public function indexAction($yacPrefix,$key)
    {
    	$yac = new Yac($yacPrefix);
    	$ret = $yac->get($key);
    	// Q: Does ttl expired key flushed or not?
    	$ret2 = User::find()->toArray();
    	// $arr = unserialize($ret);
    	return array("heihei"=>$ret,"ret2"=>$ret2);
    }

}

