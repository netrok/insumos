<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Toggle extends Component
{
    public function __construct(
        public string $name,
        public string $label = 'Activo',
        public string $help = 'Controla si se puede usar en el sistema.',
        public bool $checked = true,
    ) {}

    public function render()
    {
        return view('components.toggle');
    }
}
