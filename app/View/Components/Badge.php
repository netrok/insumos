<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public function __construct(
        public string $variant = 'muted', // success|muted|gold|danger|info
        public string $size = 'sm',       // sm|md
    ) {}

    public function classes(): string
    {
        $base = 'inline-flex items-center rounded-lg font-semibold ring-1 ring-inset';
        $sizes = $this->size === 'md' ? 'px-2.5 py-1 text-xs' : 'px-2 py-0.5 text-xs';

        $variants = match ($this->variant) {
            'success' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            'danger'  => 'bg-red-50 text-red-800 ring-red-200',
            'gold'    => 'bg-gv-gold/15 text-gv-black ring-gv-gold/30',
            'info'    => 'bg-slate-50 text-slate-800 ring-slate-200',
            default   => 'bg-slate-50 text-slate-700 ring-slate-200',
        };

        return trim("$base $sizes $variants");
    }

    public function render()
    {
        return view('components.badge');
    }
}
