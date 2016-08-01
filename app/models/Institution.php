<?php

class Institution extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $institution_id;

    /**
     *
     * @var string
     */
    public $institution_name;

    /**
     *
     * @var string
     */
    public $institution_desc;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('institution_id', 'Departments', 'institution_id', array('alias' => 'Departments'));
        $this->hasMany('institution_id', 'User', 'institution_id', array('alias' => 'User'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'institution';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Institution[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Institution
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
