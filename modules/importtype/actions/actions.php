<?php
if(!defined('ABSPATH'))
{
        die('Exit if accessed directly');
}


/******************************
 * filename:    modules/post/actions/actions.php
 * description:
 */

class ImporttypeActions extends SkinnyActions {

    public function __construct()
    {
    }

  /**
   * The actions index method
   * @param array $request
   * @return array
   */
    public function executeIndex($request)
    {
        // return an array of name value pairs to send data to the template
        $data = array();
        return $data;
    }

}
