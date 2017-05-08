<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Model;
use Hobord\LavueCms\Content;

class ContentType extends Model
{
    private $translations_table = 'contents_translations';

    protected $casts = [
        'config' => 'array'
    ];

    protected $fillable = [
        'name',
        'config'
    ];

    public function contents()
    {
        return $this->hasMany(Content::class, 'type_id', 'id');
    }

    public function updateJsonIndexes()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if($driver=='mysql') {
            $indexes = \DB::select(DB::raw("SHOW KEYS FROM $this->translations_table WHERE Key_name LIKE \"%_jsonfield\" "));

            if(array_key_exists('indexes', $this->config)) {
                foreach ($this->config['indexes'] as $index) {
                    if(!array_key_exists($index['index_name'], $indexes)) {
                        $this->addJsonIndex($index['index_name']."_jsonfield", $index['type'], $index['field']);
                    }
                }
                foreach ($indexes as $index) {
                    if(!array_key_exists($index['index_name'], $this->config['indexes'])) {
                        $this->deleteJsonIndex($index['field'], $index['type'], $index['index_name']."_jsonfield");
                    }
                }
            }
        }
    }

    public function addJsonIndex($index_name, $type, $field)
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if($driver=='mysql') {
            $index_name .= "_jsonfield";

            //Create virtual column from extracted json
            $sql_create_virtual_column = "ALTER TABLE $this->translations_table ADD $index_name $type 
                AS (JSON_UNQUOTE($field)) STORED";

            \DB::statement(($sql_create_virtual_column));

            //Create INDEX
            $sql_create_index = "ALTER TABLE $this->translations_table ADD INDEX ($index_name)";
            \DB::statement(($sql_create_index));
        }
    }

    public function deleteJsonIndex($index_name)
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if($driver=='mysql') {
            $index_name .= "_jsonfield";
            \DB::statement(\DB::raw("DROP INDEX $index_name ON $this->translations_table"));
            \DB::statement(\DB::raw("ALTER TABLE $this->translations_table DROP COLUMN $index_name"));
        }
    }
}