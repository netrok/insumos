<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Field extends Component
{
    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?string $hint = null,
        public string $type = 'text',
    ) {}

    public function render()
    {
        return view('components.field');
    }
}
