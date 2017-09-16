<?php

namespace App;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function getIndex(Request $request, Application $app)
    {
        //

        return $app['twig']->render('index.twig', [
            //
        ]);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function getStat(Request $request, Application $app)
    {
        //

        return $app['twig']->render('stat.twig', [
            //
        ]);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function postStat(Request $request, Application $app)
    {
        //

        return $app['twig']->render('stat.twig', [
            //
        ]);
    }
}
