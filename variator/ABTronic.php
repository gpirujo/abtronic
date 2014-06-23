<?php

class ABTronic {

    const DICE = 128;
    const COOKIE = 'die';
    const CONTROL = 'CONTROL';

    public static function run($experiments, $body) {
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
    private $dieThrown = false;
    private $activeExperiments = array();

    public function __construct($experiments) {
        $this->experiments = $experiments;
    }

    public function variate($experiment, $options, $then, $else = null) {

        if (!in_array($experiment, $this->activeExperiments)) {

            $crc = crc32($experiment);
            ksort($this->experiments[$experiment]);

            ?></esi:text><?php

            if (!$this->dieThrown) {
                ?><esi:choose><?php
                    ?><esi:when test="$(HTTP_COOKIE{'<?= self::COOKIE ?>'}) matches '''^[0-9]{<?= static::DICE * 2 ?>}$'''"><?php
                        ?><esi:assign name="die" value="$(MATCHES{0})"/><?php
                    ?></esi:when><?php
                    ?><esi:otherwise><?php
                        ?><esi:assign name="die" value="''"/><?php
                        ?><esi:foreach collection="[1..<?= static::DICE * 2 ?>]"><?php
                            ?><esi:assign name="die" value="$(die) + $str($rand(10))"/><?php
                        ?></esi:foreach><?php
                        ?>$add_header('Set-Cookie', '<?= self::COOKIE ?>=' + $(die))<?php
                    ?></esi:otherwise><?php
                ?></esi:choose><?php
                $this->dieThrown = true;
            }

            ?><esi:assign name="x" value="($int($substr($(die), <?= ($crc % static::DICE) * 2 ?>, 2)) + <?= $crc % 100 ?>) % 100"/><?php
            ?><esi:choose><?php

                foreach ($this->experiments[$experiment]['options'] as $opt_name => $opt_val) {
                    $cummulative += $opt_val;
                    ?><esi:when test="$(x)<=<?= $cummulative ?>"><?php
                        ?><esi:assign name="y" value="'<?= base64_encode(json_encode($opt_name)) ?>'"/><?php
                    ?></esi:when><?php
                }

                ?><esi:otherwise><?php
                    ?><esi:assign name="y" value="'<?= base64_encode(json_encode(self::CONTROL)) ?>'"/><?php
                ?></esi:otherwise><?php

            ?></esi:choose><?php
            ?><esi:assign name="experiments{'<?= base64_encode($experiment) ?>'}" value="$(y)"/><?php

            ?><esi:text><?php

            $this->activeExperiments[] = $experiment;

        }

        if (!is_array($options)) {
            $options = array($options);
        }

        $clauses = array();
        foreach ($options as $option) {
            $clauses[] = sprintf('$(experiments{\'%s\'})==\'%s\'', base64_encode($experiment), base64_encode(json_encode($option)));
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

    public function track() {

        $dimensions = array();
        foreach ($this->activeExperiments as $experiment) {
            $dimensions[] = sprintf(
                '%s:</esi:text><esi:vars>$base64_decode($(experiments{\'%s\'}))</esi:vars><esi:text>',
                json_encode('dimension' . $this->experiments[$experiment]['dimension']),
                base64_encode($experiment)
            );
        }

        ?><script><?php

            ?>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){<?php
            ?>(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<?php
            ?>m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<?php
            ?>})(window,document,'script','//www.google-analytics.com/analytics.js','ga');<?php

            ?>ga('create','UA-51391553-1','abtronic.gpirujo.com');<?php
            ?>ga('send','pageview', {<?= join(',', $dimensions) ?>});<?php

        ?></script><?php

    }

}
