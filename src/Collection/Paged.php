<?php

namespace Mduk\Gowi\Collection;

use Mduk\Gowi\Collection;

class Paged extends Collection {

  protected $pages = [];

  /**
   * Retrieve one page of objects, if a page extends beyond
   * the end of the collection, the last page is cut short.
   *
   * @param int $page Which page you want.
   * @param int $limit How many objects in each page. Default 10.
   * @return array Objects
   */
  public function page( $page, $limit = 10 ) {
    if ( $page <= 0 ) {
      throw new Paged\Exception( "Invalid page: {$page}" );
    }

    $pageKey = "{$limit}:{$page}";
    if ( !isset( $this->pages[ $pageKey ] ) ) {
      $offset = ( $page - 1 ) * $limit;
      $end = $offset + $limit;

      if ( $end > $this->count() ) {
        $difference = $end - $this->count();
        $limit = $limit - $difference;
      }

      if ( $offset > $this->count() ) {
        throw new Paged\Exception( "Invalid page: {$page}" );
      }

      $this->pages[ $pageKey ] = new Page( $this, $page, $offset, $limit );
    }

    return $this->pages[ $pageKey ];
  }

  /**
   * Get the number of pages
   */
  public function numPages( $size = 10 ) {
  	return ceil( ( $this->count() - 1 ) / $size );
  }

  /**
   * Calculate what page an offset is on
   */
  public function calculatePage( $offset, $size = 10 ) {
  	return ceil( $offset / $size );
  }

}

