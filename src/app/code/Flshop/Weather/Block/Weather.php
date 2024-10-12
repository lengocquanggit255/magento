<?php

namespace Flshop\Weather\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class Weather extends Template
{
    protected $curl;
    protected $logger;

    public function __construct(
        Template\Context $context,
        Curl $curl,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    public function getWeatherData($city = 'HaNoi')
    {
        $apiKey = '7a20a3a7199507e141cf626aa977dbdf'; // Replace with your OpenWeatherMap API key
        $apiUrl = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode($city) . '&units=metric&appid=' . $apiKey;

        try {
            // Set the User-Agent header
            $this->curl->setHeaders([
                'User-Agent' => 'FlshopWeatherModule/1.0 (https://magento.test)'
            ]);

            $this->curl->get($apiUrl);
            $response = $this->curl->getBody();
            $weatherData = json_decode($response, true);

            if ($weatherData === null) {
                throw new \Exception('Failed to decode JSON response: ' . json_last_error_msg());
            }

            if (isset($weatherData['cod']) && $weatherData['cod'] == 200) {
                return $weatherData;
            } else {
                $errorMessage = isset($weatherData['message']) ? $weatherData['message'] : 'Unknown error';
                throw new \Exception('API Error: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the error message
            $this->logger->error('Weather API Error: ' . $e->getMessage());
            // Optionally, display a generic error message to the user
            echo '<p>Sorry, we are unable to display weather information at this time.</p>';
        }

        return [];
    }
}
https://api.openweathermap.org/data/2.5/weather?q=London&units=metric&appid=7a20a3a7199507e141cf626aa977dbdf

