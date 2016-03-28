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

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Visualphpunit graph action
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 */
class Graph extends Action
{

    /**
     *
     * @param Request $request            
     * @param Application $app            
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, Application $app)
    {
        $data = array(
            'title' => 'Graphs'
        );
        return $this->ok($data);
    }
}
