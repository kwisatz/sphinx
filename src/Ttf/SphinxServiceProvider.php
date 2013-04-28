<?php

/**
 * Silex Service provider for the sphinx search API
 * Uses neutron/sphinxsearch-api
 *
 * @author David Raison <david@raison.lu>
 */

namespace Ttf;

use Silex\Application;
use Silex\ServiceProviderInterface;

class SphinxServiceProvider implements ServiceProviderInterface {
    
    public function register( Application $app ) {
                    
        $app['sphinx'] = $app->share( function() use ($app) {
            $port = isset($app['sphinx.port']) ? $app['sphinx.port'] : '3312';
            $host = isset($app['sphinx.host']) ? $app['sphinx.host'] : 'localhost';
            return new SphinxAdapter( $host, $port );
        });  

    }
    
    public function boot( Application $app ){
        
    }
    
}

?>
