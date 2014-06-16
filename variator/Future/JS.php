<?php

class Future_JS extends Future {

    protected function docwrite($content) {
        ob_start();
        call_user_func($content);
        return sprintf('document.write(%s)', json_encode(ob_get_clean()));
    }

    // STATEMENTS

    public function choose($whens, $otherwise = null) {
        return function() use($whens, $otherwise) {
            printf('if(0){}');
            foreach ($whens as $when) call_user_func($when);
            if ($otherwise) {
                printf('else{%s}', call_user_func($otherwise));
            }
        };
    }

    public function when($expression, $action) {
        return function() use($expression, $action) {
            printf('elif(%s){%s}', call_user_func($expression), call_user_func($action));            
        };
    }

    public function assign($name, $expression) {
        return function() use($name, $expression) {
            printf('%s=%s;', $name, call_user_func($expression));
        };
    }

    public function _if($test, $then, $else = null) {
        printf('if(%s){%s}', call_user_func($test), $this->docwrite($then));
        if ($else) {
            printf('else{%s}', $this->docwrite($else));
        }
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

}
