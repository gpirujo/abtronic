<?php

class ABTester {

    // Singleton
    protected static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // Instance code

    protected $all = array();
    protected $active = array();
    protected $currentOptions = array();
    protected $esiSupported = false;
    protected $_calledThrowDie = false;
    protected $_isTemplate = false;
    protected $_mustThrowDie = array();
    protected $_slot = array();
    protected $_fullDies = null;
    protected $cookieDomain;
    protected $_cookieName = '__utmv';

    public function __construct() {
        if ($accept_esi = $_SERVER['HTTP_ACCEPT_ESI'] and $esi_versions = explode(', ', $accept_esi) and in_array('1.0', $esi_versions)) {
            $this->esiSupported = true;
            header('Edge-control: dca=esi', false);
        }

        # init has not run yet - this will be improved
        $this->cookieDomain = preg_replace('/^[^\.]+\.olx/', 'olx', $_SERVER['HTTP_HOST']);

    }

    public function startTemplate()
    {
        $this->_isTemplate = true;
        if ( ! $this->esiSupported) {
            foreach ($this->active as $test_name) {
                $this->_throwDie($test_name);
            }
        }
    }

    public function endTemplate()
    {
        foreach (array_keys($this->_mustThrowDie) as $test_name) {
            $this->_throwDie($test_name);
        }
    }

    public function addTest($name, $options, $slot, $active = true) {
        $this->all[$name] = $options;
        $option_names = array_keys($options);
        $this->currentOptions[$name] = $option_names[0];
        $slot = (int) $slot;
        if ($slot < 2) {
            $slot = 1;
        } else if ($slot > 60) {
            $slot = 60;
        }
        $this->_slot[$name] = $slot;
        $this->_calledThrowDie[$name] = false;
        if ($active) {
            $this->active[] = $name;
        }
    }

    public function open()
    {
        if ($this->esiSupported) {
            ?><esi:text><?php
        }
    }

    # can't be protected because it'll be called
    # from a closure in $this->throwDie()
    public function close()
    {
        if ($this->esiSupported) {
            ?></esi:text><?php
        }
    }

