<?php

class ABTronic {

    const RESOLUTION = 30;
    const COOKIE = 'die';
    const CONTROL = '__CONTROL__';

    static function run($experiments, $body) {

        ?><esi:choose><?php
            ?><esi:when test="$(HTTP_COOKIE{'<?= self::COOKIE ?>'}) matches '''^([0-9]{1,10})$'''"><?php
                ?><esi:assign name="die" value="$int($(MATCHES{1}))"/><?php
            ?></esi:when><?php
            ?><esi:otherwise><?php
                ?><esi:assign name="die" value="$rand(<?= pow(2, self::RESOLUTION) ?>)"/><?php
                ?>$add_header('Set-Cookie', '<?= self::COOKIE ?>=' + $(die))<?php
            ?></esi:otherwise><?php
        ?></esi:choose><?php

        foreach ($experiments as $experiment => $definition) {
            ?><esi:assign name="x" value="$(die)"/><?php
            ?><esi:choose><?php

                $max = pow(2, self::RESOLUTION) * $definition['affects'];
                $cummulative = 0;
                foreach ($definition['options'] as $opt_name => $opt_val) {
                    $cummulative += $opt_val;
                    ?><esi:when test="$(x)<=<?= round($max * $cummulative) ?>"><?php
                        ?><esi:assign name="y" value="'<?= htmlspecialchars($opt_name) ?>'"/><?php
                    ?></esi:when><?php
                }

                ?><esi:otherwise><?php
                    ?><esi:assign name="y" value="'<?= self::CONTROL ?>'"/><?php
                ?></esi:otherwise><?php
            ?></esi:choose><?php
            ?><esi:assign name="experiments{'<?= htmlspecialchars($experiment) ?>'}" value="$(y)"/><?php
            ?><esi:assign name="active{'<?= htmlspecialchars($experiment) ?>'}" value="$(x)<=<?= floor($max) ?>"/><?php
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
