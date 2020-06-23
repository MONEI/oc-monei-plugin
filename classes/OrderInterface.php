<?php namespace MONEI\MONEI\Classes;


interface OrderInterface
{
    public function setOrderIFull();

    public function updateStatus($sStatus, $sMsg);
}