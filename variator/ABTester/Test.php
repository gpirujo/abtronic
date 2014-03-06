<?php

class ABTester_Test {

    protected $tester;
    protected $name;

    public function __construct($tester, $name) {
        $this->tester = $tester;
        $this->name = $name;
    }

    public function willRender($option_names) {
        return $this->tester->willRender($this->name, $option_names);
    }

    public function outputIf($option_names, $then, $else = null) {
        return $this->tester->outputIf($this->name, $option_names, $then, $else);
    }

    public function outputIfNot($option_names, $then) {
        return $this->tester->outputIfNot($this->name, $option_names, $then, $else);
    }

    public function redirectIf($option_names, $url) {
        return $this->tester->redirectIf($this->name, $option_names, $url);
    }

}
