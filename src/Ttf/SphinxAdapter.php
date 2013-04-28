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
     * Overload the __call method to access SphinxSearch-API methods directly
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call( $method, $params ){
        if( method_exists( $this->_cl, $method ) ){
            return call_user_func_array( array( $this->_cl, $method ), $params);
        }
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
            throw new Exception( "Sphinx client oops: " . $this->_cl->GetLastError());
        } else {
            if( !empty($result['warning']) ) {
                return $this->_cl->GetLastWarning();
            }
            return new SphinxResult( $result );
        }
    }
    
}

class SphinxResult {
    
    private $_rs;       // stores original resultset
    private $_matches;  // stores result matches and their attributes
    private $_meta;     // stores metadata
    
    public function __construct( Array $resultSet ) {
        $this->_rs = $resultSet;
        $this->_matches = $resultSet['matches'];
        $this->_meta = array( 
            'time' => $resultSet['time'],
            'total' => $resultSet['total'],
            'total_found' => $resultSet['total_found'],
        );
    }
    
    /**
     *
     * @return string A human readable string of your search results
     */
    public function __toString() {
        $resultString = '';
        foreach( $this->_matches as $match => $attrs ) {
            $resultString .= sprintf( 'Document %d matches (weight: %d).<br/>', $match, $attrs['weight']);
        }
        $resultString .= sprintf('<p>Found %d result(s) in in %s seconds.</p>', $this->_meta['total_found'], $this->_meta['time']);
        return $resultString;
    }
    
    /**
     * Return the number of results found
     * @return integer The number of results found
     */
    public function numFound(){
        return (int) $this->_meta['total_found'];
    }
    
    /**
     *
     * @return array the original sphinx result object
     */
    public function dump(){
        return $this->_rs;
    }
    
    /**
     * Show the words used in the search and their respective hits.
     * These depend on the morphology used
     * @return array of words and their hits
     */
    public function showWords(){
        return $this->_rs['words'];
    }
    
    /**
     * Return all matches from the previous search
     * @return array of all matches
     */
    public function fetchAll(){
        return $this->_matches;
    }
    
    /**
     *
     * @return array of index keys
     */
    public function fetchIndices(){
        return array_keys( $this->_matches );
    }
    
}

?>