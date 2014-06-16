<?php

class Future_ESI extends Future {

    public function _tag($tag, $content = null, $args = array()) {
        echo '<esi:' . $tag;
        foreach ($args as $key => $value) {
            echo ' ' . $key . '="' . $value . '"';
        }
        if ($content) {
            echo '>';
            echo call_user_func($content);
            echo '</esi:' . $tag . '>';
        } else {
            echo '/>';
        }
        echo "\n";
    }

    // STATEMENTS

    public function choose($whens, $otherwise = null) {
        $that = $this;
        return function() use($that, $whens, $otherwise) {
            $that->_tag('choose', function() use($that, $whens, $otherwise) {
                foreach ($whens as $when) call_user_func($when);
                if ($otherwise) {
                    $that->_tag('otherwise', $otherwise);
                }
            });
        };
    }

    public function when($expression, $action) {
        $that = $this;
        return function() use($that, $expression, $action) {
            $that->_tag('when', $action, array(
                'test' => call_user_func($expression)
            ));
        };
    }

    public function assign($name, $expression) {
        $that = $this;
        return function() use($that, $name, $expression) {
            $that->_tag('assign', $expression, array('name' => $name));
        };
    }

    public function _if($test, $then, $else = null) {
        static $seq = 0;
        $that = $this;
        $var = 'esivar' . ++$seq;
        $this->assign($var, $test);
        $this->_tag('choose', function() use ($that, $var, $test, $then, $else) {
            $that->_tag('when', $then, array('test' => call_user_func($that->variable($var))));
            if ($else) {
                $that->_tag('otherwise', $else);
            }
        });
    }

    // EXPRESSIONS

    public function literal($value) {
        return function() use($value) {
            return json_encode($value);
        };
    }

    public function variable($name, $sub = null) {
        return function() use($name, $sub) {
            return '$(' . $name . (is_null($sub) ? '' : '{' . call_user_func($sub) . '}') . ')';
        };
    }

    public function int($e) {
        return function() use($e) {
            return '$int(' . call_user_func($e) . ')';
        };
    }

    public function rand($e) {
        return function() use($e) {
            return '$rand(' . call_user_func($e) . ')';
        };
    }

}
