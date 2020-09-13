<?php

namespace kch42\ste;

class Scope implements \ArrayAccess
{
    /** @var self|null */
    private $parent = null;

    /** @var array */
    public $vars = [];

    /**
     * @param string $name
     * @return string[]
     * @throws RuntimeError
     */
    private static function parse_name(string $name): array
    {
        $remain = $name;
        $fields = [];

        while ($remain !== "") {
            $br_open = strpos($remain, '[');
            if ($br_open === false) {
                $fields[] = $remain;
                break;
            }

            $br_close = strpos($remain, ']', $br_open);
            if ($br_close === false) {
                throw new RuntimeError("Invalid varname \"$name\". Missing closing \"]\".");
            }

            $fields[] = substr($remain, 0, $br_open);

            $field = substr($remain, $br_open+1, $br_close-$br_open-1);
            $more = substr($remain, $br_close+1);

            if (strpos($field, '[') !== false) {
                throw new RuntimeError("A variable field must not contain a '[' character.");
            }

            if ((strlen($more) > 0) && ($more[0] !== '[')) {
                // TODO: better error message, not very non-programmer friendly...
                throw new RuntimeError("A variable name must be of format name('[' name ']')*.");
            }

            $remain = $field . $more;
        }

        return $fields;
    }

    /**
     * @param string $name
     * @param bool $localonly
     * @return mixed A reference to the resolved variable
     * @throws VarNotInScope
     */
    private function &get_topvar_reference(string $name, bool $localonly)
    {
        if (array_key_exists($name, $this->vars)) {
            $ref = &$this->vars[$name];
            return $ref;
        }

        if ((!$localonly) && ($this->parent !== null)) {
            $ref = &$this->parent->get_topvar_reference($name, $localonly);
            return $ref;
        }

        throw new VarNotInScope();
    }

    /**
     * @param string $name
     * @param bool $create_if_not_exist
     * @param bool $localonly
     * @return mixed A reference to the resolved variable
     * @throws RuntimeError
     */
    public function &get_var_reference(string $name, bool $create_if_not_exist, $localonly=false)
    {
        $nullref = null;

        $fields = self::parse_name($name);
        if (count($fields) == 0) {
            return $nullref; // TODO: or should we throw an exception here?
        }

        $first = $fields[0];

        $ref = null;
        try {
            $ref = &$this->get_topvar_reference($first, $localonly);
        } catch (VarNotInScope $e) {
            if ($create_if_not_exist) {
                $this->vars[$first] = (count($fields) > 0) ? [] : "";
                $ref = &$this->vars[$first];
            } else {
                return $nullref;
            }
        }

        for ($i = 1; $i < count($fields); $i++) {
            $field = $fields[$i];

            if (!is_array($ref)) {
                return $nullref;
            }

            if (!array_key_exists($field, $ref)) {
                if (!$create_if_not_exist) {
                    return $nullref;
                }

                if ($i < count($fields) - 1) {
                    $ref[$field] = [];
                } else {
                    $ref[$field] = "";
                }
            }

            $ref = &$ref[$field];
        }

        return $ref;
    }

    /**
     * @param string $name
     * @param mixed $val
     * @throws RuntimeError
     */
    public function set_var_by_name(string $name, $val): void
    {
        $ref = &$this->get_var_reference($name, true);
        $ref = $val;
    }

    /**
     * @param string $name
     * @param mixed $val
     * @throws RuntimeError
     */
    public function set_local_var(string $name, $val): void
    {
        $ref = &$this->get_var_reference($name, true, true);
        $ref = $val;
    }

    /**
     * @param string $name
     * @return mixed Returns an empty string, if not found or unset
     * @throws RuntimeError
     */
    public function get_var_by_name(string $name)
    {
        $ref = $this->get_var_reference($name, false);
        return $ref === null ? "" : $ref;
    }

    /**
     * @return self
     */
    public function new_subscope(): self
    {
        $o = new self();
        $o->parent = $this;
        return $o;
    }

    /* implementing ArrayAccess */

    public function offsetSet($offset, $value)
    {
        $this->set_var_by_name($offset, $value);
    }
    public function offsetGet($offset)
    {
        return $this->get_var_by_name($offset);
    }
    public function offsetExists($offset)
    {
        try {
            $this->get_topvar_reference($offset, false);
            return true;
        } catch (VarNotInScope $e) {
            return false;
        }
    }
    public function offsetUnset($offset)
    {
        unset($this->vars[$offset]);
    }
}
