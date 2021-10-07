<?php

namespace Drupal\bhcc_central_hub\Geocoder\Provider;

use Geocoder\Collection;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Model\AddressCollection;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\Provider;
use Drupal\bhcc_central_hub\AddressLookupService;
use Drupal\localgov_forms\Geocoder\Model\LocalgovAddress;

/**
 * Provides a geocoder handler for Central Hub Residential.
 */
class CentralHubResidential extends AbstractProvider implements Provider {

  /**
   * {@inheritdoc}
   */
  public function getName() : string {
    return 'bhcc_central_hub_residential';
  }

  /**
   * {@inheritdoc}
   */
  public function geocodeQuery(GeocodeQuery $query): Collection {

    // Get the address to search.
    $address = $query->getText();

    // Get a geocode from Central Hub.
    // @todo Work out how to do this with dependency injection.
    $geocode_results = AddressLookupService::addressLookup($address, 'residential', 10);

    // If no result, return and empty collection.
    if (empty($geocode_results)) {
      return new AddressCollection([]);
    }

    // Format the results.
    foreach ($geocode_results as $geocode) {
      $results[] = LocalgovAddress::createFromArray([
        'providedBy'       => $this->getName(),
        'streetNumber'     => $geocode['house'] ?? NULL,
        'streetName'       => $geocode['street'] ?? NULL,
        'flat'             => $geocode['flat'] ?? NULL,
        'locality'         => $geocode['town'] ?? NULL,
        'postalCode'       => $geocode['postcode'] ?? NULL,
        'country'          => 'United Kingdom',
        'countryCode'      => 'GB',
        'display'          => $geocode['display'] ?? '',
        'formattedAddress' => $geocode['display'] ?? NULL,
        'latitude'         => $geocode['lat'] ?? NULL,
        'longitude'        => $geocode['lng'] ?? NULL,
        'uprn'             => $geocode['uprn'] ?? '',
      ]);
    }

    return new AddressCollection($results);
  }

  /**
   * {@inheritdoc}
   */
  public function reverseQuery(ReverseQuery $query): Collection {
    throw new UnsupportedOperation('The Central Hub provider is not able to do reverse geocoding.');
  }

}
