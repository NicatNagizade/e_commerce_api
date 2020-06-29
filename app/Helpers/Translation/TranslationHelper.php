<?php

namespace App\Helpers\Translation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TranslationHelper
{
    private $model;
    private static $languages = ['en', 'ru'];
    private static $get_language;
    private static $default_language = 'az';

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->checkTrait();
    }
    public function checkTrait(): void
    {
        foreach (class_uses($this->model) as $trait) {
            if ($trait === 'App\Helpers\Translation\TranslationTrait') {
                return;
            }
        }
        throw new \Exception('(---NN---)Add TranslationTrait to ' . get_class($this->model));
    }
    public function getModel(): Model
    {
        return $this->model;
    }
    public static function getLanguage(): string
    {
        if (!isset(static::$get_language)) {
            $lang = app()->getLocale();
            if (in_array($lang, static::$languages)) {
                static::$get_language = $lang;
            } else {
                static::$get_language = static::$default_language;
            }
        }
        return (string) static::$get_language;
    }
    public static function getDefaultLanguage(): string
    {
        return static::$default_language;
    }
    public static function checkLanguage(): bool
    {
        return static::getLanguage() !== static::getDefaultLanguage();
    }
    public function allTranslations(): Builder
    {
        $query = $this->selectAllTranslations();
        foreach ($this->model->translationColumns() as $related_name) {
            [$table_name_as, $table_name] = $this->model->getTranslationTableNameAs($related_name);
            $query->leftJoin(
                $table_name,
                function ($q) use ($related_name, $table_name_as) {
                    $q->on($table_name_as . '.related_id', $this->model->getTable() . '.id');
                    $this->model->translationWhenWhere($q, $related_name);
                }
            );
        }
        return $query;
    }
    public function selectAllTranslations(): Builder
    {
        $selected = [$this->model->getTable() . '.id'];
        foreach ($this->model->translationColumns() as $related_name) {
            [$table_name_as] = $this->model->getTranslationTableNameAs($related_name);
            $selected[] = $this->model->getTable() . '.' . $related_name;
            foreach (static::$languages as $language) {
                $selected[] = $table_name_as . '.' . $language . ' as ' . $related_name . '_' . $language;
            }
        }
        return $this->model->select($selected);
    }
    public function allTranslationToArray(object $datas): object
    {
        foreach ($datas as $key => $data) {
            foreach ($this->model->translationColumns() as $column) {
                $arr = [];
                $arr[static::getDefaultLanguage()] = $data->$column;
                foreach (static::$languages as $lang) {
                    $column_lang = $column . '_' . $lang;
                    $arr[$lang] = $data->$column_lang;
                    unset($datas[$key]->$column_lang);
                }
                $datas[$key]->$column = $arr;
            }
        }
        return $datas;
    }
    // ---
    public function createTranslation(): array
    {
        $args = $this->getRequestTranslationArguments();
        $this->tAttach($args);
        return $args;
    }
    public function updateTranslation(): array
    {
        $args = $this->getRequestTranslationArguments();
        $this->tSync($args);
        return $args;
    }
    public function deleteTranslation(): void
    {
        DB::table($this->model->translationTable())
            ->when($this->model->translationManyTableName(), function ($q) {
                $q->where('table_name', $this->model->getTable());
            })
            ->where('related_id', $this->model->getKey())
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
        $basic_data['related_id'] = $this->model->getKey();
        if ($this->model->translationManyTableName()) {
            $basic_data['table_name'] = $this->model->getTable();
        }
        foreach ($args as $related_name => $arg) {
            if ($this->model->translationManyRelatedName()) {
                $basic_data['related_name'] = $related_name;
            }
            $inserted_data = [];
            foreach ($arg as $key => $value) {
                $inserted_data[$key] = $value;
            }
            if ($update) {
                DB::table($this->model->translationTable())->updateOrInsert($basic_data, $inserted_data);
            } else {
                DB::table($this->model->translationTable())->insert(array_merge($basic_data, $inserted_data));
            }
        }
    }
    public function getRequestTranslationArguments(): array
    {
        $args = [];
        foreach ($this->model->translationColumns() as $related_name) {
            if (is_array(request($related_name))) {
                foreach (request($related_name) as $lang => $value) {
                    if (is_string($value)) {
                        if (in_array($lang, static::$languages)) {
                            $args[$related_name][$lang] = $value;
                        }
                    }
                }
            }
        }
        return $args;
    }
}
