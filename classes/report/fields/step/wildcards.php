<?php

namespace autodeploy\report\fields\step;

use
    autodeploy,
    autodeploy\definitions\php\observable,
    autodeploy\php\locale,
    autodeploy\report\field
;

abstract class wildcards extends field
{

    protected $iterator = null;

    public function __construct(locale $locale = null)
    {
        parent::__construct(array(autodeploy\steps\generate::runStop), $locale);
    }

    public function handleEvent($event, observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false)
        {
            return false;
        }
        else
        {
            //$this->iterator = $observable->getRunner()->getTasksIterator();
            $this->iterator = $observable->getRunner()->getIterator()->end()->getChildren();

            return true;
        }
    }

    public function getIterator()
    {
        return $this->iterator;
    }

}
