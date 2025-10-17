<?php namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class StockController extends Controller
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('url');
        helper('form');
    }

    public function index()
    {
        // Traer libros
        $builder = $this->db->table('libros');
        $libros = $builder->select('id, titulo, stock')->orderBy('titulo')->get()->getResultArray();

        // Traer columnas y sus valores
        $cols = $this->db->table('stock_columns')->orderBy('created_at','ASC')->get()->getResultArray();

        // Mapear valores por column_id -> libro_id
        $values = [];
        if (!empty($cols)) {
            $colIds = array_column($cols, 'id');
            $rows = $this->db->table('stock_values')->whereIn('column_id', $colIds)->get()->getResultArray();
            foreach ($rows as $r) {
                $values[$r['column_id']][$r['libro_id']] = (int)$r['cantidad'];
            }
        }
        echo view('templates/header');
        echo view('dashboards/stock', [
            'libros' => $libros,
            'cols' => $cols,
            'values' => $values
        ]);
    }

    // Crea una columna y crea valores predeterminados (0) para todos los libros
    public function createColumn()
    {
        $request = Services::request();
        $name = $request->getPost('name') ?? 'Ingreso ' . date('Y-m-d H:i:s');
        $tipo = $request->getPost('tipo') ?? 'ingreso';

        $this->db->transStart();
        $this->db->table('stock_columns')->insert([
            'name' => $name,
            'tipo' => $tipo
        ]);
        $colId = $this->db->insertID();

        // Insertar filas 0 para cada libro
        $libros = $this->db->table('libros')->select('id')->get()->getResultArray();
        $insert = [];
        foreach ($libros as $l) {
            $insert[] = [
                'column_id' => $colId,
                'libro_id' => $l['id'],
                'cantidad' => 0
            ];
        }
        if (!empty($insert)) $this->db->table('stock_values')->insertBatch($insert);
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return $this->response->setJSON(['status'=>'ok','column_id'=>$colId,'name'=>$name,'tipo'=>$tipo]);
        } else {
            return $this->response->setJSON(['status'=>'error','message'=>'No se pudo crear la columna']);
        }
    }

    // Actualiza una celda (sumar/restar) y recalcula stock total en libros.stock si queremos
    public function updateCell()
    {
        $request = Services::request();
        $colId = (int)$request->getPost('column_id');
        $libroId = (int)$request->getPost('libro_id');
        $delta = (int)$request->getPost('delta'); // +1 o -1 o valor absoluto si viene 'set'
        $mode = $request->getPost('mode') ?? 'delta'; // delta o set

        // Leer fila actual
        $svTable = $this->db->table('stock_values');
        $row = $svTable->where(['column_id'=>$colId, 'libro_id'=>$libroId])->get()->getRowArray();

        if (!$row) {
            // Si no existe, crearla (por si)
            $svTable->insert(['column_id'=>$colId,'libro_id'=>$libroId,'cantidad'=> max(0,$delta)]);
            $newVal = max(0,$delta);
        } else {
            if ($mode === 'set') {
                $newVal = max(0, $delta);
            } else {
                $newVal = max(0, $row['cantidad'] + $delta);
            }
            $svTable->where('id', $row['id'])->update(['cantidad'=>$newVal]);
        }

        // Opcional: Recalcular libros.stock como sumatorio de ingresos - egresos
        // Ejemplo sencillo: sumo todas las columnas 'ingreso' y resto 'egreso', o podrías usar otra lógica.
        $sql = "SELECT sc.tipo, SUM(sv.cantidad) AS total
                FROM stock_values sv
                JOIN stock_columns sc ON sc.id = sv.column_id
                WHERE sv.libro_id = ?
                GROUP BY sc.tipo";
        $res = $this->db->query($sql, [$libroId])->getResultArray();
        $ing = 0; $eg = 0;
        foreach($res as $r){
            if ($r['tipo'] === 'ingreso') $ing = (int)$r['total'];
            else $eg = (int)$r['total'];
        }
        $finalStock = $ing - $eg;
        if ($finalStock < 0) $finalStock = 0;

        // Actualizar libros.stock
        $this->db->table('libros')->where('id', $libroId)->update(['stock' => $finalStock]);

        return $this->response->setJSON([
            'status'=>'ok',
            'newCellValue'=>$newVal,
            'newStock'=>$finalStock
        ]);
    }

    // Opcional: borrar columna y sus valores
    public function deleteColumn()
    {
        $id = $this->request->getPost('id');
        if (!$id) return $this->response->setJSON(['status'=>'error','msg'=>'No id provided']);
        $this->db->table('stock_columns')->where('id',$id)->delete();
        // stock_values tiene FK con cascade
        return $this->response->setJSON(['status'=>'ok']);
    }
}
