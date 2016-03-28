<?php
/**
 * VisualPHPUnit
 *
 * VisualPHPUnit is a visual front-end for PHPUnit.
 *
 * PHP Version 5.6<
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @copyright 2011-2015 VisualPHPUnit
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace Visualphpunit\Api\Action;

use Symfony\Component\HttpFoundation\Response;

/**
 * Visualphpunit base action
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 */
abstract class Action
{

    /**
     * Return a found http response
     *
     * @param unknown $data            
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function ok($data)
    {
        return new Response(json_encode($data), 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * Return a not found http response
     *
     * @param unknown $data            
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function notFound($message)
    {
        return new Response(json_encode(array(
            'message' => $message
        )), 404, array(
            'Content-Type' => 'application/json'
        ));
    }
}