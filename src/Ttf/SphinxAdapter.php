<?php

namespace Ttf;

/**
 * This is a simple adapter to the SphinxApi client that comes together with the 
 * SphinxServiceProvider for Silex
 *
 * @author David Raison <david@raison.lu>
 */
class SphinxAdapter {
    
    private $_cl;   // reference to the client object
    
    public  function __construct( $host, $port ){
        $this->_cl = new \SphinxClient;
        $this->_cl->SetServer( $host, (int) $port );
        $this->_cl->SetMatchMode( SPH_MATCH_ANY );
    }
    
    /**
     *
     * @param type $query the query to perform on the catalog
     * @param type $catalog the catalog to search in
     * @return type array of results, false if none
     */
    public function search( $query, $catalog ){
        $result = $this->_cl->Query( $query, $catalog );
        if( $result == false ) {
            echo "Sphinx client oops: " . $this->_cl->GetLastError();
            return false;
        } else {
            if( !empty($result['matches']) ) {
               return $result;   
            }
        }
     
    }
    
}

?>