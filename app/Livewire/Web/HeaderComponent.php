<?php

namespace App\Livewire\Web;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Livewire\Component;

class HeaderComponent extends Component
{
    public string $q = '';

    public function mount(Request $request): void
    {
        $this->q = (string) $request->query('q', '');
    }

    public function logout()
    {
        auth()->logout();
        session()->flush();
        Auth::logout();
        return redirect(route('login'));
    }

    public function updated($key)
    {
        if($key=='q'){
            $term = trim($this->q);
            return $this->redirectRoute('web.search', ['q' => $term], navigate: true);

        }

    }

    public function goSearch(): mixed
    {
        $term = trim($this->q);
        return $this->redirectRoute('web.search', ['q' => $term], navigate: true);
    }

    public function render()
    {
        return view('livewire.web.header-component');
    }
}
