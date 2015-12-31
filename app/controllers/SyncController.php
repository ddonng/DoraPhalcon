<?php

class SyncController extends Phalcon\Mvc\Controller
{

    public function indexAction($api,$guid)
    {
    	$yac = new Yac($yacPrefix);
    	$ret = $yac->get($key);
    	// Q: Does ttl expired key flushed or not?
    	$ret2 = User::find()->toArray();
    	// $arr = unserialize($ret);

    	if(!$ret)
        {
            var_export(array($key,$ret));
            echo "\r\n";
        }
    	$yac->delete($key);
    	return array("heihei"=>$ret2);
    }

}

