<?php declare(strict_types = 1);

namespace LunchCrawler\Google;

use Dogma\Geolocation\Position;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Http\Url;
use Nette\Utils\Json;
use function sprintf;

class GoogleDistanceApiClient
{

	private const URL_BASE = 'https://maps.googleapis.com/maps/api/distancematrix/json';

	/** @var \GuzzleHttp\Client */
	private $httpClient;

	/** @var string */
	private $apiKey;

	public function __construct(Client $httpClient, string $apiKey)
	{
		$this->httpClient = $httpClient;
		$this->apiKey = $apiKey;
	}

	/**
	 * @param \Dogma\Geolocation\Position $start
	 * @param \Dogma\Geolocation\Position $end
	 * @param \LunchCrawler\Google\DistanceUnits|null $distanceUnits
	 * @return mixed[]
	 */
	public function getDistance(Position $start, Position $end, ?DistanceUnits $distanceUnits = null): array
	{
		$url = new Url(self::URL_BASE);
		$url->appendQuery([
			'units=' => $distanceUnits !== null ? $distanceUnits->getValue() : DistanceUnits::METRIC,
			'origins' => sprintf('%s,%s', $start->getLatitude(), $start->getLongitude()),
			'destinations' => sprintf('%s,%s', $end->getLatitude(), $end->getLongitude()),
			'key' => $this->apiKey,
		]);

		try {
			$response = $this->httpClient->request('GET', $url->getAbsoluteUrl());
			if ($response->getStatusCode() !== 200) {
				throw new BadGoogleApiRequestException($response->getStatusCode());
			}

			$jsonResponse = Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
			if ($jsonResponse->status !== 'OK') {
				throw new NotOkGoogleApiResponseException($jsonResponse->error_message);
			}

			return $jsonResponse;
		} catch (GuzzleException $e) {
			throw new BadGoogleApiRequestException(0, $e);
		}
	}

}
