<?php

namespace App\Helpers\Translation;

use Illuminate\Support\Facades\DB;

class TranslationHelper{
    private $model;
    private $model_id;
    private static $languages = ['en', 'ru'];
    private static $default_language = 'az';
    private static $get_language;

    public function __construct($model = null,int $id = 0)
    {
        $this->model = $model;
        $this->model_id = $id;
    }

    public static function getLanguage(): string
    {
        if(!isset(static::$get_language)){
            $lang = app()->getLocale();
            if (!in_array($lang, static::$languages)) {
                static::$get_language = '';
            } else {
                static::$get_language = $lang;
            }
        }
        return static::$get_language;
    }

    public function allTranslations()
    {
        $query = $this->selectAllTranslations();
        foreach ($this->model->translation_columns as $related_name) {
            [$table_name_as, $table_name] = $this->getTranslationTableNameAs($related_name);
            $query->leftJoin(
                $table_name,
                function ($q) use ($related_name, $table_name_as) {
                    $q->on($table_name_as . '.related_id', $this->model->getTable() . '.id');
                    $this->translationWhenWhere($q, $related_name);
                }
            );
        }
        return $query;
    }

    public function selectAllTranslations()
    {
        $selected = [$this->model->getTable() . '.id'];
        foreach ($this->model->translation_columns as $related_name) {
            [$table_name_as] = $this->getTranslationTableNameAs($related_name);
            $selected[] = $this->model->getTable() . '.' . $related_name;
            foreach (static::$languages as $language) {
                $selected[] = $table_name_as . '.' . $language . ' as ' . $related_name . '_' . $language;
            }
        }
        return $this->model->select($selected);
    }

    public function getTranslationTableNameAs(string $related_name): array
    {
        $table_name_as = $this->model->translation_table . '_' . $related_name;
        $table_name = $this->model->translation_table . ' as ' . $table_name_as;
        return [$table_name_as, $table_name];
    }
    
    public function translationWhenWhere($query, $related_name)
    {
        [$table_name_as] = $this->getTranslationTableNameAs($related_name);
        $query->when($this->model->translation_many_table_name, function ($q) use ($table_name_as) {
            $q->where($table_name_as . '.table_name', $this->model->getTable());
        })
        ->when($this->model->translation_many_related_name, function ($q) use ($related_name, $table_name_as) {
            $q->where($table_name_as . '.related_name', $related_name);
        });
    }

    public function selectTranslations($q)
    {
        $lang = static::getLanguage();
        $selected[] =  $this->model->getTable() . '.*';
        foreach ($this->model->translation_columns as $related_name) {
            [$table_name_as] = $this->getTranslationTableNameAs($related_name);
            $selected[] = $table_name_as . '.' . $lang . ' as ' . $related_name;
        }
        $q->select($selected);
    }

    public function translationJoin($q, string $related_name)
    {
        [$table_name_as, $table_name] = $this->getTranslationTableNameAs($related_name);
        $q->leftJoin(
            $table_name,
            function ($q) use ($related_name, $table_name_as) {
                $q->on($table_name_as . '.related_id', $this->model->getTable() . '.'.$this->model->getKeyName());
                $this->translationWhenWhere($q, $related_name);
            }
        );
    }

    public function translation($q)
    {
        foreach ($this->model->translation_columns as $related_name) {
            $this->translationJoin($q, $related_name);
        }
        $this->selectTranslations($q);
    }

    // ---
    
    public function changeSelect(array $args): array
    {
        if ($lang = static::getLanguage()) {
            foreach ($args as $key => $arg) {
                $arg = str_replace($this->model->getTable().'.','',$arg);
                $arg = explode(' as ', $arg);
                if (in_array($arg[0], $this->model->translation_columns)) {
                    [$table_name_as] = $this->getTranslationTableNameAs($arg[0]);
                    if(count($arg) === 2){
                        $args[$key] = $table_name_as . '.' . $lang.' as ' . $arg[1];
                    }else{
                        $args[$key] = $table_name_as . '.' . $lang.' as ' . $arg[0];
                    }
                }
            }
        }
        return $args;
    }

    public function changeWhere(string $column): string
    {
        if ($lang = static::getLanguage()) {
            $replace_column = str_replace($this->model->getTable().'.','',$column);
            if (in_array($replace_column, $this->model->translation_columns)) {
                [$table_name_as] = $this->getTranslationTableNameAs($replace_column);
                $column = $table_name_as . '.' . $lang;
            }
        }
        return $column;
    }

    // ---
    public function createTranslation(): void
    {
        $args = $this->getRequestTranslationArguments();
        $this->tAttach($args);
    }

    public function updateTranslation(): void
    {
        $args = $this->getRequestTranslationArguments();
        $this->tSync($args);
    }

    public function deleteTranslation(): void
    {
        DB::table($this->model->translation_table)
            ->when($this->model->translation_many_table_name, function ($q) {
                $q->where('table_name', $this->model->getTable());
            })
            ->where('related_id', $this->model_id)
            ->delete();
    }

    public function tAttach(array $args): void
    {
        $this->translationUpdateOrInsert($args, false);
    }

    public function tSync(array $args): void
    {
        $this->translationUpdateOrInsert($args, true);
    }

    public function translationUpdateOrInsert(array $args, bool $update = true): void
    {
        $basic_data['related_id'] = $this->model_id;
        if ($this->model->translation_many_table_name) {
            $basic_data['table_name'] = $this->model->getTable();
        }
        foreach ($args as $related_name => $arg) {
            if ($this->model->translation_many_related_name) {
                $basic_data['related_name'] = $related_name;
            }
            $inserted_data = [];
            foreach ($arg as $key => $value) {
                $inserted_data[$key] = $value;
            }
            if ($update) {
                DB::table($this->model->translation_table)->updateOrInsert($basic_data, $inserted_data);
            } else {
                DB::table($this->model->translation_table)->insert(array_merge($basic_data, $inserted_data));
            }
        }
    }

    public function getRequestTranslationArguments(): array
    {
        $args = [];
        foreach ($this->model->translation_columns as $related_name) {
            foreach (static::$languages as $language) {
                $request = request($related_name . '_' . $language);
                if (is_string($request)) {
                    $args[$related_name][$language] = $request;
                }
            }
        }
        return $args;
    }
}