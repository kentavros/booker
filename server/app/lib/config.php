<?php
/**
 * Set Date time zone
 */
date_default_timezone_set('Europe/Kiev');

/**
 * REST _ ENCODE DATA TYPE DEFAULT
 */
define('ENCODE_DEFAULT', '.json');

/**
 * Set Weekend Day - Numeric representation of the day of the week
 * 0 (for Sunday) through 6 (for Saturday)
 */
//define('WEEKEND', $arrWeekend=[0, 6]);
define('WEEKEND1', 0);
define('WEEKEND2', 6);

/**
 * Default time Start and End events
 */
define('TIME_START', 8);
define('TIME_END', 20);


/**
 * for Data Base MySQL
 */
define('DSN_MY', 'mysql:host=localhost;dbname=user6');
define('USER_NAME', 'user6');
define('PASS', 'tuser6');
define('TIME_FORMAT', 'Y-m-d H:i:s');

/**
 * Errors
 */
define('ERR_DB', 'Error connecting to DB');
define('ERR_QUERY', 'Error query to DB');
define('ERR_FIELDS', 'Error - some fields are empty!');
define('ERR_SEARCH', 'Nothing found');
define('ERR_LOGIN', 'This login or email exists');
define('INVAL_USERNAME', 'Invalid User name - User Name must be at least 3 characters and not more than 35');
define('INVAL_LOGIN', 'Wrong login - the login can consist only of letters of the English alphabet and numbers without spaces and must be at least 3 characters and not more than 30');
define('INVAL_EMAIL', 'Invalid email format!');
define('INVAL_PASS', 'Invalid password format!');
define('INVAL_DESCR', 'It is necessary to specify more detailed information on the event (minimum 6 symbols)!');
define('INVAL_TIMEMORE', 'The time of the beginning of the event should be less and not exactly the end time!');
define('INVAL_WEEKEND', 'At the weekend the Boardroom is closed - pleace, choose another date!');
define('INVAL_TIME_S_E', 'Your event is out of the acceptable time!');
define('INVAL_RECURR', 'Invalid values for creating a recurring event');
define('ERR_ADDEVENT', 'Your event intersects with another event! Check the date and time!');
define('ERR_AUTH', 'Error, check password and login');
define('ERR_DATA', 'Error, Missing data!');
define('ERR_ACCESS', 'Access denied!');
define('ERR_A_DEL', 'Error, there must be at least one user with the role of admin!');