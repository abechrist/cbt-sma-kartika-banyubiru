<x-filament-panels::page>
    <x-filament::panel-content>
        <div class="space-y-6">
            <x-filament::widgets.container>
                <x-filament::widgets.grid>
                    @foreach($widgets as $widget)
                        <x-filament::widgets.widget
                            :widget="$widget"
                            :column-span="is_array($widget['span']) ? $widget['span'] : [1 => $widget['span'] ?? 1]"
                        />
                    @endforeach
                </x-filament::widgets.grid>
            </x-filament::widgets.container>
        </div>
    </x-filament::panel-content>
</x-filament-panels::page>