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

		
        $query = $this->modelsManager->createQuery("INSERT INTO User(name,department) VALUES(:name:,:department:)");
        $ret2 = $query->execute(array('name'=>$ret['param']['name'],'department'=>$ret['param']['department']));
      	
      	if ($ret2->success()==false)
      	{
	        echo "\r\nSorry, $api:::::::$guid \r\n";
	        var_export($ret);
	        foreach ($ret2->getMessages() as $message) {
	            echo "\r\n".$message->getMessage(), "\r\n";
	        }
	    }else{
	    	unset($query);
	    	unset($ret2);
	    }
	    
	    /***********************************************************/
/*
    	$user = new User();

    	$user->name = $ret['param']['name'];
    	$user->department = $ret['param']['department'];

    	$success = $user->save();
    	if ($success) {
            unset($user);
    		// echo "Thanks for registering!\r\n";
    	} else {
    		echo "Sorry, $api:::::::$guid \r\n";
    		var_export($ret);
    		foreach ($user->getMessages() as $message) {
    			echo "\r\n".$message->getMessage(), "\r\n";
    		}
    	}
    	*/

    	//just for test, export to server terminal console
        // if(!$ret2)
        // {
        //     var_export(array($api."-------".$guid,$ret2));
        //      echo "\r\n";
        // }
        
        $this->redis->hDel($api,$guid);

        return true;
    }

    public function updateUserAction($api,$guid)
    {
		$ret = unserialize($this->redis->hGet($api,$guid));
        
    	$user = new User();

    	$user->name = $ret['param']['name'];
    	$user->department = $ret['param']['department'];

    	$success =  $user->save();
    	if ($success) {
            unset($user);
    		// echo "Thanks for registering!\r\n";
    	} else {
    		echo "Sorry, $api:::::::$guid \r\n";
    		var_export($ret);
    		foreach ($user->getMessages() as $message) {
    			echo "\r\n".$message->getMessage(), "\r\n";
    		}
    	}

        //just for test, export to server terminal console
        if(!$ret)
        {
            var_export(array($api."-------".$guid,$ret));
             echo "\r\n";
        }
        
        $this->redis->hDel($api,$guid);

        return true;

    }

}

