<?php

class Future_PHP extends Future {

    public $variables = array(); // TODO make this protected

    public function _eval($e) {
        return eval('return ' . call_user_func($e) . ';');
    }

    // STATEMENTS

    public function choose($whens, $otherwise = null) {
        return function() use($whens, $otherwise) {
            foreach ($whens as $when) {
                if (call_user_func($when)) return;
            }
            if ($otherwise) {
                call_user_func($otherwise);
            }
        };
    }

    public function when($expression, $action) {
        $that = $this;
        return function() use($expression, $action, $that) {
            if ($result = $that->_eval($expression)) {
                call_user_func($action);
            }
            return $result;
        };
    }

    public function assign($name, $expression) {
        $that = $this;
        return function() use($that, $name, $expression) {
            $value = $that->_eval($expression);
            if (preg_match('/(\w+)\{(\w+)\}/', $name, $matches)) {
                list($_, $name, $sub) = $matches;
                $that->variables[$name][$sub] = $value;
            } else {
                $that->variables[$name] = $value;
            }
        };
    }

    // EXPRESSIONS

    public function literal($value) {
        return function() use($value) {
            return var_export($value, true);
        };
    }

    public function variable($name, $sub = null) {
        return function() use($name, $sub) {
            $result = '$this->variables[' . var_export($name, true) . ']';
            if (!is_null($sub)) {
                $result .= '[' . call_user_func($sub) . ']';
            }
            return $result;
        };
    }

}
