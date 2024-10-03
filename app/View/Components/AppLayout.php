<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    
    /**
     * O layout a ser renderizado.
     *
     * @var string
     */
    public $layout;

    /**
     * Cria uma nova instância do componente.
     *
     * @param string $layout
     */
    public function __construct($layout = 'app')
    {
        $this->layout = $layout;
    }

    /**
     * Obtém a view / conteúdo que representa o componente.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        if ($this->layout === 'app2') {
            return view('layouts.app2');
        }

        return view('layouts.app');
    }
}
