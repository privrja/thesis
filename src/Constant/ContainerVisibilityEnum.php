<?php


namespace App\Constant;

/**
 * Class VisibilityEnum
 * Visibility modes of container PRIVATE or PUBLIC
 * @package App\Constant
 */
abstract class ContainerVisibilityEnum {

    const PRIVATE = 0;
    const PUBLIC = 1;

    const TEXT_PRIVATE = 'PRIVATE';
    const TEXT_PUBLIC = 'PUBLIC';

    /** @var array mapping int code to string */
    public static $backValues = [
        self::TEXT_PRIVATE => self::PRIVATE,
        self::TEXT_PUBLIC => self::PUBLIC
    ];

}
