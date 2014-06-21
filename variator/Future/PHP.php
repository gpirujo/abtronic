<?php

class Future_PHP extends Future {

    public $variables = array();

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
        return function() use($expression, $action) {
            if ($result = call_user_func($expression)) {
                call_user_func($action);
            }
            return $result;
        };
    }

    public function assign($name, $expression) {
        $that = $this;
        return function() use($that, $name, $expression) {
            $value = call_user_func($expression);
            if (preg_match('/(\w+)\{(\w+)\}/', $name, $matches)) {
                list($name, $sub) = array_slice($matches, 1, 2);
                $that->variables[$name][$sub] = $value;
            } else {
                $that->variables[$name] = $value;
            }
        };
    }

    // EXPRESSIONS

    public function literal($value) {
        return function() use($value) {
            return $value;
        };
    }

    public function variable($name, $sub = null) {
        $that = $this;
        return function() use($that, $name, $sub) {
            $result = $that->variables[$name];
            if (!is_null($sub)) {
                $result = $result[$sub];
            }
            return $result;
        };
    }

    protected function _buildBinaryOp($op, $a, $b) {
        return function() use($op, $a, $b) {
            return call_user_func($op, call_user_func($a), call_user_func($b));
        };
    }

    public function eq($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a == $b;
        }, $a, $b);
    }

    public function ne($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a != $b;
        }, $a, $b);
    }

    public function gt($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a > $b;
        }, $a, $b);
    }

    public function gte($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a >= $b;
        }, $a, $b);
    }

    public function lt($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a < $b;
        }, $a, $b);
    }

    public function lte($a, $b) {
        return $this->_buildBinaryOp(function($a, $b) {
            return $a <= $b;
        }, $a, $b);
    }

    protected function _buildMultipleOp($op, $args) {
        return function() use($op, $args) {
            $result = call_user_func(array_shift($args));
            while ($arg = array_shift($args)) {
                $result = call_user_func($op, $result, call_user_func($arg));
            }
            return $result;
        };
    }

    public function mul() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r * $x;
        }, func_get_args());
    }

    public function div() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r / $x;
        }, func_get_args());
    }

    public function mod() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r % $x;
        }, func_get_args());
    }

    public function add() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r + $x;
        }, func_get_args());
    }

    public function sub() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r - $x;
        }, func_get_args());
    }

    public function _and() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r && $x;
        }, func_get_args());
    }

    public function _or() {
        return $this->_buildMultipleOp(function($r, $x) {
            return $r || $x;
        }, func_get_args());
    }

    public function int($e) {
        return function() use($e) {
            return (int) call_user_func($e);
        };
    }

    public function rand($e) {
        return function() use($e) {
            return rand(0, call_user_func($e) - 1);
        };
    }

}
