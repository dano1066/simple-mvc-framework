<?php 
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Dan Hastings <danielhastings1066@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link http://thephpleague.com/oauth2-client/ Documentation
 */
//Initialise the framework
require_once "../config.php";
require_once "../core.php";
initialiseFramework();
$response = initialiseController();

initialiseView($response);
?>
