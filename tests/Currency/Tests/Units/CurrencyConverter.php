<?php

namespace Currency\Tests\Units;

include __DIR__ . '/../../../bootstrap/autoload.php';

use \atoum;

class CurrencyConverter extends atoum
{

    public function testConstructor()
    {
        $converter = new \Currency\CurrencyConverter;
        $this->float($converter->getAmount())->isEqualTo((float) 1);
        $this->string($converter->getFromCurrency())->isEqualTo('EUR');
        $this->string($converter->getToCurrency())->isEqualTo('USD');
        $this->string($converter->getUserLang())->isEqualTo('en');
        $this->string($converter->getFromCurrencyLabel())->isEqualTo('Euro');
        $this->string($converter->getToCurrencyLabel())->isEqualTo('U.S. dollars');
    }

    public function testGetAmount()
    {
        $converter = new \Currency\CurrencyConverter;
        $this->boolean(is_float($converter->getAmount()))->isTrue();
        $this->float($converter->getAmount())->isEqualTo((float) 1);
        $converter->setAmount(1.5);
        $this->float($converter->getAmount())->isEqualTo((float) 1.5);
    }

}