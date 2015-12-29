<?php

class IndexController extends ControllerBase
{

    public function indexAction($guid,$tid)
    {
    	$yac = new Yac();
    	$ret = $yac->get($guid);
    	sleep(2);
    	$ret2 = $yac->get($guid);
    	// Q: Does ttl expired key flushed or not?

    	// $arr = unserialize($ret);
    	return array("heihei"=>$ret,"ret2"=>$ret2);
    }

}

