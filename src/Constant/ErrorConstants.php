<?php

namespace App\Constant;

class ErrorConstants {

    const ERROR_JSON_FORMAT = 'Wrong JSON format';
    const ERROR_VISIBILITY_FORMAT = 'Visibility has not supported format! Supported format is PUBLIC|PRIVATE';
    const ERROR_CONTAINER_NAME_EXISTS = 'Container with this name exists for same user';
    const ERROR_EMPTY_PARAMS = 'At least one parameter should not be empty';
    const ERROR_EMPTY_ID = 'You need to specify id';
    const ERROR_CONTAINER_NOT_EXISTS_FOR_USER = 'Container with specified id for user not exists';

}
