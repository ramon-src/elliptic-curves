<?php
session_start();
require 'EllipticCurve.php';

if (isset($_GET['a'])) {
    $a = $_GET['a'];
    $_SESSION['ECurve']['a'] = $a;
    die(json_encode(['status' => true, 'a' => $a]));
}

if (isset($_GET['b'])) {
    $b = $_GET['b'];
    $_SESSION['ECurve']['b'] = $b;
    die(json_encode(['status' => true, 'b' => $b]));
}

if (isset($_GET['prime'])) {
    $prime = $_GET['prime'];
    $_SESSION['ECurve']['prime'] = $prime;
    $elliptic = new EllipticCurve();
    $elliptic->setPrime($prime);
    $criveNumbers = $elliptic->genCriveNumbers();
    $_SESSION['ECurve']['criveNumbers'] = $criveNumbers;
    die(json_encode(['status' => true, 'prime' => $prime, 'criveNumbers' => $criveNumbers]));
}

if (isset($_GET['genElipticGroup'])) {
    if ($_SESSION['ECurve']['a'] !== '' && $_SESSION['ECurve']['b'] !== '' && $_SESSION['ECurve']['prime'] !== '' && $_SESSION['ECurve']['criveNumbers'] !== null) {
        $elliptic = new EllipticCurve();
        $elliptic->setA((int)$_SESSION['ECurve']['a']);
        $elliptic->setB((int)$_SESSION['ECurve']['b']);
        if ($elliptic->validateConditionAB()) {
            $elliptic->setPrime((int)$_SESSION['ECurve']['prime']);
            $elliptic->setCriveNumbers($_SESSION['ECurve']['criveNumbers']);
            $elipticGroup = $elliptic->genElipticGroup();
            die(json_encode(['status' => true, 'elipticGroup' => $elipticGroup, 'prime' => $_SESSION['ECurve']['prime']]));
        } else {
            die(json_encode(['status' => false, 'message' => 'Escolha outro valor para A ou B']));
        }
    }
}

if ((isset($_POST['p1']) && isset($_POST['p2'])) || (isset($_POST['q1']) && isset($_POST['q2']))) {
    $prime = $_SESSION['ECurve']['prime'];
    if ($_SESSION['ECurve']['a'] !== '' && $prime) {
        $type = $_POST['type'];
        $elliptic = new EllipticCurve();
        $elliptic->setA((int)$_SESSION['ECurve']['a']);
        $elliptic->setP($_POST['p1'], $_POST['p2']);

        if ($type == 'sub')
            $elliptic->setQ($_POST['q1'], ($prime - $_POST['q2']));
        else
            $elliptic->setQ($_POST['q1'], $_POST['q2']);

        $elliptic->setPrime($prime);

        $results = calculate($elliptic, $_POST['product']);
        die(json_encode(['status' => true, 'results' => $results]));
    }
}
function calculate(EllipticCurve $elliptic, $product)
{
    $q = $elliptic->getQ();
    $results = [];

    $lambda = $elliptic->calculateLambda();
    $x3 = $elliptic->calculateX3();
    $y3 = $elliptic->calculateY3();
    $elliptic->setP($x3, $y3);
    if (!is_float($lambda))
        $results[] = ['lambda' => $lambda, 'x3' => $x3, 'y3' => $y3];
    for ($i = 1; $i < $product; $i++) {

        $lambda = $elliptic->calculateLambda();
        $x3 = $elliptic->calculateX3();
        $y3 = $elliptic->calculateY3();
        $elliptic->setQ($x3, $y3);
        if (!is_float($lambda))
            $results[] = ['lambda' => $lambda, 'x3' => $x3, 'y3' => $y3];
    }
    return $results;
}

if (isset($_GET['getPrimes'])) {
    $elliptic = new EllipticCurve();
    die(json_encode(['status' => true, 'primes' => $elliptic->getPrimes()]));
}

if (isset($_GET['clean'])) {
    $_SESSION['ECurve'] = [];
}