<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class FilterForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public Collection $universities,
        public Collection $faculties,
        public Collection $programs,
        public Collection $kurikulums,
        public bool $showProdi = true,
        public bool $showFakultas = true,
        public bool $showUniversitas = true,
        public bool $showKurikulum = false,
        public bool $showCpl = false,
        public Collection|array $cplsFilter = []
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filter-form');
    }
}
