<?php

namespace App;

use Service\StatisticsManager;
use Service\ViewRecordFactory;
use Silex\Application;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    /**
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function getIndexAction(Request $request, Application $app)
    {
        $response = new Response($app['twig']->render('index.twig'));

        if (!$request->cookies->has(ViewRecordFactory::COOKIE_NAME)) {
            $userAgent = $request->headers->get('user-agent');
            $cookie = new Cookie(ViewRecordFactory::COOKIE_NAME, md5($userAgent));

            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function getStatAction(Application $app)
    {
        $statistics = $this->getStatisticsManager($app)->getStatistics();

        return $app['twig']->render('stat.twig', [
            'statistics' => $statistics
        ]);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     */
    public function postStatAction(Request $request, Application $app)
    {
        $record = $app['view_record_factory']->fromRequest($request);

        if (!$record) {
            return $app->json([
                'success' => false
            ]);
        }

        $success = $this->getStatisticsManager($app)->save($record);

        if (!$success) {
            return $app->json([
                'success' => false
            ]);
        }

        return $app->json(array_merge($request->request->all(), [
            'success' => true
        ]));
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
