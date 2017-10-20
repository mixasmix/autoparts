<?php
interface Supplier{
    public function step1($partNumber);
    public function step2($uid, $partNumber='');
    public function searchalloffer($partNumber);
    public function addbacket($uid, $quantity, $price, $comment='');
    public function editbacket($uid, $quantity);
    public function clearbacket();
    public function deletebacket($uid);
    public function getbacket();
    public function makeorder($uid, $quantity, $price, $comment='');
}