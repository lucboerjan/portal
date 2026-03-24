<?php

/**
 * FilaCheck Pro Configuration
 *
 * Customize the behavior of FilaCheck Pro rules.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Too Many Columns Rule
    |--------------------------------------------------------------------------
    |
    | Maximum number of visible table columns before a warning is triggered.
    | Columns with `toggleable(isToggledHiddenByDefault: true)` are excluded.
    |
    */
    'too-many-columns' => [
        'max_columns' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Flat Form Overload Rule
    |--------------------------------------------------------------------------
    |
    | Maximum number of form fields without any layout grouping (Section, Tabs,
    | Fieldset, etc.) before a warning is triggered.
    |
    */
    'flat-form-overload' => [
        'max_fields' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enum Missing Filament Interfaces Rule
    |--------------------------------------------------------------------------
    |
    | Which Filament interfaces enums should implement when cast in models.
    | Valid values: HasLabel, HasColor, HasIcon, HasDescription
    |
    */
    /*
    |--------------------------------------------------------------------------
    | Table Without Searchable Columns Rule
    |--------------------------------------------------------------------------
    |
    | When enabled, tables inside Filament widget classes will be skipped
    | by this rule, since widgets are often small supplementary displays
    | where searchable columns are not needed.
    |
    */
    'table-without-searchable-columns' => [
        'skip_widgets' => false,
    ],

    'enum-missing-filament-interfaces' => [
        'required_interfaces' => ['HasLabel'],
    ],
];
