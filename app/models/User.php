<?php

use Phalcon\Mvc\Model\Validator\Email as Email;

class User extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $passwd;

    /**
     *
     * @var string
     */
    public $sex;

    /**
     *
     * @var integer
     */
    public $institution_id;

    /**
     *
     * @var integer
     */
    public $deartment_id;

    /**
     *
     * @var string
     */
    public $professional_title;

    /**
     *
     * @var string
     */
    public $diploma;

    /**
     *
     * @var string
     */
    public $degree;

    /**
     *
     * @var string
     */
    public $nation;

    /**
     *
     * @var string
     */
    public $native_place;

    /**
     *
     * @var string
     */
    public $stuff_id;

    /**
     *
     * @var string
     */
    public $qq;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $mobile_phone;

    /**
     *
     * @var string
     */
    public $office_phone;

    /**
     *
     * @var integer
     */
    public $register_time;

    /**
     *
     * @var string
     */
    public $login_q;

    /**
     *
     * @var string
     */
    public $login_phone;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        //若非空，则需要是email格式
        if( !is_null($this->email) && isset($this->email) ){
            $this->validate(
                new Email(
                    array(
                        'field'    => 'email',
                        'required' => true,
                    )
                )
            );
        }


        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('deartment_id', 'Departments', 'department_id', array('alias' => 'Departments'));
        $this->belongsTo('institution_id', 'Institution', 'institution_id', array('alias' => 'Institution'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
