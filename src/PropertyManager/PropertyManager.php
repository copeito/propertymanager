<?php
namespace copeito;

use \Exception as Exception;

class PropertyManager
{
    private $props = array();
    private $setted = array();
    private $values = array();

    use getInstance;

    public function __construct(array $args)
    {
        /*
            If $args contains pairs of index/values, addProps adds $args keys
            to managed properties
            If $args don't contains pairs of index/values, addProps adds all
            array as managed properties
         */
        $hasValues = (count(array_filter(array_keys($args), 'is_string')) > 0);

        $this->addProps(
            $hasValues ? array_keys($args) : $args
        );

        /*
            If $args contains pairs of index/values, PropertyManager initializes
            managed properties values
         */
        if ($hasValues){
            $this->set(
                $args
            );
        }
    }

    /**
     *    Add properties to array of controllable properties
     *    @method addProps
     *    @author David Rey
     *    @since  2018-06-10T17:21:54+020
     *    @param  array                   $props [description]
     *    @return PropertyManager                [description]
     */
    public function addProps(array $props) : PropertyManager
    {
        foreach($props as $prop){
            $this->addProp($prop);
        }

        return $this;
    }

    /**
     *    Add property to array of controllable properties
     *    @method addProp
     *    @author David Rey
     *    @since  2018-06-10T17:22:49+020
     *    @param  string                  $prop [description]
     *    @return PropertyManager               [description]
     */
    public function addProp(string $prop) : PropertyManager
    {
        $this->props[$prop] = true;
        $this->setted[$prop] = false;

        return $this;
    }

    /**
     *    Checks if given propery is controllable
     *    @method managed
     *    @author David Rey
     *    @since  2018-06-10T17:26:23+020
     *    @param  string
     *    @return bool                          [description]
     */
    public function managed(string $prop) : bool
    {
        return array_key_exists($prop, $this->props);
    }

    /**
     *    Magic method __call implementation
     *    @method __call
     *    @author David Rey
     *    @since  2018-06-19T11:11:18+020
     *    @param  string                  $method    [description]
     *    @param  array                   $arguments [description]
     *    @return mixed                             [description]
     */
    public function __call(string $method, array $arguments)
    {
        $return = null;

        switch($method){
            case 'set':
                if (is_array($arguments[0])){
                    $args = $arguments[0];
                }else{
                    $args = ([
                        $arguments[0] => $arguments[1]
                    ]);
                }

                return $this->set(
                    $args
                );
                break;
        }
    }

    /**
     *    Sets given properties values
     *    @method set
     *    @author David Rey
     *    @since  2018-06-10T17:27:02+020
     *    @param  array                   $args [description]
     *    @return PropertyManager               [description]
     */
    private function set(array $args) : PropertyManager
    {
        foreach($args as $param => $value){
            $this->setValue(
                $param,
                $value
            );
        }

        return $this;
    }

    /**
     *    Sets given property value
     *    @method setValue
     *    @author David Rey
     *    @since  2018-06-10T17:27:23+020
     *    @param  string                  $param [description]
     *    @param  [type]                  $value [description]
     *    @return PropertyManager                [description]
     */
    private function setValue(string $param, $value) : PropertyManager
    {
        if (!$this->managed($param)){
            throw new Exception('Property '.$param.' is not managed');
        }

        $this->setted[$param] = true;
        $this->values[$param] = $value;

        return $this;
    }

    /**
     *    Checks if given property is controllable
     *    @method setted
     *    @author David Rey
     *    @since  2018-06-10T17:27:57+020
     *    @param  string                  $param [description]
     *    @return bool                           [description]
     */
    public function setted(string $param) : bool
    {
        return ($this->setted[$param] ? true : false);
    }

    /**
     *    Return given property value
     *    @method get
     *    @author David Rey
     *    @since  2018-06-10T17:28:15+020
     *    @param  string                  $param [description]
     *    @return [type]                         [description]
     */
    public function get(string $param)
    {
        if (!$this->managed($param)){
            throw new Exception('Property '.$param.' is not managed');
        }

        return $this->values[$param];
    }
}
