<?php

class Departments extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $department_id;

    /**
     *
     * @var string
     */
    public $department_name;

    /**
     *
     * @var string
     */
    public $department_desc;

    /**
     *
     * @var integer
     */
    public $institution_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('department_id', 'User', 'deartment_id', array('alias' => 'User'));
        $this->belongsTo('institution_id', 'Institution', 'institution_id', array('alias' => 'Institution'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'departments';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Departments[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Departments
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
