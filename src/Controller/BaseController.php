<?php

namespace App\Controller;

use App\Service\ForecastService;
use App\Utils\ResponseGenrator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{

    private ForecastService $service;

    public function __construct(ForecastService $forecastService)
    {
        $this->service = $forecastService;
    }

    #[Route('/', name: 'app_index')]
    public function index(): JsonResponse
    {
        return $this->json(ResponseGenrator::generate("Weather data service"));
    }

    #[Route("/forecast/now", name: "now", methods:"GET")]
    public function now(): JsonResponse
    {
        try{
            $result = $this->service->getActualNow();
        } catch (Exception $e) {
            $result = $e;
        }

        return $this->json(ResponseGenrator::generate($result));
    }

    #[Route("/forecast", name: "get", methods:"GET")]
    public function get(Request $request): JsonResponse
    {
        try{
            $days = $request->get("days") ?? 7;
            $result = $this->service->getData($days);
        } catch (Exception $e) {
            $result = $e;
        }

        return $this->json(ResponseGenrator::generate($result));
    }

    #[Route("/forecast", name: "update", methods:"PATCH")]
    public function update(): JsonResponse
    {
        try{
            $this->service->updateData();
            $result = "true";
        } catch (Exception $e) {
            $result = $e;
        }

        return $this->json(ResponseGenrator::generate($result));
    }
}
