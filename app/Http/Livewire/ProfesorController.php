<?php

namespace App\Http\Livewire;

use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Profesor;
use Livewire\Component;
use Livewire\WithPagination;

class ProfesorController extends Component
{

    use WithPagination;

    public $nombre, $apellidos, $materia_id, $selected_id, $pageTitle, $componentName, $search;
    private $pagination = 3;
    protected $paginationTheme = 'bootstrap';

    public function mount() 
    {
        $this->pageTitle = 'Profesores';
        $this->componentName = 'Listado';
    }

    public function render()
    {
        if(strlen($this->search) > 0)
            $profesor = Profesor::join('materias as mate', 'mate.id', 'profesors.materia_id')
            ->select('profesors.*', 'mate.nombre as nombre_materia')
            ->where('profesors.nombre', 'LIKE', '%' . $this->search . '%')
            ->orWhere('profesors.apellidos', 'LIKE', '%' . $this->search . '%')
            ->orderBy('nombre', 'ASC')
            ->paginate($this->pagination);
        else 
        $profesor = Profesor::join('materias as mate', 'mate.id', 'profesors.materia_id')
        ->select('profesors.*', 'mate.nombre as nombre_materia')
        ->where('profesors.nombre', 'LIKE', '%' . $this->search . '%')
        ->orWhere('profesors.apellidos', 'LIKE', '%' . $this->search . '%')
        ->orderBy('nombre', 'ASC')
        ->paginate($this->pagination);
        return view('livewire.profesor.profe', ['profesor' => $profesor, 'mate' => Materia::orderBy('id', 'ASC')->get()])
        ->extends('layouts.theme.plantilla')
        ->section('content');
    }


    public function resetUI() 
    {
        $this->nombre = '';
        $this->apellidos = '';
        $this->materia_id = '';
        $this->selected_id = 0;
    }


    public function Store() 
    {
        $rules = [
            'nombre' => 'required',
            'apellidos' => 'required',
            'materia_id' => 'required'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'apellidos.required' => 'Los apellidos son requeridos',
            'materia_id' => 'Selecione una materia'
        ];

        $this->validate($rules, $messages);


        $profesor = Profesor::create([
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'materia_id' => $this->materia_id,
        ]);

        $profesor->save();

        $this->resetUI();
        $this->emit('msgok', 'Agregado');
    }

    public function Edit($id) 
    {
        $profesor = Profesor::find($id, ['id','nombre','apellidos','materia_id']);
        $this->selected_id = $profesor->id;
        $this->nombre = $profesor->nombre;
        $this->apellidos = $profesor->apellidos;
        $this->materia_id = $profesor->materia_id;


        $this->emit('show-modal');
    }

    public function Update() 
    {
        $rules = [
            'nombre' => 'required',
            'apellidos' => 'required',
            'materia_id' => 'required'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'apellidos.required' => 'Los apellidos son requeridos',
            'materia_id' => 'Seleeciona una materia'
        ];

        $this->validate($rules, $messages);

        $profesor = Profesor::find($this->selected_id);

        $profesor->update([
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'materia_id' => $this->materia_id,
        ]);

        $this->resetUI();
        $this->emit('msg-update', 'Actualizado');
    }



    public function Destroy($id)
    {
        Profesor::destroy($id);


        return redirect()->route('profesor.index');

    }
}