    protected function _throwDie($test_name)
    {
        if ( ! $this->_calledThrowDie[$test_name] && $this->isActive($test_name)) {
            $this->_calledThrowDie[$test_name] = true;
            $this->close();
            if ($this->esiSupported) {
                $options = $this->all[$test_name];
                $option_names = array_keys($options);
                $control = array_shift($option_names);
                $this->currentOptions[$test_name] = $control;
                $varname = "abtests{'" . htmlspecialchars($test_name) . "'}";
                if (count($option_names)) {
                    if (null === $this->_fullDies) {
                        $this->_fullDies = 1;
                        ?><esi:choose><?php
                            ?><esi:when test="$(HTTP_COOKIE{'<?= $this->_cookieName ?>'}) matches '''5=ABDice=([0-9]{4})=1'''"><?php
                                ?><esi:assign name="fullDies" value="$(MATCHES{1})" /><?php
                            ?></esi:when><?php
                            ?><esi:otherwise><?php
                                ?><esi:choose><?php
                                    ?><esi:when test="$(HTTP_COOKIE{'<?= $this->_cookieName ?>'}) matches '''5=ABDice=([0-9]{1,2})=1'''"><?php
                                        ?><esi:assign name="oldDie" value="$int($(MATCHES{1}))" /><?php
                                    ?></esi:when><?php
                                    ?><esi:otherwise><?php
                                        ?><esi:assign name="oldDie" value="$rand(100)"/><?php
                                    ?></esi:otherwise><?php
                                ?></esi:choose><?php
                                ?><esi:choose><?php
                                    ?><esi:when test="($(oldDie) < 10)"><?php
                                        ?><esi:assign name="oldDie" value="0 + $str($(oldDie))" /><?php
                                    ?></esi:when><?php
                                ?></esi:choose><?php
                                ?><esi:assign name="fullDies" value="$str($(oldDie))" /><?php
                                ?><esi:foreach collection="[1..2]"><?php
                                    ?><esi:assign name="newDie" value="$rand(10)"/><?php
                                    ?><esi:assign name="fullDies" value="$(fullDies) + $str($(newDie))" /><?php
                                ?></esi:foreach><?php
                                ?>$add_header('Set-Cookie', 'die_to_set=' + $(fullDies)<?php
                                    foreach ($this->cookieProperties() as $name => $value) {
                                        ?> + '; <?= $name ?>=<?= $value ?>'<?php
                                    }
                                ?>)<?php
                            ?></esi:otherwise><?php
                        ?></esi:choose><?php
                    }
                    ?><esi:choose><?php
                        ?><esi:when test="$(fullDies) matches '''[0-9]{<?= (2 * ($this->_slot[$test_name] - 1)) ?>}([0-9]{2})[0-9]*'''"><?php
                            ?><esi:assign name="dice_val" value="$int($(MATCHES{1}))" /><?php
                            ?><esi:choose><?php
                                foreach ($option_names as $option_name) {
                                    $conditions = array();
                                    foreach ($options[$option_name] as $range) {
                                        $conditions[] = '($(dice_val) >= ' . $range[0] . ' && $(dice_val) <= ' . $range[1] . ')';
                                    }
                                ?><esi:when test="<?= implode('||', $conditions) ?>"><?php
                                    ?><esi:assign name="<?= htmlspecialchars($varname) ?>"><?= htmlspecialchars($option_name) ?></esi:assign><?php
                                ?></esi:when><?php
                                }
                                ?><esi:otherwise><?php
                                    ?><esi:assign name="<?= htmlspecialchars($varname) ?>"><?= htmlspecialchars($control) ?></esi:assign><?php
                                ?></esi:otherwise><?php
                            ?></esi:choose><?php
                        ?></esi:when><?php
                    ?></esi:choose><?php
                }
            } else {
                if (null === $this->_fullDies) {
                    if (preg_match('/5=ABDice=([0-9]{4})=1/', @$_COOKIE[$this->_cookieName], $matches)) {
                        $this->_fullDies = $matches[1];
                    } else {
                        if (preg_match('/5=ABDice=([0-9]{1,2})=1/', @$_COOKIE[$this->_cookieName], $matches)) {
                            $oldDie = intval($matches[1]);
                        } else {
                            $oldDie = rand(0, 99);
                        }
                        if ($oldDie < 10) {
                            $oldDie = '0' . $oldDie;
                        }
                        $this->_fullDies = $oldDie;
                        for ($i = 1; $i < 3; $i++) {
                            $this->_fullDies .= rand(0, 9);
                        }
                    }
                    $cp = $this->cookieProperties();
                    setcookie('die_to_set', $this->_fullDies, @$cp['expires'], @$cp['path'], @$cp['domain']);
                }

                if (preg_match('/[0-9]{' . (2 * ($this->_slot[$test_name] - 1)) .'}([0-9]{2})[0-9]*/', $this->_fullDies, $matches)) {
                    $dice_val = $matches[1];
                    $options = $this->all[$test_name];
                    $option_names = array_keys($options);
                    foreach ($option_names as $option_name) {
                        foreach ($options[$option_name] as $range) {
                            if ($dice_val >= $range[0] && $dice_val <= $range[1]) {
                                $this->currentOptions[$test_name] = $option_name;
                                break 2;
                            }
                        }
                    }
                }
            }
            $this->open();
        }
    }

    public function startEsi()
    {
        $this->open();
        $that = $this;
        Sherpa_Shutdown::getInstance()->push(function() use($that) {
            $that->close();
        });
    }

    public function getActiveTests() {
        $active = array();
        foreach ($this->active as $test_name) {
            $options = $this->all[$test_name];
            $optionNames = array_keys($options);
            if (count($optionNames) > 1) {
                $active[] = $test_name;
            }
        }
        return $active;
    }

    public function getTest($test_name) {
        return new Sherpa_ABTester_Test($this, $test_name);
    }

    protected function isActive($test_name) {
        return in_array($test_name, $this->active);
    }

    public function willRender($test_name, $option_names) {
        $option_names = (array) $option_names;
        return $this->isActive($test_name) || in_array($this->currentOptions[$test_name], $option_names);
    }

    public function staticOutputIf($negated, $test_name, $option_names, $then, $else = '') {
        $elseCallback = strlen($else) ? function() use($else) { echo $else; } : null;
        return $this->_outputIf($negated, $test_name, $option_names, function() use($then) { echo $then; }, $elseCallback);
    }

    public function outputIf($test_name, $option_names, $then, $else = null) {
        return $this->_outputIf(false, $test_name, $option_names, $then, $else);
    }

    public function outputIfNot($test_name, $option_names, $then) {
        return $this->_outputIf(true, $test_name, $option_names, $then);
    }

