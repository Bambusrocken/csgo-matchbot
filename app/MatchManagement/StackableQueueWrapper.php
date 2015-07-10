<?php

namespace Bot\MatchManagement;

class StackableQueueWrapper
{
    private $_stackable;

    public function __construct($stackable)
    {
        $this->_stackable = $stackable;
    }

    public function push($element)
    {
        $this->_stackable[] = $element;
    }

    public function pop()
    {
        if(!isset($this->_stackable[key($this->_stackable)]))
            return false;

        $data = reset($this->_stackable);
        unset($this->_stackable[key($this->_stackable)]);
        return $data;
    }

    //TODO: Method to delete underlying stackable object to prevent possible memory leaks
}