<?php

namespace Jdlx\Traits;

trait FieldDescriptor
{
    private static $booleanFields = ['editable', 'readOnly', 'writeOnly', 'sortable', 'filterable'];

    public static function getFieldConfigurations()
    {
        $fields = [];
        foreach (self::$fieldSetup as $name => $field) {
            $values = [];
            foreach (self::$booleanFields as $attr) {
                $values[$attr] = self::resourceFieldHasAttribute($name, $attr);
            }

            foreach ($field as $k => $value) {
                if (!is_numeric($k)) {
                    $values[$k] = $value;
                }
            }
            $fields[$name] = $values;
        }
        return $fields;
    }

    public static function getFieldSetup($field)
    {
        return self::$fieldSetup[$field] ?? [];
    }

    public static function isReadable($fieldName)
    {
        return !self::resourceFieldHasAttribute($fieldName, 'writeOnly');
    }

    public static function isWritable($fieldName)
    {
        return !self::resourceFieldHasAttribute($fieldName, 'readOnly');
    }

    public static function readOnly($fieldName)
    {
        return self::resourceFieldHasAttribute($fieldName, 'readOnly');
    }

    public static function writeOnly($fieldName)
    {
        return self::resourceFieldHasAttribute($fieldName, 'writeOnly');
    }

    public static function editable($fieldName)
    {
        return self::resourceFieldHasAttribute($fieldName, 'editable');
    }

    public static function sortable($fieldName)
    {
        return self::resourceFieldHasAttribute($fieldName, 'sortable');
    }

    public static function filterable($fieldName)
    {
        return self::resourceFieldHasAttribute($fieldName, 'filterable');
    }

    public static function getReadableFields()
    {
        return self::filterByMissingAttribute('writeOnly');
    }

    public static function getWritableFields()
    {
        return self::filterByMissingAttribute('readOnly');
    }

    public static function getEditableFields()
    {
        return self::filterByAttribute('editable');
    }

    public static function getFilterableFields()
    {
        return self::filterByMissingAttribute('noFilter');
    }

    public static function getSortableFields()
    {
        return self::filterByAttribute('sortable');
    }

    protected static function filterByAttribute($name, $entry = false)
    {

        $results = array_filter(self::$fieldSetup, function ($v) use ($name) {
            return in_array($name, $v);
        });

        if (!$entry) {
            return array_keys($results);
        }

        return $results;
    }

    protected static function filterByMissingAttribute($name, $entry = false)
    {

        $results = array_filter(self::$fieldSetup, function ($v) use ($name) {
            return !in_array($name, $v);
        });

        if (!$entry) {
            return array_keys($results);
        }

        return $results;
    }

    protected static function resourceFieldHasAttribute($field, $name)
    {
        $attr = self::getFieldSetup($field);
        return in_array($name, $attr);
    }
}
