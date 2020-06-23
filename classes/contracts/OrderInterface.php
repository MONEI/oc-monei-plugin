<?php namespace MONEI\MONEI\Classes\Contracts;


interface OrderInterface
{
    public function setOrderIFull();

    public function updateStatus($sStatus, $sMsg);
}