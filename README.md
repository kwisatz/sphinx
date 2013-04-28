SphinxServiceProvider
=====================

This is a simple service provider for silex that ties the SphinxSearch-API in.


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
The only available call at this moment is "search":

    $app->get('/search', function( Request $request ) use ($app) {
        $result = $app['sphinx']->search( $request->get('q'), 'myCatalog' );
        return new Response( json_encode( $result ), 200 );
    });

In this case, a call request would look like:

    http://silex.local/search?q=cheese

and return a json string with the raw sphinx result object.