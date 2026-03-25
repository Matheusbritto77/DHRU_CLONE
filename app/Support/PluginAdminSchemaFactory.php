<?php

namespace App\Support;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PluginAdminSchemaFactory
{
    /**
     * @param  array<int, array<string, mixed>>  $schema
     * @return array<int, Component>
     */
    public static function build(array $schema, ?string $prefix = null): array
    {
        return collect($schema)
            ->map(fn (array $node): Component => self::makeNode($node, $prefix))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeNode(array $node, ?string $prefix = null): Component
    {
        $type = Arr::get($node, 'type', 'text');

        return match ($type) {
            'section' => self::makeSection($node, $prefix),
            'grid' => self::makeGrid($node, $prefix),
            'group' => self::makeGroup($node, $prefix),
            'fieldset' => self::makeFieldset($node, $prefix),
            'tabs' => self::makeTabs($node, $prefix),
            'tab' => self::makeTab($node, $prefix),
            'repeater' => self::makeRepeater($node, $prefix),
            default => self::makeField($node, $prefix),
        };
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeSection(array $node, ?string $prefix): Section
    {
        $component = Section::make(Arr::get($node, 'label', Arr::get($node, 'heading')));
        $component->schema(self::build(Arr::get($node, 'schema', []), $prefix));

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeGrid(array $node, ?string $prefix): Grid
    {
        $component = Grid::make(Arr::get($node, 'columns', 2));
        $component->schema(self::build(Arr::get($node, 'schema', []), $prefix));

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeGroup(array $node, ?string $prefix): Group
    {
        $component = Group::make();
        $component->schema(self::build(Arr::get($node, 'schema', []), $prefix));

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeFieldset(array $node, ?string $prefix): Fieldset
    {
        $component = Fieldset::make(Arr::get($node, 'label', 'Grupo'));
        $component->schema(self::build(Arr::get($node, 'schema', []), $prefix));

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeTabs(array $node, ?string $prefix): Tabs
    {
        $tabs = collect(Arr::get($node, 'tabs', []))
            ->map(fn (array $tab): Tab => self::makeTab($tab, $prefix))
            ->all();

        $component = Tabs::make(Arr::get($node, 'label'));
        $component->tabs($tabs);

        if ($activeTab = Arr::get($node, 'activeTab')) {
            $component->activeTab((int) $activeTab);
        }

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeTab(array $node, ?string $prefix): Tab
    {
        $component = Tab::make(Arr::get($node, 'label', 'Aba'));
        $component->schema(self::build(Arr::get($node, 'schema', []), $prefix));

        if ($icon = Arr::get($node, 'icon')) {
            $component->icon($icon);
        }

        return self::applyContainerProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeRepeater(array $node, ?string $prefix): Repeater
    {
        $name = self::requireName($node);
        $component = Repeater::make(self::statePath($name, $prefix));
        $component->schema(self::build(Arr::get($node, 'schema', [])));

        if ($label = Arr::get($node, 'label')) {
            $component->label($label);
        }

        if ($helperText = Arr::get($node, 'helperText')) {
            $component->helperText($helperText);
        }

        if (Arr::get($node, 'defaultItems') !== null) {
            $component->defaultItems((int) Arr::get($node, 'defaultItems'));
        }

        if (Arr::get($node, 'reorderable') === false) {
            $component->reorderable(false);
        }

        if (Arr::get($node, 'reorderableWithButtons')) {
            $component->reorderableWithButtons();
        }

        if (Arr::get($node, 'collapsible')) {
            $component->collapsible();
        }

        if (Arr::get($node, 'cloneable')) {
            $component->cloneable();
        }

        if (Arr::get($node, 'addActionLabel')) {
            $component->addActionLabel(Arr::get($node, 'addActionLabel'));
        }

        if (Arr::get($node, 'grid') !== null) {
            $component->grid((int) Arr::get($node, 'grid'));
        }

        return self::applyBaseProps($component, $node);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected static function makeField(array $node, ?string $prefix): Component
    {
        $type = Arr::get($node, 'type', 'text');
        $name = self::requireName($node);
        $statePath = self::statePath($name, $prefix);

        $component = match ($type) {
            'text' => TextInput::make($statePath),
            'textarea' => Textarea::make($statePath),
            'toggle' => Toggle::make($statePath),
            'select' => Select::make($statePath)->options(Arr::get($node, 'options', [])),
            'key_value' => KeyValue::make($statePath),
            'tags' => TagsInput::make($statePath),
            'file', 'image' => FileUpload::make($statePath)
                ->disk(Arr::get($node, 'disk', 'public'))
                ->directory(Arr::get($node, 'directory', 'plugin_uploads'))
                ->visibility('public'),
            'placeholder' => Placeholder::make($statePath)->content(Arr::get($node, 'content', '')),
            'rich_editor' => RichEditor::make($statePath),
            'date' => DatePicker::make($statePath),
            'datetime' => DateTimePicker::make($statePath),
            'time' => TimePicker::make($statePath),
            'color' => ColorPicker::make($statePath),
            'toggle_buttons' => ToggleButtons::make($statePath)->options(Arr::get($node, 'options', [])),
            'radio' => Radio::make($statePath)->options(Arr::get($node, 'options', [])),
            'checkbox_list' => CheckboxList::make($statePath)->options(Arr::get($node, 'options', [])),
            default => throw new InvalidArgumentException("Tipo de campo não suportado: {$type}"),
        };

        self::configureField($component, $node, $type);

        return $component;
    }

    protected static function configureField(Component $component, array $node, string $type): void
    {
        self::applyBaseProps($component, $node);

        if ($component instanceof TextInput) {
            if ($placeholder = Arr::get($node, 'placeholder')) {
                $component->placeholder($placeholder);
            }

            if (Arr::get($node, 'numeric')) {
                $component->numeric();
            }
        }

        if ($component instanceof Textarea) {
            $component->rows((int) Arr::get($node, 'rows', 5));
        }

        if ($component instanceof Select && Arr::get($node, 'multiple')) {
            $component->multiple();
        }

        if ($component instanceof CheckboxList && Arr::get($node, 'columns')) {
            $component->columns((int) Arr::get($node, 'columns'));
        }

        if ($component instanceof Radio && Arr::get($node, 'inline')) {
            $component->inline();
        }

        if ($component instanceof ToggleButtons && Arr::get($node, 'inline')) {
            $component->inline();
        }

        if ($component instanceof FileUpload) {
            if ($type === 'image') {
                $component->image();
            }

            if (Arr::get($node, 'multiple')) {
                $component->multiple();
            }

            if ($acceptedFileTypes = Arr::get($node, 'acceptedFileTypes')) {
                $component->acceptedFileTypes($acceptedFileTypes);
            }
        }

        if ($component instanceof KeyValue) {
            $component
                ->keyLabel(Arr::get($node, 'keyLabel', 'Chave'))
                ->valueLabel(Arr::get($node, 'valueLabel', 'Valor'));
        }
    }

    protected static function applyBaseProps(Component $component, array $node): Component
    {
        if ($label = Arr::get($node, 'label')) {
            $component->label($label);
        }

        if ($helperText = Arr::get($node, 'helperText')) {
            $component->helperText($helperText);
        }

        if (Arr::get($node, 'required')) {
            $component->required();
        }

        if (Arr::has($node, 'columnSpan')) {
            $component->columnSpan(Arr::get($node, 'columnSpan'));
        }

        if (Arr::has($node, 'columns') && method_exists($component, 'columns')) {
            $component->columns(Arr::get($node, 'columns'));
        }

        if ($id = Arr::get($node, 'id')) {
            $component->id($id);
        }

        return $component;
    }

    protected static function applyContainerProps(Component $component, array $node): Component
    {
        self::applyBaseProps($component, $node);

        if (($description = Arr::get($node, 'description')) && method_exists($component, 'description')) {
            $component->description($description);
        }

        if (($icon = Arr::get($node, 'icon')) && method_exists($component, 'icon')) {
            $component->icon($icon);
        }

        if (Arr::get($node, 'collapsible') && method_exists($component, 'collapsible')) {
            $component->collapsible();
        }

        return $component;
    }

    protected static function requireName(array $node): string
    {
        $name = Arr::get($node, 'name');

        if (! is_string($name) || $name === '') {
            throw new InvalidArgumentException('Cada campo do admin_form_schema precisa de um "name".');
        }

        return $name;
    }

    protected static function statePath(string $name, ?string $prefix = null): string
    {
        return $prefix ? "{$prefix}.{$name}" : $name;
    }
}
