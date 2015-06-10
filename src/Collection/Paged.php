<?php

namespace Mduk\Gowi\Collection;

use Mduk\Gowi\Collection;

class Paged extends Collection {
  /**
   * Retrieve one page of objects, if a page extends beyond
   * the end of the collection, the last page is cut short.
   *
   * @param int $page Which page you want.
   * @param int $limit How many objects in each page. Default 10.
   * @return array Objects
   */
  public function page( $page, $limit = 10 ) {
  	$offset = $page * $limit;
  	$end = $offset + $limit;

  	if ( $end > $this->count() ) {
  		$difference = $end - $this->count();
  		$limit = $limit - $difference;
  	}

  	return $this->get( $offset, $limit );
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
