<?php
namespace Diggin\RobotRules\Rules\Txt;
use Diggin\RobotRules\Rules\Txt\LineEntity as Line;

class RecordEntity implements \ArrayAccess
{
    private $fields = array();
    
    public function offsetExists($offset)
    {
        return isset($this->fields[strtolower($offset)]);
    }

    public function offsetSet($offset, $lines)
    {
        $this->fields[$offset] = $lines;
    }

    public function offsetGet($offset)
    {
        //@todo not strtowloer,CamelUppder
        
        if (array_key_exists(strtolower($offset), $this->fields)) {
            return $this->fields[strtolower($offset)];
        } else {
            return null;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    public function append(Line $line)
    {
        if (array_key_exists($field = $line->getField(), $this->fields)) {
            $this->fields[$field][count($this->fields[$field])] = $line;
        } else {
            $this->fields[$field][0] = $line;
        }
    }

    /**
     * note: User-Agent must first. 
     * but Allow & Disallow which not define sort
     * http://www.robotstxt.org/norobots-rfc.txt 3.2
     */
    private function fieldsort($x, $y)
    {
        if (strtolower($x) === 'user-agent') return -10;
        if (strtolower($y) === 'user-agent') return 10;

        if (strtolower($x) === 'disallow' && strtolower($y) == 'allow') {
            return -1;
        } else if (strtolower($x) === 'allow' && strtolower($y) == 'disallow') {
            return 1;
        }
        return 0;
    }

    public function toString()
    {
        $fieldstring = "";
        uksort($this->fields, array($this, 'fieldsort'));
        foreach ($this->fields as $field) {
            $fieldstring .= implode("\n", $field);
        }
        return $fieldstring;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
