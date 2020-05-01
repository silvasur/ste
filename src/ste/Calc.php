<?php

namespace kch42\ste;

/**
 * Class Calc contains static methods needed by <ste:calc />
 */
class Calc
{
    private function __construct()
    {
    }

    /**
     * Parse a mathematical expression with the shunting yard algorithm (https://en.wikipedia.org/wiki/Shunting-yard_algorithm)
     *
     * We could also just eval() the $infix_math code, but this is much cooler :-D (Parser inception)

     * @param string $infix_math
     * @return array
     * @throws RuntimeError
     */
    private static function shunting_yard($infix_math)
    {
        $operators = array(
            "+" => array("l", 2),
            "-" => array("l", 2),
            "*" => array("l", 3),
            "/" => array("l", 3),
            "^" => array("r", 4),
            "_" => array("r", 5),
            "(" => array("", 0),
            ")" => array("", 0)
        );

        preg_match_all("/\s*(?:(?:[+\\-\\*\\/\\^\\(\\)])|(\\d*[\\.]?\\d*))\\s*/s", $infix_math, $tokens, PREG_PATTERN_ORDER);
        $tokens_raw   = array_filter(array_map('trim', $tokens[0]), function ($x) {
            return ($x === "0") || (!empty($x));
        });
        $output_queue = array();
        $op_stack     = array();

        $lastpriority = null;
        /* Make - unary, if neccessary */
        $tokens = array();
        foreach ($tokens_raw as $token) {
            $priority = isset($operators[$token]) ? $operators[$token][1] : -1;
            if (($token == "-") && (($lastpriority === null) || ($lastpriority >= 0))) {
                $priority = $operators["_"][1];
                $tokens[] = "_";
            } else {
                $tokens[] = $token;
            }
            $lastpriority = $priority;
        }

        while (!empty($tokens)) {
            $token = array_shift($tokens);
            if (is_numeric($token)) {
                $output_queue[] = $token;
            } elseif ($token == "(") {
                $op_stack[] = $token;
            } elseif ($token == ")") {
                $lbr_found = false;
                while (!empty($op_stack)) {
                    $op = array_pop($op_stack);
                    if ($op == "(") {
                        $lbr_found = true;
                        break;
                    }
                    $output_queue[] = $op;
                }
                if (!$lbr_found) {
                    throw new RuntimeError("Bracket mismatch.");
                }
            } elseif (!isset($operators[$token])) {
                throw new RuntimeError("Invalid token ($token): Not a number, bracket or operator. Stop.");
            } else {
                $priority = $operators[$token][1];
                if ($operators[$token][0] == "l") {
                    while (
                        !empty($op_stack)
                        && $priority <= $operators[$op_stack[count($op_stack)-1]][1]
                    ) {
                        $output_queue[] = array_pop($op_stack);
                    }
                } else {
                    while (
                        !empty($op_stack)
                        && $priority < $operators[$op_stack[count($op_stack)-1]][1]
                    ) {
                        $output_queue[] = array_pop($op_stack);
                    }
                }
                $op_stack[] = $token;
            }
        }

        while (!empty($op_stack)) {
            $op = array_pop($op_stack);
            if ($op == "(") {
                throw new RuntimeError("Bracket mismatch...");
            }
            $output_queue[] = $op;
        }

        return $output_queue;
    }

    /**
     * @param array $array
     * @return array
     * @throws RuntimeError
     */
    private static function pop2(&$array)
    {
        $rv = array(array_pop($array), array_pop($array));
        if (array_search(null, $rv, true) !== false) {
            throw new RuntimeError("Not enough numbers on stack. Invalid formula.");
        }
        return $rv;
    }

    /**
     * @param array $rpn A mathematical expression in reverse polish notation
     * @return int|float
     * @throws RuntimeError
     */
    private static function calc_rpn($rpn)
    {
        $stack = array();
        foreach ($rpn as $token) {
            switch ($token) {
            case "+":
                list($b, $a) = self::pop2($stack);
                $stack[] = $a + $b;
                break;
            case "-":
                list($b, $a) = self::pop2($stack);
                $stack[] = $a - $b;
                break;
            case "*":
                list($b, $a) = self::pop2($stack);
                $stack[] = $a * $b;
                break;
            case "/":
                list($b, $a) = self::pop2($stack);
                $stack[] = $a / $b;
                break;
            case "^":
                list($b, $a) = self::pop2($stack);
                $stack[] = pow($a, $b);
                break;
            case "_":
                $a = array_pop($stack);
                if ($a === null) {
                    throw new RuntimeError("Not enough numbers on stack. Invalid formula.");
                }
                $stack[] = -$a;
                break;
            default:
                $stack[] = $token;
                break;
            }
        }
        return array_pop($stack);
    }

    /**
     * Calculate a simple mathematical expression. Supported operators are +, -, *, /, ^.
     * You can use ( and ) to group expressions together.
     *
     * @param string $expr
     * @return float|int
     * @throws RuntimeError
     */
    public static function calc($expr)
    {
        return self::calc_rpn(self::shunting_yard($expr));
    }
}
