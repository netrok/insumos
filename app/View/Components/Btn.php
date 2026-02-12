<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Btn extends Component
{
    public function __construct(
        public string $variant = 'primary', // primary|secondary|outline|soft|danger|ghost
        public string $size = 'md',         // xs|sm|md|lg
        public bool $iconOnly = false,
    ) {}

    public function classes(): string
    {
        $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition
                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white
                 disabled:opacity-60 disabled:cursor-not-allowed';

        $sizes = match ($this->size) {
            'xs' => 'px-2.5 py-1.5 text-xs',
            'sm' => 'px-3 py-2 text-sm',
            'lg' => 'px-5 py-3 text-sm',
            default => 'px-4 py-2 text-sm',
        };

        // ✅ GV + fallback “a prueba de purge”
        // Si tus colores gv-* están compilando, perfecto.
        // Si NO están compilando, estos HEX aseguran contraste (no más texto blanco invisible).
        $variants = match ($this->variant) {
            'primary'   => 'bg-gv-black text-white ring-1 ring-inset ring-gv-line hover:bg-gv-ink hover:ring-gv-gold/40 shadow-gv
                            bg-[#0B0B0C] text-white ring-[#232326] hover:bg-[#141416] hover:ring-[#C8A24A]/40',

            'secondary' => 'bg-white text-gv-black ring-1 ring-inset ring-gray-200 hover:bg-gray-50 focus:ring-gv-gold/40
                            bg-white text-[#0B0B0C] ring-gray-200 hover:bg-gray-50 focus:ring-[#C8A24A]/40',

            'outline'   => 'bg-transparent text-gv-black ring-1 ring-inset ring-gv-gold/70 hover:bg-gv-gold/10 focus:ring-gv-gold/50
                            bg-transparent text-[#0B0B0C] ring-[#C8A24A]/70 hover:bg-[#C8A24A]/10 focus:ring-[#C8A24A]/50',

            'soft'      => 'bg-gv-gold/15 text-gv-black ring-1 ring-inset ring-gv-gold/30 hover:bg-gv-gold/25 focus:ring-gv-gold/50
                            bg-[#C8A24A]/15 text-[#0B0B0C] ring-[#C8A24A]/30 hover:bg-[#C8A24A]/25 focus:ring-[#C8A24A]/50',

            'danger'    => 'bg-red-700 text-white ring-1 ring-inset ring-red-800/30 hover:bg-red-800 focus:ring-red-500 shadow-gv',

            'ghost'     => 'bg-transparent text-gv-black hover:bg-black/5 focus:ring-gv-gold/40
                            bg-transparent text-[#0B0B0C] hover:bg-black/5 focus:ring-[#C8A24A]/40',

            default     => 'bg-gv-black text-white ring-1 ring-inset ring-gv-line hover:bg-gv-ink hover:ring-gv-gold/40 shadow-gv
                            bg-[#0B0B0C] text-white ring-[#232326] hover:bg-[#141416] hover:ring-[#C8A24A]/40',
        };

        $icon = $this->iconOnly ? 'p-2' : '';

        return trim("$base $sizes $variants $icon");
    }

    public function render()
    {
        return view('components.btn');
    }
}
