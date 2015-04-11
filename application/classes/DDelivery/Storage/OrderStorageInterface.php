<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 11:32 PM
 */

namespace DDelivery\Storage;


interface OrderStorageInterface {

    public function createStorage();

    public function saveOrder($sdkId, $cmsId, $payment, $status, $ddeliveryId = 0, $id = 0 );

    public function getOrder($cmsId);
} 