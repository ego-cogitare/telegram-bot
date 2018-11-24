<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/17/18
 * Time: 10:51 PM
 */

namespace App\Traits;


/**
 * Trait ReflectionTrait
 */
trait ReflectionTrait
{
    /**
     * @return array
     * @throws ReflectionException
     * @throws \ReflectionException
     */
    public static function getConstants()
    {
        /** @var ReflectionClass $rc */
        $rc = new \ReflectionClass(static::class);

        return $rc->getConstants();
    }
}