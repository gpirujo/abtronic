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
    }

    // STATEMENTS

    public function choose($whens, $otherwise = null) {
        $that = $this;
        return function() use($that, $whens, $otherwise) {
            $that->_tag('choose', function() use($that, $whens, $otherwise) {
                foreach ($whens as $when) {
                    call_user_func($when);
                }
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
            if (is_callable($name)) $name = call_user_func($name);
            $that->_tag('assign', $expression, array('name' => $name));
        };
    }

    public function setcookie($name, $e) {
        return function() use($name, $e) {
            echo '$add_header(\'Set-Cookie\',\'' . $name . '=\'+$url_encode(' . call_user_func($e) . '))';
        };
    }

    // EXPRESSIONS

    public function literal($value) {
        return function() use($value) {
            if (is_string($value)) {
                return "'" . str_replace("'", "\\'", $value) . "'";
            } else {
                return json_encode($value);
            }
        };
    }

    public function variable($name) {
        return function() use($name) {
            if (is_callable($name)) $name = call_user_func($name);
            return '$(' . $name . ')';
        };
    }

    public function name($name, $e = null) {
        return function() use($name, $e) {
            $result = $name;
            if ($e) {
                $result .= '{' . call_user_func($e) . '}';
            }
            return $result;
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

    public function regex($e, $r) {
        return function() use($e, $r) {
            return call_user_func($e) . " matches '''" . $r . "'''";
        };
    }

}
