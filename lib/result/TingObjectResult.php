<?php

class TingObjectResult {

  /**
   * Raw data from webservice
   * @var OutputInterface
   */
  private $result;

  /**
   * Item in current result set.
   * Contains TingObject or TingEntity.
   * @var array
   */
  protected $item;

  public function __construct(OutputInterface $result, TingObjectRequest $request) {
    $this->result = $result;
    $this->process();
  }

  /**
   * Build items from raw data (json).
   */
  protected function process() {
    // Check for errors.
    $error = $this->result->getValue('searchResponse/error');
    if (!empty($error)) {
      throw new TingClientException($error);
    }

    $data = $this->result->getValue('searchResponse/result');
    $items = $data->getValue('searchResult');

    // Opensearch 5.2 introduces new error element in "searchResult" if record
    // is unavailable. If the "error" is present, log it.
    if (!empty($items[0]->getValue('collection/object/error'))) {
      $error = $items[0]->getValue('collection/object/error');
      throw new TingClientException($error);
    }

    try {
      $this->item = new TingObject($items[0]);
    } catch (TingObjectException $e) {
      throw new TingClientException($e->getMessage());
    }

    $this->result = NULL;
  }

  public function getObject() {
    return $this->item;
  }

}
