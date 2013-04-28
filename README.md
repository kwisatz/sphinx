SphinxServiceProvider
=====================

This is a simple service provider for silex that ties the SphinxSearch-API in.

A word of warning
-----------------

This is currently work in progress and not suitable for use in productive environments.

The API may change often and erratically. Generally, I would advise against
using this before version 1.0.0

I you'd really like to use this productively, I recommend to choose a version
and stick with it, i.e. set "require 'ttf/sphinx': '0.1.2'" in your composer.json


Usage 
-----

Register the service provider as you would any other:

    $app->register( new Ttf\SphinxServiceProvider());

If you're running the sphinx searchd on any host/port combination other than the
assumed "localhost:3312", you need to supply these parameters here:

    $app->register( new Ttf\SphinxServiceProvider(), array( 
        'sphinx.port' => 3413,
        'sphinx.host' => 'sphinx.example.org'
    ));

Following that, you would use it as any other service provider.
Start off by using the search method on the provider, which will return
a result object:

    $app->get('/search', function( Request $request ) use ($app) {
        $result = $app['sphinx']->search( $request->get('q'), 'myCatalog' );
        return new Response( (String) $result, 200 );
    });

Coercing the $result object to a string will return a human readable 
representation of your research results.

In this case, a call request would look like:

    http://silex.local/search?q=cheese

A more elaborate example would be:

    $app->get('/search', function( Request $request ) use ($app) {
        if( $result = $app['sphinx']->search( $request->get('q'), 'catalog' ) ) {
            $indices = $result->fetchIndices();
            
            $query = 'Select description, tags from stand where fiorg = ?';
            foreach( $indices as $index ) {
                $entries = $app['db']->fetchAssoc( $query, array( $index ));
            }
            $app['twig']->render('search.twig',
                array(
                    'results' => $entries
                )
            );
        }
    });

Passing parameters to the Sphinx API
------------------------------------

Note that you can also directly pass arguments to the sphinxsearch api. 
Simply use them on the service:

    $app['sphinx']->SetRankingMode( SPH_RANK_SPH04 );
    $app['sphinx']->SetFieldWeights( array( 'tags' => 10, 'description' => 5) );