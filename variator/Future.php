<?php

abstract class Future {

    // STATEMENTS

    abstract public function assign($name, $expression);
    abstract public function choose($whens, $otherwise);
    abstract public function when($expression, $action);

    public function block() {
        foreach (func_get_args() as $function) {
            call_user_func($function);
        }
    }

    public function _elseif($test, $then, $else = null) {
        $that = $this;
        return function() use ($that, $test, $then, $else) {
            $that->_if($test, $then, $else);
        };
    }

    // EXPRESSIONS

    abstract public function literal($value);
    abstract public function variable($name, $sub = null);

    public function _not($e) {
        return function() use($e) {
            return '!' . call_user_func($e);
        };
    }

    protected function _buildBinaryOp($op, $a, $b) {
        return function() use($op, $a, $b) {
            return call_user_func($a) . $op . call_user_func($b);
        };
    }

    public function eq($a, $b) {
        return $this->_buildBinaryOp('==', $a, $b);
    }

    public function ne($a, $b) {
        return $this->_buildBinaryOp('!=', $a, $b);
    }

    public function gt($a, $b) {
        return $this->_buildBinaryOp('>', $a, $b);
    }

    public function gte($a, $b) {
        return $this->_buildBinaryOp('>=', $a, $b);
    }

    public function lt($a, $b) {
        return $this->_buildBinaryOp('<', $a, $b);
    }

    public function lte($a, $b) {
        return $this->_buildBinaryOp('<=', $a, $b);
    }

    protected function _buildMultipleOp($op, $args) {
        return function() use($op, $args) {
            return '(' . implode($op, array_map('call_user_func', $args)) . ')';
        };
    }

    public function mul() {
        return $this->_buildMultipleOp('*', func_get_args());
    }

    public function div() {
        return $this->_buildMultipleOp('/', func_get_args());
    }

    public function mod() {
        return $this->_buildMultipleOp('%', func_get_args());
    }

    public function add() {
        return $this->_buildMultipleOp('+', func_get_args());
    }

    public function sub() {
        return $this->_buildMultipleOp('-', func_get_args());
    }

    public function _and() {
        return $this->_buildMultipleOp('&&', func_get_args());
    }

    public function _or() {
        return $this->_buildMultipleOp('||', func_get_args());
    }

}
