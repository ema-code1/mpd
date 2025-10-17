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
        // Traer TODOS los libros con stock calculado dinámicamente
        $sql = "SELECT 
                    l.id,
                    l.titulo,
                    l.descripcion,
                    l.autor,
                    l.edicion,
                    l.precio,
                    l.categoria,
                    l.foto1,
                    l.foto2,
                    l.created_at,
                    l.updated_at,
                    GREATEST(COALESCE(SUM(
                        CASE 
                            WHEN sc.tipo = 'ingreso' THEN sv.cantidad 
                            WHEN sc.tipo = 'egreso' THEN -sv.cantidad 
                            ELSE 0 
                        END
                    ), 0), 0) as stock
                FROM libros l
                LEFT JOIN stock_values sv ON l.id = sv.libro_id
                LEFT JOIN stock_columns sc ON sv.column_id = sc.id
                GROUP BY l.id, l.titulo, l.descripcion, l.autor, l.edicion, l.precio, 
                         l.categoria, l.foto1, l.foto2, l.created_at, l.updated_at
                ORDER BY l.titulo"; // ELIMINADO el HAVING stock >= 1

        $libros = $this->db->query($sql)->getResultArray();

        // Traer columnas de ingresos/egresos
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

    // Crear nueva columna (ingreso/egreso)
    public function createColumn()
    {
        $request = Services::request();
        $name = $request->getPost('name') ?? 'Ingreso ' . date('Y-m-d H:i:s');
        $tipo = $request->getPost('tipo') ?? 'ingreso';

        $this->db->transStart();
        
        // Insertar en stock_columns
        $this->db->table('stock_columns')->insert([
            'name' => $name,
            'tipo' => $tipo,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $colId = $this->db->insertID();

        // Insertar filas con valor 0 para TODOS los libros
        $libros = $this->db->table('libros')->select('id')->get()->getResultArray();
        
        $insert = [];
        foreach ($libros as $l) {
            $insert[] = [
                'column_id' => $colId,
                'libro_id' => $l['id'],
                'cantidad' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($insert)) {
            $this->db->table('stock_values')->insertBatch($insert);
        }
        
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return $this->response->setJSON([
                'status' => 'ok', 
                'column_id' => $colId, 
                'name' => $name, 
                'tipo' => $tipo
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo crear la columna']);
        }
    }

    // Agrega este método al controlador
public function deleteColumn()
{
    $id = (int)$this->request->getPost('id');
    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'msg' => 'No id provided']);
    }
    
    $this->db->table('stock_columns')->where('id', $id)->delete();
    // stock_values se elimina automáticamente por CASCADE
    
    return $this->response->setJSON(['status' => 'ok']);
}
}