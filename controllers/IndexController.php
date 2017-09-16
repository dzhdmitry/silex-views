<?php

namespace App;

use Service\StatisticsManager;
use Silex\Application;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function getIndex(Request $request, Application $app)
    {
        $response = new Response($app['twig']->render('index.twig', [
            //
        ]));

        if (!$request->cookies->has('ua')) {
            $ua = $request->headers->get('user-agent');
            $uaHashed = md5($ua);
            $cookie = new Cookie('ua', $uaHashed);

            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function getStat(Request $request, Application $app)
    {
        $statistics = $this->getStatisticsManager($app)->getStatistics();

        return $app['twig']->render('stat.twig', [
            'statistics' => $statistics
        ]);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function postStat(Request $request, Application $app)
    {
        if ($request->cookies->has('ua')) {
            $data = $request->request->all();
            $cookie = $request->cookies->get('ua');

            $this->getStatisticsManager($app)->save($cookie, $data['type'], $data['payload']);

            $response = array_merge($data, [
                'success' => true
            ]);
        } else {
            $response = [
                'success' => false
            ];
        }

        return $app->json($response);
    }

    /**
     * @param Application $app
     * @return StatisticsManager
     */
    private function getStatisticsManager(Application $app)
    {
        return $app['statistics_manager'];
    }
}
