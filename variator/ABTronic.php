<?php

class ABTronic {

    const DICE = 128;
    const COOKIE = 'die';
    const CONTROL = 'CONTROL';

    public static function run($esi, $experiments, $body) {
        $that = new static($esi);
        $esi->in(function($esi) use($body, $that, $experiments) {
            $esi->for_each('[0,1]', 'run_user_code', function($esi) use($body, $that, $experiments) {
                $esi->choose(function($esi) use($body, $that, $experiments) {
                    $esi->when('$(run_user_code)', function($esi) use($body, $that) {
                        $esi->out($body, $that);
                    });
                    $esi->otherwise(function($esi) use($that, $experiments) {
                        if (count($that->activeExperiments)) {

                            $esi->choose(function($esi) {
                                $esi->when(sprintf('$(HTTP_COOKIE{\'%s\'}) matches \'\'\'^[0-9]{%d}$\'\'\'', ABTronic::COOKIE, ABTronic::DICE * 2), function($esi) {
                                    $esi->assign('die', '$(MATCHES{0})');
                                });
                                $esi->otherwise(function($esi) {
                                    $esi->assign('die', '\'\'');
                                    $esi->for_each(sprintf('[1..%d]', ABTronic::DICE * 2), 'item', function($esi) {
                                        $esi->assign('die', '$(die) + $str($rand(10))');
                                    });
                                    $esi->set_cookie(ABTronic::COOKIE, '$(die)');
                                });
                            });

                            foreach ($that->activeExperiments as $experiment) {

                                $opts = $experiments[$experiment]['options'];
                                $crc = crc32($experiment);

                                $esi->assign('x', sprintf('($int($substr($(die), %d, 2)) + %d) %% 100', ($crc % ABTronic::DICE) * 2, $crc % 100));
                                $esi->choose(function($esi) use($opts) {

                                    ksort($opts);
                                    foreach ($opts as $opt_name => $opt_val) {
                                        $cummulative += $opt_val;
                                        $esi->when(sprintf('$(x)<=%d', $cummulative), function($esi) use($opt_name) {
                                            $esi->assign('y', sprintf('\'%s\'', base64_encode($opt_name)));
                                        });
                                    }

                                    $esi->otherwise(function($esi) {
                                        $esi->assign('y', sprintf('\'%s\'', base64_encode(ABTronic::CONTROL)));
                                    });

                                });
                                $esi->assign(sprintf('experiments{\'%s\'}', base64_encode($experiment)), '$(y)');
                                $esi->assign(sprintf('dimensions{\'%s\'}', base64_encode($experiment)), sprintf('\'dimension%d\'', $experiments[$experiment]['dimension']));

                            }

                        }
                    });
                });
            });
        });
    }

    private $esi;
    public $activeExperiments = array();

    public function __construct($esi) {
        $this->esi = $esi;
    }

    public function variate($experiment, $options, $then, $else = null) {

        if (!in_array($experiment, $this->activeExperiments)) {
            $this->activeExperiments[] = $experiment;
        }

        if (!is_array($options)) {
            $options = array($options);
        }

        $clauses = array();
        foreach ($options as $option) {
            $clauses[] = sprintf('$(experiments{\'%s\'})==\'%s\'', base64_encode($experiment), base64_encode($option));
        }

        $this->esi->in(function($esi) use($then, $else, $clauses) {
            $esi->choose(function($esi) use($then, $else, $clauses) {
                $esi->when(join('||', $clauses), $then);
                if ($else) {
                    $esi->otherwise($else);
                }
            });
        });

    }

    public function track() {
        ?><script><?php

            ?>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){<?php
            ?>(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<?php
            ?>m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<?php
            ?>})(window,document,'script','//www.google-analytics.com/analytics.js','ga');<?php

            ?>ga('create','UA-51391553-1','abtronic.gpirujo.com');<?php
            ?>ga('send','pageview', {<?php

            $this->esi->in(function($esi) {
                $esi->assign('comma', "''");
                $esi->for_each('$(experiments)', 'experiment', function($esi) {
                    $esi->vars('$(comma)\'$(dimensions{$(experiment{0})})\':\'$base64_decode($(experiment{1}))\'');
                    $esi->assign('comma', "','");
                });
            });

            ?>});<?php

        ?></script><?php
    }

}
