<?php

namespace App\Constant;

class ErrorConstants {

    public const ERROR_JSON_FORMAT = 'Wrong JSON format';
    public const ERROR_VISIBILITY_FORMAT = 'Visibility has not supported format! Supported format is PUBLIC/PRIVATE';
    public const ERROR_CONTAINER_NAME_EXISTS = 'Container with this name exists for same user';
    public const ERROR_EMPTY_PARAMS = 'At least one parameter should not be empty';
    public const ERROR_EMPTY_ID = 'You need to specify id';
    public const ERROR_CONTAINER_NOT_EXISTS_FOR_USER = 'Container with specified id for user not exists';
    public const ERROR_NAME_IS_TAKEN = 'This name is taken';
    public const ERROR_SOMETHING_GO_WRONG = 'Something go wrong';
    public const ERROR_SERVER_IDENTIFIER = 'Server is unknown';
    public const ERROR_SERVER_IDENTIFIER_PROBLEM = 'Server identifier or server is empty, but one of them is filled';
    public const ERROR_FORMULA_OR_SMILES = 'Formula or SMILES need to be filled';
    public const ERROR_CONTAINER_INSUFIENT_RIGHTS = 'You don\'t have enough permissions';
    public const ERROR_SEQUENCE_BAD_TYPE = 'Bad sequence type';
    public const ERROR_USER_ALREADY_IN_CONTAINER = 'User already in container';
    public const ERROR_CANT_DELETE_LAST_RWM_USER = 'Can\'t remove last RWM user from container';
    public const ERROR_MODE_FORMAT = 'Mode has not supported format! Supported format is R/RW/RWM';
    public const ERROR_SEQUENCE_FAMILY_NOT_FOUND = 'Sequence family not exists';
    public const ERROR_MODIFICATION_NOT_FOUND = 'Modification not found';
    public const ERROR_CONDITIONS_NOT_MET = 'Conditions not met';
    public const ERROR_CONTAINER_NOT_FOUND = 'Container not found';
    public const ERROR_SIMILARITY_FORMAT = 'Similarity has not supported format! Supported format is name/tanimoto';
    public const ERROR_ORGANISM_NOT_FOUND = 'Organism not found';
    public const QUESTION_EMPTY = 'Bad session';
    public const CAP_VERIFY_FAILURE = 'Cap verify failure';
    public const MASS_POSITIVE = 'Mass or range have to be positive numbers';
    public const USER_NOT_FOUND = 'User not found';
    public const ALREADY_IN_DATABASE = 'Already in database';

}
