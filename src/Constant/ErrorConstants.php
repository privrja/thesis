<?php

namespace App\Constant;

class ErrorConstants {

    const ERROR_JSON_FORMAT = 'Wrong JSON format';
    const ERROR_VISIBILITY_FORMAT = 'Visibility has not supported format! Supported format is PUBLIC/PRIVATE';
    const ERROR_CONTAINER_NAME_EXISTS = 'Container with this name exists for same user';
    const ERROR_EMPTY_PARAMS = 'At least one parameter should not be empty';
    const ERROR_EMPTY_ID = 'You need to specify id';
    const ERROR_CONTAINER_NOT_EXISTS_FOR_USER = 'Container with specified id for user not exists';
    const ERROR_NAME_IS_TAKEN = 'This name is taken';
    const ERROR_SOMETHING_GO_WRONG = 'Something go wrong';
    const ERROR_SERVER_IDENTIFIER = 'Server is unknown';
    const ERROR_SERVER_IDENTIFIER_PROBLEM = 'Server identifier or server is empty, but one of them is filled';
    const ERROR_FORMULA_OR_SMILES = 'Formula or SMILES need to be filled';
    const ERROR_CONTAINER_INSUFIENT_RIGHTS = 'You don\'t have enough permissions';
    const ERROR_SEQUENCE_BAD_TYPE = 'Bad sequence type';
    const ERROR_USER_ALREADY_IN_CONTAINER = 'User already in container';
    const ERROR_CANT_DELETE_LAST_RWM_USER = 'Can\'t remove last RWM user from container';
    const ERROR_MODE_FORMAT = 'Mode has not supported format! Supported format is R/RW/RWM';

}
