<?php

use App\Database\DB;

/* CORS
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        //  'http://localhost:8080'
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
*/

$app->get('/home', function ($request, $response)
{
    return $this->view->render($response, 'home.twig');
});

$app->get('/home2', 'App\Controllers\HomeController:index');

$app->get('/', function ($request, $response) {
    $routes = $this->router->getRoutes();
    $html = "<table>
    <thead>
        <tr>
            <td> Method </td>
            <td> URI </td>
            <td> Name </td>
            <td> Action </td>
        </tr>
    </thead>
    <tbody>";

    $i=0;
    foreach ($routes as $route) {
        $action = $route->getCallable() instanceof Closure ? 'Closure' : $route->getCallable();
        $html .= "<tr>
            <td>" . implode('|', $route->getMethods()) . "</td>
            <td>". $route->getPattern() . "</td>
            <td>". $route->getName() . "</td>
            <td>". $action . "</td>
        </tr>";
    }

    $html .= "</tbody></table>";
    echo $html;
});

$app->get('/a', function ($request, $response) {
    echo 'HOLA';
});

$app->get('/b', function ($request, $response) {
    $sql = "INSERT INTO redes.parametros (parametros) VALUES (?) RETURNING id";
    DB::query($sql, ["P4"], false);

    $a = $this->db->query("SELECT * FROM redes.parametros WHERE id>=?", 56);

    //$a = $this->db->query('SELECT * FROM redes.parametros');
    return $response->withJson($a);
});
