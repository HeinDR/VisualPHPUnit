<?php
/**
 * VisualPHPUnit
 *
 * VisualPHPUnit is a visual front-end for PHPUnit.
 *
 * PHP Version 5.3<
 *
 * @author    Johannes Skov Frandsen <localgod@heaven.dk>
 * @copyright 2011-2015 VisualPHPUnit
 * @license   http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link      https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace Visualphpunit\Api\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;

class Vpu implements ControllerProviderInterface
{

    /**
     * (non-PHPdoc)
     *
     * @param Application $app            
     *
     * @see \Silex\ControllerProviderInterface::connect()
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/', 'Visualphpunit\Api\Action\Index::index');
        $controllers->post('/', 'Visualphpunit\Api\Action\Index::index');
        $controllers->get('/archives', 'Visualphpunit\Api\Action\Archive::index');
        $controllers->post('/graphs', 'Visualphpunit\Api\Action\Graph::index');
        $controllers->get('/graphs', 'Visualphpunit\Api\Action\Graph::index');
        $controllers->get('/file-list', 'Visualphpunit\Api\Action\File::index');
        $controllers->get('/help', 'Visualphpunit\Api\Action\Help::index');
        return $controllers;
    }
}