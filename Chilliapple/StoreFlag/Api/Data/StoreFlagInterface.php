<?php

namespace Chilliapple\StoreFlag\Api\Data;

/**
 * @api
 */
interface StoreFlagInterface
{
    const FLAG_ID = 'flag_id';
    const FLAG_IMAGE = 'flag_image';
    const FLAG_URL = 'flag_url';

    public function getId();


    public function getFlagId();

    public function setFlagId($flagId);


    public function getFlagImage();

    public function setFlagImage($flagImage);

    public function getFlagUrl();

    public function setFlagUrl($flagUrl); 

}
