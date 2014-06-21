<?php

class Future_JS extends Future {

    public function docwrite($content) {
        ob_start();
        call_user_func($content);
        return sprintf('document.write(%s);', json_encode(ob_get_clean()));
    }

    // STATEMENTS

    public function choose($whens, $otherwise = null) {
        $that = $this;
        return function() use($that, $whens, $otherwise) {
            if ($when = array_shift($whens)) {
                call_user_func($when);
                foreach ($whens as $when) {
                    echo 'el';
                    call_user_func($when);
                }
                if ($otherwise) {
                    printf('else{%s}', $that->docwrite($otherwise));
                }
            } else if ($otherwise) {
                echo $that->docwrite($otherwise);
            }
        };
    }

    public function when($expression, $action) {
        $that = $this;
        return function() use($that, $expression, $action) {
            printf('if(%s){%s}', call_user_func($expression), $that->docwrite($action));            
        };
    }

    public function assign($name, $expression) {
        return function() use($name, $expression) {
            printf('%s=%s;', $name, call_user_func($expression));
        };
    }

    // EXPRESSIONS

    public function literal($value) {
        return function() use($value) {
            return json_encode($value);
        };
    }

    public function variable($name, $sub = null) {
        return function() use($name, $sub) {
            if ($sub) {
                $name .= '[' . call_user_func($sub) . ']';
            }
            return $name;
        };
    }

    public function int($e) {
        return function() use($e) {
            return 'parseInt(' . call_user_func($e) . ')';
        };
    }

    public function rand($e) {
        return function() use($e) {
            return 'Math.random()*' . call_user_func($e);
        };
    }

}
