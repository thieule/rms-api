<?php
namespace App\Api\V1\Notification\Services;

/* 
 * ProviderAbstract
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */
abstract class ProviderAbstract
{

    protected $_condition_data;

    abstract protected function getResource();

    abstract protected function getParam();


    private $_result;

    /**
     * Get logic list
     * @return array
     */
    public static function getLogicBase()
    {
        return [
            '=' => 'equal',
            '>' => 'bigger',
            '<' => 'less',
            '>=' => 'biggerOrEqual',
            '<=' => 'lessOrEqual'
        ];
    }


    /**
     * @return bool
     */
    public function check()
    {
        $logic_func = $this->getFuncLogic();
        return $this->{$logic_func}();
        return false;
    }

    /**
     * @return null
     */
    public function getFuncLogic()
    {
        $list_logic = $this->getLogicList();
        if (isset($list_logic[$this->_condition_data->logic]['logic_func'])) {
            return $list_logic[$this->_condition_data->logic]['logic_func'];
        }
        return null;
    }

    /**
     * @return null
     */
    public function getFuncMsg()
    {
        $list_logic = $this->getLogicList();
        if (isset($list_logic[$this->_condition_data->logic]['msg_func'])) {
            return $list_logic[$this->_condition_data->logic]['msg_func'];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getLogicList()
    {
        $class = get_called_class();
        $list_event = $class::getEventConfig();
        if (isset($list_event[$this->_condition_data->event]['logicList'])) {
            return $list_event[$this->_condition_data->event]['logicList'];
        }

        return [];
    }

    public function setResultData($result)
    {
        $this->_result = $result;
    }

    public function getResultData()
    {
        return $this->_result ? $this->_result : null;
    }

    /**
     * If equal
     *
     * @return mixed
     */
    public function equal()
    {
        return ($this->getResource() == $this->getParam());
    }

    /**
     * @return bool
     */
    public function bigger()
    {
        return ($this->getResource() > $this->getParam());
    }

    /**
     * @return bool
     */
    public function biggerOrEqual()
    {
        return ($this->getResource() >= $this->getParam());
    }

    /**
     * @return bool
     */
    public function less()
    {
        return ($this->getResource() < $this->getParam());
    }

    /**
     * @return bool
     */
    public function lessOrEqual()
    {
        return ($this->getResource() < $this->getParam());
    }

    /**
     * @return bool
     */
    public function in()
    {
        if (!empty($this->param)) {
            return false;
        }

        if (!is_array($this->getParam())) {
            return $this->getResource() == $this->getParam();
        }

        return in_array($this->getResource(), $this->getParam());
    }

    public function notIn()
    {
        return (!$this->in());
    }

    /**
     * @return bool
     */
    public function contains()
    {
        if (!empty($this->getParam())) {
            return false;
        }

        if (is_array($this->getParam())) {
            return in_array($this->getResource(), $this->getParam());
        }


        if (strpos($this->getResource(), $this->getParam()) === false) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function notContains()
    {
        return (!$this->contains());
    }

    /**
     * @return bool
     */
    public function preg()
    {
        if (!empty($this->getParam()) || !empty($this->getResource())) {
            return false;
        }

        if (!preg_match($this->getParam(), $this->getResource())) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function notPreg()
    {
        return $this->preg();
    }


}
