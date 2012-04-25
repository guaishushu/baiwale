<?php

class Subject extends ArrayObject implements SplSubject
{
    private $storage = null;

    function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    public function attach(SplObserver $observer)
    {
        $this->storage->attach($observer);
    }

    public function detach(SplObserver $observer)
    {
        $this->storage->detach($observer);
    }

    public function notify()
    {
        foreach ($this->storage as $obj) {
            $obj->update($this);
        }
    }
}

?>