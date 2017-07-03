<?php

require 'Math.php';

class EllipticCurve
{

    private $a;
    private $b;
    private $p;
    private $q;
    private $x3;
    private $y3;
    private $prime;
    private $lambda;
    private $criveNumbers;
    private $elipticGroup;

    public function __construct()
    {
        $this->criveNumbers = array();
        $this->elipticGroup = array();
    }

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setB($b)
    {
        $this->b = $b;
    }

    public function getPrime()
    {
        return $this->prime;
    }

    public function setPrime($prime)
    {
        $this->prime = $prime;
    }

    public function getCriveNumbers(): array
    {
        return $this->criveNumbers;
    }

    public function setCriveNumbers(array $criveNumbers)
    {
        $this->criveNumbers = $criveNumbers;
    }

    public function getP()
    {
        return $this->p;
    }

    public function setP($p1, $p2)
    {
        $this->p = [(int)$p1, (int)$p2];
    }

    public function getQ()
    {
        return $this->q;
    }

    public function setQ($q1, $q2)
    {
        if (!$q1 || !$q2) {
            $this->q = $this->p;
        } else {
            $this->q = [(int)$q1, (int)$q2];
        }
    }

    public function genCriveNumbers(): Array
    {
        if ($this->prime == null) {
            throw new Exception("To get crive array we need the prime number.");
        }
        $primeSum = $this->prime;
        for ($i = 0; $i < $this->prime; $i++) {
            if ($i > 0)
                $primeSum = $primeSum + $this->prime;
//            echo "<br>".$i." ". $primeSum. "<br>";
            for ($j = 0; $j < $this->prime; $j++) {
                $sumIsPerfectSquare = ($primeSum + $j);
//                echo " ". $sumIsPerfectSquare. " ";
                if (gmp_perfect_square((string)$sumIsPerfectSquare)) {
//                    echo "perfect";
                    array_push($this->criveNumbers, $j);
                }
            }
//            echo "<br>";
        }
        $this->criveNumbers = array_unique($this->criveNumbers);
        return $this->criveNumbers;
    }

    public function genElipticGroup()
    {
        if ($this->a == null) {
            throw new Exception("To get ElipticGroup array we need the A number.");
        }
        if ($this->b == null) {
            throw new Exception("To get ElipticGroup array we need the B number.");
        }
        if ($this->prime == null) {
            throw new Exception("To get ElipticGroup array we need the Prime number.");
        }
        if ($this->criveNumbers == null) {
            throw new Exception("To get ElipticGroup array we need the Crive numbers.");
        }
//        print_r($this->criveNumbers);
//        echo "<br>";
        foreach ($this->criveNumbers as $crive) {
//            echo 'crivo: '.$crive;
//            echo "<br>";
            $y = $this->genY($crive);
//            echo 'y: '.$y;  echo "<br>";
            if ($this->yIsGreaterThanZero($y)) {
                $value = sqrt($y);
                if (filter_var($value, FILTER_VALIDATE_INT)) {
                    $y1 = $this->mod($value, $this->prime);
                    $y2 = $this->mod(($value * -1), $this->prime);
                    $this->elipticGroup[] = [$crive, $y1];
                    $this->elipticGroup[] = [$crive, $y2];
                }
            }
        }
        return $this->elipticGroup;
    }

    public function calculateLambda()
    {
        if ($this->p == $this->q) {
            $this->lambda = ((3 * pow($this->p[0],2) + $this->a) / (2 * $this->p[1]));
        } else {
            $this->lambda = (($this->q[1] - $this->p[1]) / ($this->q[0] - $this->p[0]));
        }
        return $this->lambda;
    }

    public function calculateX3()
    {
        $lambdaNegative = false;
//        echo '<br>'.$this->lambda . '<br>';
        $newLambda = $this->lambda;
        if ($this->lambda < 0) {
            $newLambda *= -1;
            $lambdaNegative = true;
        }
        $newLambda = pow($newLambda, 2);
//        echo $newLambda .'- (' .$this->p[0] .' - '. $this->q[0].')';
        $result = ($newLambda - $this->p[0] - $this->q[0]);
//        echo '<br>'.$result . '<br>';
//        $result *= ($lambdaNegative) ? -1 : 1; // pow function cannot receive base negative so contorn with that
//        echo '<br>'.$result . '<br>';
        $this->x3 = $this->mod((int)$result, $this->prime);
        return $this->x3;
    }

    public function calculateY3()
    {
        $result = (int)($this->lambda * ($this->p[0] - $this->x3) - $this->p[1]);
        $this->y3 = $this->mod((int)$result, $this->prime);
        return $this->y3;
    }

    public function validateConditionAB()
    {
        $result = ((4 * pow($this->a, 2)) + (27 * pow($this->b, 2)));
        return $this->mod($result, 23) != 0;
    }

    private function yIsGreaterThanZero($y): Bool
    {
        return ($y > 0);
    }

    private function mod($value, $prime): Int
    {
        return (int)gmp_strval(gmp_mod((string)$value, (string)$prime));
    }

    private function genY($crive)
    {
        return (pow($crive, 3) + ($this->a * $crive) + $this->b);
    }

    public function getPrimes()
    {
        return json_encode(Math::generatePrimes());
    }
}

/**
 * https://stackoverflow.com/questions/15993119/php-gmp-perfect-square-doesnt-work/15993157
 * http://php.net/manual/en/gmp.installation.php
 * https://github.com/heroku/heroku-buildpack-php/issues/117
 * https://github.com/heroku/heroku-buildpack-php/pull/58
 *
 */
//$elliptic = new EllipticCurve();
//$elliptic->setA(1);
//$elliptic->setB(6);
//$elliptic->setPrime(7);
//print_r($elliptic->genCriveNumbers());
//print_r($elliptic->genElipticGroup());
//$elliptic->setP(1, 9);
//$elliptic->setQ(2, 19-7);
//$lambda = $elliptic->calculateLambda();
//print_r($elliptic->calculateX3());
//print_r($elliptic->calculateY3());
