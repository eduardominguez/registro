<?php

namespace App\Http\Livewire;

use App\Models\Estudiante;
use App\Models\Materia;
use Livewire\Component;
use Livewire\WithPagination;

class EstudianteController extends Component
{

    public $nombre, $apellido, $carnet, $selected_id, $materia_id, $pageTitle, $componentName, $search;
    private $pagination = 3;
    protected $paginationTheme = 'bootstrap';

    use WithPagination;

    public function mount()
    {
        $this->pageTitle = 'Estudiantes';
        $this->componentName = 'Listado';
    }

    public function render()
    {
        if (strlen($this->search) > 0)
            $estudiantes = Estudiante::join('materias as mat', 'mat.id', 'estudiantes.materia_id')
                ->select('estudiantes.*', 'mat.nombre as namemat')
                ->where('estudiantes.nombre', 'LIKE', '%' . $this->search . '%')
                ->orWhere('estudiantes.apellido', 'LIKE', '%' . $this->search . '%')
                ->orWhere('estudiantes.carnet', 'LIKE', '%' . $this->search . '%')
                ->where('mat.nombre', 'LIKE', '%' . $this->search . '%')
                ->orderBy('id', 'ASC')
                ->paginate($this->pagination);
        else
            $estudiantes = Estudiante::join('materias as mat', 'mat.id', 'estudiantes.materia_id')
                ->select('estudiantes.*', 'mat.nombre as namemat')
                ->where('estudiantes.nombre', 'LIKE', '%' . $this->search . '%')
                ->orWhere('estudiantes.apellido', 'LIKE', '%' . $this->search . '%')
                ->orWhere('estudiantes.carnet', 'LIKE', '%' . $this->search . '%')
                ->where('mat.nombre', 'LIKE', '%' . $this->search . '%')
                ->orderBy('id', 'ASC')
                ->paginate($this->pagination);
        return view('livewire.estudiante.estudiantes', ['estudiantes' => $estudiantes, 'mat' => Materia::orderBy('id', 'asc')->get()])
            ->extends('layouts.theme.plantilla')
            ->section('content');
    }

    public function resetUI()
    {
        $this->nombre = '';
        $this->apellido = '';
        $this->carnet = '';
        $this->selected_id = 0;
    }

    public function Store()
    {
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'carnet' => 'required'

        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'carnet.required' => 'El carnet es requerido'
        ];

        $this->validate($rules, $messages);

        $estudiantes = Estudiante::create([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'carnet' => $this->carnet,
            'materia_id'=> $this->materia_id,
        ]);

        $estudiantes->save();

        $this->resetUI();
        $this->emit('msgok', 'Agregado');
    }

    public function Edit($id)
    {
        $estudiantes = Estudiante::find($id, ['id', 'nombre', 'apellido', 'carnet','materia_id']);
        $this->selected_id = $estudiantes->id;
        $this->nombre = $estudiantes->nombre;
        $this->apellido = $estudiantes->apellido;
        $this->carnet = $estudiantes->carnet;
        $this->materia_id = $estudiantes->materia_id;



        $this->emit('show-modal');
    }

    public function Update()
    {
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'carnet' => 'required'

        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'carnet.required' => 'El carnet es requerido',
            'materia_id.required' =>'La materia es requerida',
        ];

        $this->validate($rules, $messages);

        $estudiantes = Estudiante::find($this->selected_id);
        $estudiantes->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'carnet' => $this->carnet,
            'materia_id' => $this->materia_id,
        ]);

        $this->resetUI();
        $this->emit('msg-update', 'Actualizado');
    }


    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    public function Destroy($id)
    {
        Estudiante::destroy($id);

        return redirect()->route('estudiante.index');
    }
}
