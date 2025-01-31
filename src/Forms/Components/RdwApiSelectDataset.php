<?php

namespace Jdkweb\Rdw\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Jdkweb\Rdw\Enums\Endpoints;
use Livewire\Livewire;

class RdwApiSelectDataset extends Select
{
    /**
     * Prefix for methods
     *
     * @var string
     */
    public string $prefix = 'endpoints_';

    protected string | array | \Closure | null $dataset = null;

    protected bool | \Closure $shortname = false;

    // protected string | \Closure | null $position = null;

    /**
     * Set default endpoint to select
     *
     * @return void
     */
    protected function setUp(): void
    {
        // All options
        $this->options($this->orderDataSet());

        parent::setUp();
    }

    /**
     * Custom default endpoints to select
     *
     * @param  string|array|\Closure|null  $dataset
     * @return $this
     */
    public function setDefault(string | array | \Closure | null $dataset = null): static
    {
        $this->defaultState = array_keys($this->orderDataSet($dataset));

        return $this;
    }

    /**
     * Set endpoints to use
     *
     * @param  string|array|\Closure|null  $dataset
     * @return $this
     */
    public function dataSet(string | array | \Closure | null $dataset = null): static
    {
        $options = $this->orderDataSet($dataset);

        $this->options($options);

        return $this;
    }

    /**
     * Translate (string, array, object) settings to Endpoint-array
     *
     * @param $dataset
     * @return array
     */
    public function orderDataSet($dataset = null): array
    {
        // check closure
        if(is_object($dataset)) {
            $dataset = $this->evaluate($dataset);
        }

        // check string
        if(is_string($dataset)) {
            $dataset = [$dataset];
        }

        // check dataset is null or empty
        if(is_null($dataset) || empty($dataset) || is_array($dataset) && strtoupper($dataset[0]) == 'ALL') {
            $options = Endpoints::getOptions([],$this->shortname);
            $dataset = Endpoints::names();
        }

        // Accept lower key datasets
        $dataset = array_map(fn($value): string => strtoupper($value), $dataset);

        // Check endpoints
        if(count(array_diff($dataset, Endpoints::names())) == 0) {
            $this->dataset = $dataset;
            $options = Endpoints::getOptions($dataset,$this->shortname);
        }

        return $options;
    }

    public function label(Htmlable|Closure|string|null $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Use the shortname of an endpoint
     *
     * @param  bool|\Closure  $shortname
     * @return $this
     */
    public function shortname(bool | \Closure $shortname = true): static
    {
        // Rewrite dataset is already set
        if($this->shortname != $shortname && !is_null($this->dataset)) {
            $this->shortname = $shortname;
            $this->dataset($this->dataset);
        }

        $this->shortname = $shortname;

        return $this;
    }

//    /**
//     * @TODO Closure not handled
//     *
//     * @param  Closure|string|null  $pos
//     * @return $this
//     */
//    public function position(\Closure|string|null $pos = null): static
//    {
//        $this->position = $pos ?? 'bottom';
//
//        return $this;
//    }

    /**
     * Optional 'Select All' link for selecting all datasets
     *
     * @return RdwApiSelectDataset
     */
    public function showSelectAll(): RdwApiSelectDataset
    {
        if($this->isMultiple()) {
            $this->hintAction(function ($component) {
                return Action::make('selectall')
                    ->label(__('rdw-api::form.selectallLabel'))
                    ->icon('heroicon-m-list-bullet')
                    ->action(function (Set $set) use ($component) {
                        $set($component->statePath, array_keys($component->getEnabledOptions()));
                    });
            });
        }

        return $this;
    }
}
