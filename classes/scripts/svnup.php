<?php

namespace autodeploy\scripts;

use autodeploy;
use autodeploy\step;
use autodeploy\factories;
use autodeploy\php\arguments\parser;

final class svnup extends autodeploy\script
{

    protected function setArgumentHandlers()
    {
        $runner = $this->getRunner();

        $this->addArgumentHandler(
            function($script, $argument, $values) use ($runner)
            {
                foreach ($values as $value)
                {
                    $runner->getIterator()->getChildren()->append( $value );
                }
            },
            array(''),
            parser::TYPE_MULTIPLE,
            parser::MANDATORY,
            'file',
            'Files to update'
        );

        return $this;
    }

    protected function setStepHandlers()
    {
        $this->getRunner()
            ->addStep(step::STEP_INVOKE, array(
                function ($runner)
                {
                    $runner->setProfile(new autodeploy\profiles\basic());
                },
            ))
            ->addStep(step::STEP_TRANSFORM, array(
                function ($runner)
                {
                    return factories\profile\transformer::instance(step::defaultFactory)->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_FILTER, array(
                function ($runner)
                {
                    return factories\profile\filter::instance(step::defaultFactory)->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_PARSE, array(
                function ($runner)
                {
                    return factories\profile\parser::instance(step::defaultFactory)->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_INVOKE, array(
                function ($runner)
                {
                    $runner->setProfile(new autodeploy\profiles\svn());
                },
            ))
            ->addStep(step::STEP_GENERATE, array(
                function ($runner, $task)
                {
                    return factories\profile\generator::instance($runner->getProfile()->getName(), 'up')->with($runner, $task['value'])->make();
                },
            ))
            ->addStep(step::STEP_EXECUTE, array(
                function ($runner, $action)
                {
                    return factories\task::instance(str_replace('_', '\\', $action['type']), $action['parser'])
                        ->with($runner, $action['command'], $action['wildcard'])
                        ->make()
                    ;
                },
            ))
            ->addStep(step::STEP_INVOKE, array(
                function ($runner)
                {
                    $profile = new autodeploy\profiles\ezpublish();
                    $profile->setOrigin('svn');
                    $runner->setProfile($profile);
                },
            ))
            ->addStep(step::STEP_TRANSFORM, array(
                function ($runner)
                {
                    if (substr( php_uname(), 0, 7 ) == "Windows")
                    {
                        $output = "A    extension/labackoffice/settings/site.ini.append.php\n";
                        $output .= "A    design/deco/templates/page_mainarea.tpl\n";
                        $output .= "A    extension/labackoffice/settings/override.ini.append.php\n";
                        $output .= "A    bin/toto.php\n";
                        $output .= "U    extension/labackoffice/classes/toto.php\n";
                        $output .= "U    extension/labackoffice/settings/design.ini.append.php";

                        $iterator = $runner->getIterator()->end()->getChildren();

                        foreach (explode("\n", $output) as $s)
                        {
                            $iterator->append($s);
                        }
                    }

                    return factories\profile\transformer::instance($runner->getProfile()->getOrigin())->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_FILTER, array(
                function ($runner)
                {
                    return factories\profile\filter::instance($runner->getProfile()->getOrigin())->with($runner)->make();
                },
                function ($runner)
                {
                    return factories\profile\filter::instance($runner->getProfile()->getName())->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_PARSE, array(
                function ($runner, $parser)
                {
                    return factories\profile\parser::instance(
                        $runner->getProfile()->getName(),
                        $parser,
                        $runner->getProfile()->getOrigin()
                    )->with($runner)->make();
                },
            ))
            ->addStep(step::STEP_GENERATE, array(
                function ($runner, $task)
                {
                    return factories\profile\generator::instance(
                        $runner->getProfile()->getName(),
                        $task['parser']
                    )->with($runner, $task['value'])->make();
                },
            ))
            ->addStep(step::STEP_EXECUTE, array(
                function ($runner, $action)
                {
                    return factories\task::instance(
                        str_replace('_', '\\', $action['type']),
                        $action['parser']
                    )
                        ->with($runner, $action['command'], $action['wildcard'])->make();
                },
            ))
        ;
    }

}
