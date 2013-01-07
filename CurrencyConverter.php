<?php

namespace Currency;

class CurrencyConverter
{

    private $googleApiUrl = 'http://www.google.com/ig/calculator?hl=%s&q=%d%s=?%s';
    private $haveToReload = true;

    private $amount;
    private $fromCurrency;
    private $toCurrency;
    private $fromCurrencyLabel;
    private $toCurrencyLabel;
    private $result;
    private $userLang;
    private $rate;

    /**
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     */
    public function __construct($amount = 1, $fromCurrency = 'EUR', $toCurrency = 'USD', $userLang = 'en')
    {
        $this->amount = $amount;
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->userLang = $userLang;
    }

    private function reloadDatas()
    {
        if (is_null($this->userLang)) {
            throw new CurrencyConverterException("The user language must be set.");
        }
        if (is_null($this->amount)) {
            throw new CurrencyConverterException("The amount must be set.");
        }
        if ($this->amount == 0) {
            throw new CurrencyConverterException("The amount can't be 0.");
        }
        if (is_null($this->fromCurrency)) {
            throw new CurrencyConverterException("The local currency must be set.");
        }
        if (is_null($this->toCurrency)) {
            throw new CurrencyConverterException("The required currency must be set.");
        }

        $json = file_get_contents(sprintf($this->googleApiUrl, $this->userLang, $this->amount, $this->fromCurrency, $this->toCurrency));
        $json = str_replace('lhs', '"lhs"', $json);
        $json = str_replace('rhs', '"rhs"', $json);
        $json = str_replace('error', '"error"', $json);
        $json = str_replace('icc', '"icc"', $json);
        $datas = json_decode(utf8_encode($json), true);

        if (is_null($datas) || count($datas) == 0) {
            throw new CurrencyConverterException("An error occured during the conversion from '%s' to '%s'.", $this->fromCurrency, $this->toCurrency);
        }

        preg_match('/\d+(?:[\.,]\d+)?/', $datas['rhs'], $matches);
        $this->result = (float) str_replace(',', '.', $matches[0]);

        $this->rate = (float) round($this->amount / $this->result, 2);

        $this->fromCurrencyLabel = trim(preg_replace('/\d+(?:[\.,]\d+)?/', '', $datas['lhs']));
        $this->toCurrencyLabel = trim(preg_replace('/\d+(?:[\.,]\d+)?/', '', $datas['rhs']));

        $this->haveToReload = false;
    }

    public function getAmount()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->haveToReload = true;
        $this->amount = $amount;
    }

    public function getFromCurrency()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->fromCurrency;
    }

    public function setFromCurrency($fromCurrency)
    {
        $this->haveToReload = true;
        $this->fromCurrency = $fromCurrency;
    }

    public function getToCurrency()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->toCurrency;
    }

    public function setToCurrency($toCurrency)
    {
        $this->haveToReload = true;
        $this->toCurrency = $toCurrency;
    }

    public function getUserLang()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->userLang;
    }

    public function setUserLang($userLang)
    {
        $this->haveToReload = true;
        $this->userLang = $userLang;
    }

    public function getResult()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->result;
    }

    public function getPrettyResult()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return sprintf('%s %s (%s) = %s %s (%s) ==> [%s %%]',
                $this->amount,
                $this->fromCurrencyLabel,
                $this->fromCurrency,
                $this->result,
                $this->toCurrencyLabel,
                $this->toCurrency,
                $this->rate
        );
    }

    public function getFromCurrencyLabel()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->fromCurrencyLabel;
    }

    public function getToCurrencyLabel()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->toCurrencyLabel;
    }

    public function getRate()
    {
        if ($this->haveToReload)
        {
            $this->reloadDatas();
        }
        return $this->rate;
    }

}

class CurrencyConverterException extends \RuntimeException
{

}