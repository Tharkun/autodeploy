<?php

namespace autodeploy\profiles\ezpublish\parsers;

use
    autodeploy
;

class designBase extends autodeploy\parser
{

    protected static $singleton = null;


    /*****************************************************************************************************************************/
    /*****************************************************************************************************************************/
    /*****************************************************************************************************************************/


    const PATTERN = '([^/]+)?';


    /*****************************************************************************************************************************/
    /*****************************************************************************************************************************/
    /*****************************************************************************************************************************/


    public function hasMatches(autodeploy\element $element, array & $matches = null, & $i = null)
    {
        return preg_match('@extension/[^/]+/design$@', $element->name, $matches) && !is_null($i = 0)
            || preg_match('@extension/[^/]+/design/'.self::PATTERN.'$@', $element->name, $matches) && !is_null($i = 1)
        ;
    }

    public function getTaskType()
    {
        return autodeploy\tasks\delete\file::TYPE;
    }

}
