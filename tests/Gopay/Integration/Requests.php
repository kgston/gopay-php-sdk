<?php
namespace GopayTest\Integration;

trait Requests {

    public function createValidToken() {
        $transactionToken = $this->getClient()->createCardToken(
            "test@test.com",
            "PHP test",
            "4242424242424242",
            "02",
            "2022",
            "123",
            "one_time",
            NULL,
            "test",
            NULL,
            "test",
            "test",
            "jp",
            "101-1111",
            "81",
            "12910298309128");
        return $transactionToken;
    }

    public function createValidCharge(bool $capture = true) {
        $transactionToken = $this->createValidToken();
        $charge = $this->getClient()->createCharge($transactionToken->id, 1000, "jpy", $capture);
        $charge = $charge->awaitResult();        
        return $charge;
    }

    public function createValidRefund() {
        $charge = $this->createValidCharge(true);
        $refund = $charge->createRefund(1000, "jpy", "fraud", "test", array("something" => "value"));
        return $refund;
    }
}
