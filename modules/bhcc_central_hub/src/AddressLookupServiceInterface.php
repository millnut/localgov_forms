<?php

namespace Drupal\bhcc_central_hub;

/**
 * Interface for the Address lookup service.
 */
interface AddressLookupServiceInterface {

  /**
   * Initilise the search query.
   *
   * Ensure the search query array is blank.
   *
   * @return AddressLookupServiceInterface
   *   Return self.
   */
  public function initSearch();

  /**
   * Set search parameters.
   *
   * @param array $options
   *   Keyed array of search parameters.
   *   Use to set up the search query.
   *
   * @return AddressLookupServiceInterface
   *   Return self.
   */
  public function setSearchParameters(array $options);

  /**
   * Get search Parameters.
   *
   * @return AddressLookupServiceInterface
   *   Return self.
   */
  public function getSearchParameters();

  /**
   * Do address lookup.
   *
   * @return AddressLookupServiceInterface
   *   Return self.
   */
  public function doLookup();

  /**
   * Get the response status code.
   *
   * @return int
   *   Http status code.
   */
  public function getStatusCode();

  /**
   * Get the results from a lookup.
   *
   * @return array
   *   Results array from search query.
   */
  public function getResults();

  /**
   * Address Lookup static method.
   *
   * @param string $search_string
   *   A search string eg. A postcode.
   * @param string $address_type
   *   An address type
   *   (Central hub currently supports 'residential', 'commercial' and 'all').
   * @param int|null $limit
   *   Limit on how many records to fetch.
   * @param int|null $offset
   *   Offset for how many records to skip.
   *
   * @return array|bool
   *   An array of addresslist from the Central hub api,
   *   or FALSE if api error.
   *   Can also return an empty array if there are no records.
   */
  public static function addressLookup(string $search_string, string $address_type, $limit, $offset);

}
