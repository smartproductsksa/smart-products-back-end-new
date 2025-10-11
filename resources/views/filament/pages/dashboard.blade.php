<x-filament-panels::page>
    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
            ]
        "
        :widgets="$this->getWidgets()"
    />
</x-filament-panels::page>
