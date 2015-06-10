<?php

namespace Mduk\Gowi\Collection;

use Mduk\Gowi\Collection;

class Page extends Collection {

  protected $collection;
  protected $pageNum;
  protected $offset;
  protected $limit;

  public function __construct( $collection, $pageNum, $offset, $limit ) {
    $this->collection = $collection;
    $this->pageNum = $pageNum;
    $this->offset = $offset;
    $this->limit = $limit;
  }

  public function get( $offset, $limit = null ) {
    $realOffset = $this->offset + $offset;
    return $this->collection->get( $offset, $limit );
  }

  public function getAll() {
    return $this->collection->get( $this->offset, $this->limit );
  }

  public function nextPage() {
    return $this->collection->page( $this->pageNum + 1, $this->limit );
  }
}

