<?php

class IndexController extends ControllerBase
{

    public function indexAction($pid,$tid)
    {
    	return array("heihei"=>$pid.$tid);
    }

}

