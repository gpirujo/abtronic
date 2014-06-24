<?php

class ESI {

    // content level interface

    public function start($f) {
        header('Edge-Control: dca=esi');
        call_user_func_array(array($this, 'out'), func_get_args());
    }

    public function in($f) {
        $this->_tag1('text', array(), false, $f, array_slice(func_get_args(), 1));
    }

    public function out($f) {
        $this->_tag1('text', array(), true, $f, array_slice(func_get_args(), 1));
    }

    // high level esi interface

    public function assign($name, $value) {
        $this->tag('assign', array(
            'name' => $name,
            'value' => $value,
        ));
    }

    public function choose($f) {
        $this->tag('choose', array(), $f, array_slice(func_get_args(), 1));
    }

    public function when($test, $f) {
        $this->tag('when', array(
            'test' => $test
        ), $f, array_slice(func_get_args(), 2));
    }

    public function otherwise($f) {
        $this->tag('otherwise', array(), $f, array_slice(func_get_args(), 1));
    }

    public function for_each($collection, $f) {
        $this->tag('foreach', array(
            'collection' => $collection
        ), $f, array_slice(func_get_args(), 2));
    }

    public function vars($expr) {
        $this->tag('vars', array(), function($esi) use($expr) {
            echo $expr;
        });
    }

    public function set_cookie($name, $value) {
        $this->func('add_header', '\'Set-Cookie\'', sprintf('\'%s=%s\'', $name, $value));
    }

    // low level esi interface

    public function tag($tag, $attr, $f = null) {
        $this->_tag1($tag, $attr, true, $f, array_slice(func_get_args(), 3));
    }

    public function func($name) {
        echo '$' . $name . '(' . join(',', array_slice(func_get_args(), 1)) . ')';
    }

    // dirty work

    private function _tag1($tag, $attr, $open, $f, $args) {
        $this->_tag2($open, !$f, $tag, $attr);
        if ($f) {
            try {
                call_user_func_array($f, array_merge(array($this), $args));
            } catch (Exception $e) {
                $exception = $e;
            }
            $this->_tag2(!$open, false, $tag, $attr);
            if (isset($exception)) throw $exception;
        }
    }

    private function _tag2($open, $close, $tag, $attr) {
        echo '<';
        if (!$open) {
            echo '/';
        }
        echo 'esi:' . $tag;
        if ($open) {
            foreach (((array)@$attr) as $key => $value) {
                printf(' %s="%s"', $key, $value);
            }
        }
        if ($close) {
            echo '/';
        }
        echo '>';
    }

}
