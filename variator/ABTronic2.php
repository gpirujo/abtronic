<?php

require 'Future.php';
require 'Future/ESI.php';

class ABTronic {

    const RESOLUTION = 30;
    const COOKIE = 'die';
    const CONTROL = '__CONTROL__';

    static function run($experiments, $body) {

        $f = new Future_ESI;

        call_user_func($f->choose(array(
            $f->when($f->regex(
                $f->variable($f->name('HTTP_COOKIE', $f->literal(self::COOKIE))),
                '^([0-9]{1,10})$'
            ), $f->assign(
                'die',
                $f->int(
                    $f->variable($f->name('MATCHES', $f->literal(1)))
                )
            ))
        ), $f->block(array(
            $f->assign(
                'die',
                $f->rand(
                    $f->literal(pow(2, self::RESOLUTION))
                )
            ),
            $f->setcookie(
                self::COOKIE,
                $f->variable('die')
            )
        ))));

        foreach ($experiments as $experiment => $definition) {
            $whens = array();
            $max = pow(2, self::RESOLUTION) * $definition['affects'];
            $cummulative = 0;
            foreach ($definition['options'] as $opt_name => $opt_val) {
                $cummulative += $opt_val;
                $whens[] = $f->when(
                    $f->lte(
                        $f->variable('x'),
                        $f->literal(round($max * $cummulative))
                    ),
                    $f->assign('y', $f->literal($opt_name))
                );
            }

            call_user_func($f->block(array(
                $f->assign('x', $f->variable('die')),
                $f->choose(
                    $whens,
                    $f->assign('y', $f->literal(self::CONTROL))
                ),
                $f->assign($f->name('experiments', $f->literal($experiment)), $f->variable('y')),
                $f->assign($f->name('active', $f->literal($experiment)), $f->lte($f->variable('x'), $f->literal(floor($max))))
            )));
        }

        ?><esi:text><?php
        try {
            call_user_func($body, new static($experiments));
        } catch (Exception $e) {
            $exception = $e;
        }
        ?></esi:text><?php
        if (isset($exception)) throw $exception;

    }

    private $experiments;

    function __construct($experiments) {
        $this->experiments = $experiments;
    }

    function experiments() {
        return array_keys($this->experiments);
    }

    function variation($experiment) {
        ?></esi:text><esi:vars><?php
            ?>$(experiments{'<?= htmlspecialchars($experiment) ?>'})<?php
        ?></esi:vars><esi:text><?php
    }

    function variate($experiment, $options, $then, $else = null) {

        if (!is_array($options)) {
            $options = array($options);
        }

        $clauses = array();
        foreach ($options as $option) {
            $clauses[] = sprintf('$(experiments{\'%s\'})==\'%s\'', htmlspecialchars($experiment), htmlspecialchars($option));
        }

        ?></esi:text><esi:choose><?php
        try {
            if ($then) {
                ?><esi:when test="<?= join('||', $clauses) ?>"><esi:text><?php
                try {
                    call_user_func($then);
                } catch (Exception $e) {
                    $exception = $e;
                }
                ?></esi:text></esi:when><?php
                if (isset($exception)) throw $exception;
            }
            if ($else) {
                ?><esi:otherwise><esi:text><?php
                try {
                    call_user_func($else);
                } catch (Exception $e) {
                    $exception = $e;
                }
                ?></esi:text></esi:otherwise><?php
                if (isset($exception)) throw $exception;
            }
        } catch (Exception $e) {
            $exception = $e;
        }
        ?></esi:choose><esi:text><?php
        if (isset($exception)) throw $exception;

    }

}
