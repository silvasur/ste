<?php

namespace kch42\ste;

class STEStandardLibrary
{
    public static function _register_lib($ste)
    {
        foreach (get_class_methods(__CLASS__) as $method) {
            if ($method[0] != "_") {
                $ste->register_tag($method, array(__CLASS__, $method));
            }
        }
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     */
    public static function escape($ste, $params, $sub)
    {
        if ($ste->evalbool(@$params["lines"])) {
            return nl2br(htmlspecialchars(str_replace("\r\n", "\n", $sub($ste))));
        } else {
            return htmlspecialchars($sub($ste));
        }
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     */
    public static function strlen($ste, $params, $sub)
    {
        return strlen($sub($ste));
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     * @throws RuntimeError
     */
    public static function arraylen($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:arraylen>.");
        }
        $a = $ste->get_var_by_name($params["array"]);
        return (is_array($a)) ? count($a) : "";
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @throws RuntimeError
     */
    public static function inc($ste, $params, $sub)
    {
        if (empty($params["var"])) {
            throw new RuntimeError("Missing var parameter in <ste:inc>.");
        }
        $ref = &$ste->get_var_reference($params["var"], true);
        $ref++;
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @throws RuntimeError
     */
    public static function dec($ste, $params, $sub)
    {
        if (empty($params["var"])) {
            throw new RuntimeError("Missing var parameter in <ste:dec>.");
        }
        $ref = &$ste->get_var_reference($params["var"], true);
        $ref--;
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     */
    public static function date($ste, $params, $sub)
    {
        return @strftime($sub($ste), empty($params["timestamp"]) ? @time() : (int) $params["timestamp"]);
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     * @throws RuntimeError
     */
    public static function in_array($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:in_array>.");
        }
        $ar = &$ste->get_var_reference($params["array"], false);
        if (!is_array($ar)) {
            return "";
        }
        return in_array($sub($ste), $ar) ? "y" : "";
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @return string
     * @throws RuntimeError
     */
    public static function join($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:join>.");
        }
        return implode($sub($ste), $ste->get_var_by_name($params["array"]));
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @throws RuntimeError
     */
    public static function split($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:split>.");
        }
        if (empty($params["delim"])) {
            throw new RuntimeError("Missing delim parameter in <ste:split>.");
        }
        $ste->set_var_by_name($params["array"], explode($params["delim"], $sub($ste)));
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @throws RuntimeError
     */
    public static function array_add($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:array_add>.");
        }

        $ar = &$ste->get_var_reference($params["array"], true);
        if (empty($params["key"])) {
            $ar[] = $sub($ste);
        } else {
            $ar[$params["key"]] = $sub($ste);
        }
    }

    /**
     * @param STECore $ste
     * @param array $params
     * @param callable $sub
     * @throws RuntimeError
     */
    public static function array_filter($ste, $params, $sub)
    {
        if (empty($params["array"])) {
            throw new RuntimeError("Missing array parameter in <ste:array_filter>.");
        }

        $ar = $ste->get_var_by_name($params["array"]);
        if (!is_array($ar)) {
            throw new RuntimeError("Variable at 'array' is not an array.");
        }

        $keys = array_keys($ar);

        if (!empty($params["keep_by_keys"])) {
            $keep_by_keys = &$ste->get_var_reference($params["keep_by_keys"], false);
            if (!is_array($keep_by_keys)) {
                throw new RuntimeError("Variable at 'keep_by_keys' is not an array.");
            }
            $delkeys = array_filter($keys, function ($k) use ($keep_by_keys) {
                return !in_array($k, $keep_by_keys);
            });
            foreach ($delkeys as $dk) {
                unset($ar[$dk]);
            }
            $keys = array_keys($ar);
        }
        if (!empty($params["keep_by_values"])) {
            $keep_by_values = &$ste->get_var_reference($params["keep_by_values"], false);
            if (!is_array($keep_by_values)) {
                throw new RuntimeError("Variable at 'keep_by_values' is not an array.");
            }
            $ar = array_filter($ar, function ($v) use ($keep_by_values) {
                return in_array($v, $keep_by_values);
            });
            $keys = array_keys($ar);
        }
        if (!empty($params["delete_by_keys"])) {
            $delete_by_keys = &$ste->get_var_reference($params["delete_by_keys"], false);
            if (!is_array($delete_by_keys)) {
                throw new RuntimeError("Variable at 'delete_by_keys' is not an array.");
            }
            $delkeys = array_filter($keys, function ($k) use ($delete_by_keys) {
                return in_array($k, $delete_by_keys);
            });
            foreach ($delkeys as $dk) {
                unset($ar[$dk]);
            }
            $keys = array_keys($ar);
        }
        if (!empty($params["delete_by_values"])) {
            $delete_by_values = &$ste->get_var_reference($params["delete_by_values"], false);
            if (!is_array($delete_by_values)) {
                throw new RuntimeError("Variable at 'delete_by_values' is not an array.");
            }
            $ar = array_filter($ar, function ($v) use ($delete_by_values) {
                return !in_array($v, $delete_by_values);
            });
            $keys = array_keys($ar);
        }

        $ste->set_var_by_name($params["array"], $ar);
    }
}
