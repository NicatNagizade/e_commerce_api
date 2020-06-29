<?php

namespace App\Helpers\Translation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait TranslationTrait
{
    public function translationTable(): string
    {
        return $this->translation_table ?? 'translations';
    }
    public function translationColumns(): array
    {
        return $this->translation_columns ?? ['name'];
    }
    public function translationManyRelatedName(): bool
    {
        return $this->translation_many_related_name ?? true;
    }
    public function translationManyTableName(): bool
    {
        return $this->translation_many_table_name ?? true;
    }
    public function translationAuto(): bool
    {
        if (!empty($this->translationColumns())) {
            return $this->translation_auto ?? true;
        }
        return false;
    }
    public function translationCached(): bool
    {
        return $this->translation_cached ?? false;
    }
    public function translationBeginCheck(): bool
    {
        if (TranslationHelper::checkLanguage()) {
            if ($this->translationAuto()) {
                return true;
            }
        }
        return false;
    }
    public static function bootTranslationTrait(): void
    {
        static::deleting(function ($model) {
            if ($model->translationAuto()) {
                DB::table($model->translationTable())
                    ->when($model->translationManyTableName(), function ($q) use ($model) {
                        $q->where('table_name', $model->getTable());
                    })
                    ->where('related_id', $model->getKey())
                    ->delete();
            }
        });
    }
    // manual translation model and change translatin columns
    public static function t(array $columns = []): Model
    {
        $model = new static;
        $model->translation_auto = true;
        if (!empty($columns)) {
            $model->translation_columns = $columns;
        }
        return $model;
    }
    // manual translation model builder
    public static function translation(Builder $query): Builder
    {
        $model = new static;
        $model->translation_auto = true;
        if ($model->translationBeginCheck()) {
            $sub_query =  $model->translationSub();
            $query->fromRaw($sub_query);
        }
        return $query;
    }
    public function newQuery(): Builder
    {
        $query = parent::newQuery();
        if ($this->translationBeginCheck()) {
            $sub_query =  $this->translationSub();
            $query->fromRaw($sub_query);
        }
        return $query;
    }
    // --- helper methods
    public function translationSub(): string
    {
        $q = DB::table($this->getTable());
        $this->translationQuery($q);
        $addSlashes = str_replace('?', "'?'", $q->toSql());
        $sub_query = vsprintf(str_replace('?', '%s', $addSlashes), $q->getBindings());
        return '(' . $sub_query . ') as ' . $this->getTable();
    }
    public function translationQuery(QueryBuilder $q): void
    {
        foreach ($this->translationColumns() as $related_name) {
            $this->translationJoin($q, $related_name);
        }
        $this->selectTranslations($q);
    }
    public function getTranslationTableNameAs(string $related_name): array
    {
        $table_name_as = $this->translationTable() . '_' . $related_name;
        $table_name = $this->translationTable() . ' as ' . $table_name_as;
        return [$table_name_as, $table_name];
    }
    public function translationWhenWhere(QueryBuilder $query, string $related_name): void
    {
        [$table_name_as] = $this->getTranslationTableNameAs($related_name);
        $query->when($this->translationManyTableName(), function ($q) use ($table_name_as) {
            $q->where($table_name_as . '.table_name', $this->getTable());
        })
            ->when($this->translationManyRelatedName(), function ($q) use ($related_name, $table_name_as) {
                $q->where($table_name_as . '.related_name', $related_name);
            });
    }
    public function selectTranslations(QueryBuilder $q): void
    {
        $columns = [];
        if ($this->translationCached()) {
            $columns = cache()->rememberForever('all_columns_' . $this->getTable(), function () {
                return $this->getColumnListing();
            });
        } else {
            $columns = $this->getColumnListing();
        }
        $columns = $this->translationChangeSelect($columns);
        $q->select($columns);
    }
    public function translationJoin(QueryBuilder $q, string $related_name): void
    {
        [$table_name_as, $table_name] = $this->getTranslationTableNameAs($related_name);
        $q->leftJoin(
            $table_name,
            function ($q) use ($related_name, $table_name_as) {
                $q->on($table_name_as . '.related_id', $this->getTable() . '.' . $this->getKeyName());
                $this->translationWhenWhere($q, $related_name);
            }
        );
    }
    public function getColumnListing(): array
    {
        return array_map(function ($column) {
            return $this->getTable() . '.' . $column;
        }, Schema::getColumnListing($this->getTable()));
    }
    public function translationChangeSelect(array $columns): array
    {
        $lang = TranslationHelper::getLanguage();
        foreach ($columns as $key => $column) {
            $column = str_replace($this->getTable() . '.', '', $column);
            if (in_array($column, $this->translationColumns())) {
                [$table_name_as] = $this->getTranslationTableNameAs($column);
                $columns[$key] = $table_name_as . '.' . $lang . ' as ' . $column;
            }
        }
        return $columns;
    }
}
