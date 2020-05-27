<?php

namespace App\Helpers\Translation;

use Illuminate\Support\Facades\DB;

trait TranslationTrait
{
    public $translation_table = 'translations';
    public $translation_columns = ['name'];
    public $languages = ['en', 'ru'];
    public $default_language = 'az';
    public $translation_many_related_name = true;
    public $translation_many_table_name = true;
    public $translation_auto_delete = false;
    private $translation_start = false;

    public function getLanguage(): string
    {
        $lang = app()->getLocale();
        if (!in_array($lang, $this->languages)) {
            return '';
        }
        return $lang;
    }

    public function scopeT($q)
    {
        if ($this->getLanguage() !== '') {
            $q->translation();
        }
    }

    public static function t()
    {
        $model = new static;
        if ($model->getLanguage() !== '') {
            $model->translation_start = true;
            return $model->translationNewQuery();
        }
        return $model->newQuery();
    }

    public function newEloquentBuilder($builder)
    {
        if($this->translation_start){
            return new TranslationBuilder($builder);
        }
        return parent::newEloquentBuilder($builder);
    }

    public function translationNewQuery()
    {
        $query = parent::newQuery();
        $query->translation();
        return $query;
    }

    public function scopeTranslation($q)
    {
        foreach ($this->translation_columns as $related_name) {
            $q->translationJoin($related_name);
        }
        $q->selectTranslations();
    }

    public function getTranslationTableNameAs(string $related_name): array
    {
        $table_name_as = $this->translation_table . '_' . $related_name;
        $table_name = $this->translation_table . ' as ' . $table_name_as;
        return [$table_name_as, $table_name];
    }

    public function scopeTranslationJoin($q, string $related_name)
    {
        [$table_name_as, $table_name] = $this->getTranslationTableNameAs($related_name);
        $q->leftJoin(
            $table_name,
            function ($q) use ($related_name, $table_name_as) {
                $q->on($table_name_as . '.related_id', $this->getTable() . '.id');
                $this->translationWhenWhere($q, $related_name);
            }
        );
    }

    public function translationWhenWhere($query, $related_name)
    {
        [$table_name_as] = $this->getTranslationTableNameAs($related_name);
        $query->when($this->translation_many_table_name, function ($q) use ($table_name_as) {
            $q->where($table_name_as . '.table_name', $this->getTable());
        })
        ->when($this->translation_many_related_name, function ($q) use ($related_name, $table_name_as) {
            $q->where($table_name_as . '.related_name', $related_name);
        });
    }

    public function delete()
    {
        if ($this->translation_auto_delete || $this->translation_start) {
            $this->deleteTranslation();
        }

        return parent::delete();
    }

    public function scopeSelectTranslations($q)
    {
        $lang = $this->getLanguage();
        $selected[] =  $this->getTable() . '.*';
        foreach ($this->translation_columns as $related_name) {
            [$table_name_as] = $this->getTranslationTableNameAs($related_name);
            $selected[] = $table_name_as . '.' . $lang . ' as ' . $related_name;
        }
        $q->select($selected);
    }

    public function scopeAllTranslations($q)
    {
        foreach ($this->translation_columns as $related_name) {
            $q->translationJoin($related_name);
        }
        $q->selectAllTranslations();
    }

    public function scopeSelectAllTranslations($q)
    {
        $selected = [$this->getTable() . '.' . $this->primaryKey];
        foreach ($this->translation_columns as $related_name) {
            [$table_name_as] = $this->getTranslationTableNameAs($related_name);
            $selected[] = $this->getTable() . '.' . $related_name;
            foreach ($this->languages as $language) {
                $selected[] = $table_name_as . '.' . $language . ' as ' . $related_name . '_' . $language;
            }
        }
        $q->select($selected);
    }

    public function changeSelect(array $args): array
    {
        if ($lang = $this->getLanguage()) {
            foreach ($args as $key => $arg) {
                $arg = str_replace($this->getTable().'.','',$arg);
                $arg = explode(' as ', $arg);
                if (in_array($arg[0], $this->translation_columns)) {
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
        if ($lang = $this->getLanguage()) {
            $column = str_replace($this->getTable().'.','',$column);
            if (in_array($column, $this->translation_columns)) {
                [$table_name_as] = $this->getTranslationTableNameAs($column);
                $column = $table_name_as . '.' . $lang;
            }
        }
        return $column;
    }

    // ----------------------------
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
        DB::table($this->translation_table)
            ->when($this->translation_many_table_name, function ($q) {
                $q->where('table_name', $this->getTable());
            })
            ->where('related_id', $this->getKey())
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
        $basic_data['related_id'] = $this->getKey();
        if ($this->translation_many_table_name) {
            $basic_data['table_name'] = $this->getTable();
        }
        foreach ($args as $related_name => $arg) {
            if ($this->translation_many_related_name) {
                $basic_data['related_name'] = $related_name;
            }
            $inserted_data = [];
            foreach ($arg as $key => $value) {
                $inserted_data[$key] = $value;
            }
            if ($update) {
                DB::table($this->translation_table)->updateOrInsert($basic_data, $inserted_data);
            } else {
                DB::table($this->translation_table)->insert(array_merge($basic_data, $inserted_data));
            }
        }
    }

    public function getRequestTranslationArguments(): array
    {
        $args = [];
        foreach ($this->translation_columns as $related_name) {
            foreach ($this->languages as $language) {
                $request = request($related_name . '_' . $language);
                if (is_string($request)) {
                    $args[$related_name][$language] = $request;
                }
            }
        }
        return $args;
    }
}