    protected function _outputIf($negated, $test_name, $option_names, $then, $else = null) {
        if ($this->_isTemplate) {
            $this->_mustThrowDie[$test_name] = true;
        } else {
            $this->_throwDie($test_name);
        }
        $option_names = (array) $option_names;
        if ($this->esiSupported && $this->isActive($test_name) && count($this->all[$test_name]) > 1) {

            $conds = array();
            foreach ($option_names as $option_name) {
                $conds[] = sprintf("$(abtests{'%s'}) %s '%s'", htmlspecialchars($test_name), $negated ? '!=' : '==', htmlspecialchars($option_name));
            }
            $cond = implode($negated ? '&&' : '||', $conds);

            $exception = false;
            ?></esi:text><?php
            ?><esi:choose><?php
                ?><esi:when test="<?= $cond ?>"><?php
                    ?><esi:text><?php
                        try {
                            call_user_func($then);
                        } catch (Exception $e) {
                            $exception = $e;
                        }
                    ?></esi:text><?php
                ?></esi:when><?php
                if ($else) {
                    ?><esi:otherwise><?php
                        ?><esi:text><?php
                            try {
                                call_user_func($else);
                            } catch (Exception $e) {
                                $exception = $e;
                            }
                        ?></esi:text><?php
                    ?></esi:otherwise><?php
                }
            ?></esi:choose><?php
            ?><esi:text><?php
            if ($exception) {
                throw $exception;
            }

        } else {
            if (in_array($this->currentOptions[$test_name], $option_names) xor $negated) {
                call_user_func($then);
            } else if ($else) {
                call_user_func($else);
            }
        }
    }
    
    public function homeRedirects($countryId)
    {
        $esi_redirect = array();
        if ($this->esiSupported) {
            $host = $_SERVER['HTTP_HOST'];
            $from = 'from';
            $stay = 'stay';
            $cookieName = 'wasAtLastCountry';
            
            $countries = \Config::get('home.esiRedirects.' . $countryId);

            if (is_array($countries) && ! empty($countries)) {
                $subdomain = preg_replace('/\..*/', '', $host);

                ob_start();
                foreach ($countries as $code => $domain) {
                    $destination = 'http://' . $domain . '/?' . http_build_query(array($from => $subdomain));
                    ?>
                    <esi:choose><esi:when test="$(GEO{'country_code'}) == '<?= $code ?>' && !$(QUERY_STRING{'<?= $stay ?>'}) && !$(HTTP_COOKIE{'<?= $cookieName ?>'})">
                        <esi:assign name="destination" value="'<?= $destination ?>'" />
                        <esi:choose><esi:when test="!$is_empty($(QUERY_STRING))">
                            <esi:assign name="destination" value="$(destination) + '&' + $(QUERY_STRING)" />
                        </esi:when></esi:choose>
                        $set_redirect($(destination))
                    </esi:when></esi:choose>
                    <?php
                }
                $esi_redirect[] = ob_get_clean();
            }

            $currentLocationFilter = CurrentLocationFilter::getInstance();
            if ($currentLocationFilter->atCountryLevel()) {
                $regex = str_replace(array('www.', '.'), array('', '\.'), $host) . '$';
                ob_start();
                ?>
                <esi:choose><esi:when test="!$exists($(HTTP_REFERER)) || $is_empty($(HTTP_REFERER)) || $(HTTP_REFERER) matches '''<?= $regex ?>'''">
                    <esi:choose><esi:when test="$(HTTP_COOKIE{'last_url'}) matches '''<?= $regex ?>''' && $(HTTP_COOKIE{'last_url'}) != '<?= $host ?>'">
                        <esi:assign name="destination" value="'http://' + $(HTTP_COOKIE{'last_url'}) + $(REQUEST_PATH)" />
                        <esi:choose><esi:when test="!$is_empty($(QUERY_STRING))">
                            <esi:assign name="destination" value="$(destination) + '?' + $(QUERY_STRING)" />
                        </esi:when></esi:choose>
                        $set_redirect($(destination))
                    </esi:when></esi:choose>
                </esi:when></esi:choose>
                <?php
                $esi_redirect[] = ob_get_clean();
            }

            if (count($esi_redirect)) {
                ?></esi:text><?= implode('', $esi_redirect) ?><esi:text><?php
            }
        }
    }

    protected function cookieProperties() {
        return array(
            'domain' => $this->cookieDomain,
        );
    }

    public function saveDie() {
        if ($active = $this->getActiveTests()) {
            ?><script type="text/javascript">//<![CDATA[
                function readDie(readCookie, deleteCookie, saveDie) {
                    die = readCookie('die_to_set');
                    if (die.match(/^[0-9]{4}$/)) {
                        deleteCookie('die_to_set');
                        saveDie(die);
                    }
                }
                if (!window._gaqEvent) window._gaqEvent=[];
                _gaqEvent.push(function() {
                    _olxga.setCustomVar(3, 'passed_abtest', <?= json_encode(implode(',', $active)) ?>, 3);
                    readDie(readCookie, function(n) {
                        deleteCookie(n, null, <?= json_encode($this->cookieDomain) ?>);
                    }, function(die) {
                        _olxga.setCustomVar(5, 'ABDice', die, 1);
                    });
                });
            </script><?php
        }
    }

}
